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

namespace O3\SimpleCaptcha\Application\Core\Setup;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Actions
{
    public function migrateUp(): void
    {
        /** @var MigrationsBuilder $migrationsBuilder */
        $migrationsBuilder = oxNew(MigrationsBuilder::class);
        $migrations = $migrationsBuilder->build();
        $migrations->execute('migrations:migrate', 'o3-captcha');
    }

    /**
     * Regenerate views for changed tables
     */
    public function regenerateViews(): void
    {
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class);
        $oDbMetaDataHandler->updateViews();
    }


    public function migrateDown(): void
    {
        // at the moment the migration wrapper cannot migrate down
        // do not delete the table via other options
    }

    /**
     * clear cache
     */
    public function clearCache(): void
    {
        try {
            $oUtils = Registry::getUtils();
            $oUtils->resetTemplateCache($this->getModuleTemplates());
            $oUtils->resetLanguageCache();
        } catch (ContainerExceptionInterface|NotFoundExceptionInterface|ModuleConfigurationNotFoundException $e) {
            Registry::getLogger()->error($e->getMessage(), [$this]);
            Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
        }
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ModuleConfigurationNotFoundException
     */
    protected function getModuleTemplates(): array
    {
        $container = $this->getDIContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();
        $moduleConfiguration = $shopConfiguration->getModuleConfiguration('o3-captcha');

        return array_unique(
            array_merge(
                $this->getModuleTemplatesFromTemplates($moduleConfiguration),
                $this->getModuleTemplatesFromBlocks($moduleConfiguration)
            )
        );
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    protected function getModuleTemplatesFromTemplates(ModuleConfiguration $moduleConfiguration): array
    {
        /** @var $template Template */
        return array_map(
            function ($template) {
                return $template->getTemplateKey();
            },
            $moduleConfiguration->getTemplates()
        );
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return array
     */
    protected function getModuleTemplatesFromBlocks(ModuleConfiguration $moduleConfiguration): array
    {
        /** @var $templateBlock TemplateBlock */
        return array_map(
            function ($templateBlock) {
                return basename($templateBlock->getShopTemplatePath());
            },
            $moduleConfiguration->getTemplateBlocks()
        );
    }

    /**
     * @return ContainerInterface|null
     */
    protected function getDIContainer(): ?ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}