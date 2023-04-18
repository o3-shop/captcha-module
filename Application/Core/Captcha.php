<?php

/**
 * This file is part of O3-Shop Captcha Module.
 *
 * O3-Shop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

declare(strict_types=1);

namespace O3\SimpleCaptcha\Application\Core;

use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\CaptchaBuilderInterface;
use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class Captcha extends Base
{
    /** @var CaptchaBuilder|CaptchaBuilderInterface */
    protected $builder;

    /**
     * Captcha timeout 60 * 5 = 5 minutes
     *
     * @var int
     */
    protected int $timeout = 300;

    public function __construct(CaptchaBuilderInterface $builder = null)
    {
        parent::__construct();

        $this->builder = $builder ?? (new CaptchaBuilder())->build();
    }

    /**
     * Returns text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->builder->getPhrase();
    }

    /**
     * Returns text hash
     *
     * @param string|null $text User supplied text
     *
     * @return string
     */
    public function getHash(string $text = null): string
    {
        $time = time() + $this->timeout;
        $textHash = $this->getTextHash($text);

        try {
            return Registry::getSession()->isSessionStarted() ?
                $this->getSessionHash($textHash, $time) :
                $this->getHashFromDatabase($textHash, $time);
        } catch (DoctrineException $e) {
            Registry::getUtilsView()->addErrorToDisplay(Registry::getLang()->translateString('CAPTCHA_CANT_SAVE'));
            Registry::getUtils()->redirect(Registry::getConfig()->getShopHomeUrl(), false);
        }

        return '';
    }

    /**
     * @param string $textHash
     * @param int $time
     * @return string
     */
    protected function getSessionHash(string $textHash, int $time): string
    {
        $session = Registry::getSession();
        $hash = UtilsObject::getInstance()->generateUID();
        $hashArray = $session->getVariable('captchaHashes');
        $hashArray[$hash] = [$textHash => $time];
        $session->setVariable('captchaHashes', $hashArray);
        return $hash;
    }

    /**
     * @param string $textHash
     * @param int $time
     * @return string
     * @throws DoctrineException
     */
    public function getHashFromDatabase(string $textHash, int $time): string
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $queryBuilder->insert('oecaptcha')
            ->values(
                [
                    'oxhash' => $queryBuilder->createNamedParameter($textHash),
                    'oxtime' => $queryBuilder->createNamedParameter($time, ParameterType::INTEGER)
                ]
            );
        $queryBuilder->execute();
        return $queryBuilder->getConnection()->lastInsertId();
    }

    /**
     * Returns given string captcha hash
     *
     * @param ?string $text string to hash
     *
     * @return string
     */
    public function getTextHash(string $text = null): string
    {
        $text = $text ?? $this->getText();
        $text = strtolower($text);

        return md5('o3' . $text);
    }

    /**
     * Returns inline image.
     *
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->builder->inline();
    }

    /**
     * Check if captcha is passed.
     *
     * @param bool $displayError
     * @return bool
     */
    public function passCaptcha(bool $displayError = true): bool
    {
        $return = true;

        // spam spider prevention
        $mac = Registry::getRequest()->getRequestEscapedParameter('c_mac');
        $macHash = Registry::getRequest()->getRequestEscapedParameter('c_mach');

        if (!$this->pass($mac, $macHash)) {
            $return = false;
        }

        if (!$return && $displayError) {
            // even if there is no exception, use this as a default display method
            Registry::getUtilsView()->addErrorToDisplay('CAPTCHA_WRONG_VERIFICATION_CODE');
        }

        return $return;
    }

    /**
     * Verifies captcha input vs supplied hash. Returns true on success.
     *
     * @param string $mac User supplied text
     * @param string $macHash Generated hash
     *
     * @return bool
     */
    protected function pass(string $mac, string $macHash): bool
    {
        $pass = null;

        try {
            $time = time();
            $hash = $this->getTextHash($mac);
            $pass = $this->passFromSession($macHash, $hash, $time);

            // if captcha info was NOT stored in session
            if ($pass === null) {
                $pass = $this->passFromDb((int)$macHash, $hash, $time);
            }
        } catch (DoctrineException $e) {
            Registry::getLogger()->error($e->getMessage(), [$this]);
        }

        return (bool)$pass;
    }

    /**
     * Checks for session captcha hash validity
     *
     * @param string $macHash hash key
     * @param string $hash captcha hash
     * @param int $time check time
     *
     * @return ?bool
     */
    protected function passFromSession(string $macHash, string $hash, int $time): ?bool
    {
        $pass = null;
        $session = Registry::getSession();

        if (($hashArray = $session->getVariable('captchaHashes'))) {
            $pass = isset($hashArray[$macHash][$hash]) && $hashArray[$macHash][$hash] >= $time;
            unset($hashArray[$macHash]);
            if (!empty($hashArray)) {
                $session->setVariable('captchaHashes', $hashArray);
            } else {
                $session->deleteVariable('captchaHashes');
            }
        }

        return $pass;
    }

    /**
     * Checks for DB captcha hash validity
     *
     * @param int $macHash hash key
     * @param string $hash captcha hash
     * @param int $time check time
     *
     * @return bool
     * @throws DoctrineException
     */
    protected function passFromDb(int $macHash, string $hash, int $time): bool
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $where = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq(
                'oxid',
                $queryBuilder->createNamedParameter($macHash)
            ),
            $queryBuilder->expr()->eq(
                'oxhash',
                $queryBuilder->createNamedParameter($hash)
            )
        );

        $queryBuilder->select(1)
            ->from('oecaptcha')
            ->where($where);
        $pass = (bool)$queryBuilder->execute()->fetchOne();

        if ($pass) {
            // cleanup
            $queryBuilder->delete('oecaptcha')
                ->where($where);
            $queryBuilder->execute();
        }

        // garbage cleanup
        $queryBuilder->delete('oecaptcha')
            ->where(
                $queryBuilder->expr()->lt(
                    'oxtime',
                    $queryBuilder->createNamedParameter($time, ParameterType::INTEGER)
                )
            );
        $queryBuilder->execute();

        return $pass;
    }
}