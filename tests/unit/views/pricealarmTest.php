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

use OxidEsales\EshopCommunity\Core\DatabaseProvider;

require_once __DIR__ . '/../CaptchaTestCase.php';

class Unit_pricealarmTest extends CaptchaTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxpricealarm', 'oxartid');

        parent::tearDown();
    }

    /**
     * Test incorrect captcha.
     */
    public function testAddmeIncorectCaptcha()
    {
        $priceAlarm = $this->getProxyClass('oecaptchapricealarm');
        $this->setRequestParameter('c_mac', 'aa');
        $this->setRequestParameter('c_mach', 'bb');

        $priceAlarm->addme();

        $this->assertEquals(2, $priceAlarm->getNonPublicVar('_iPriceAlarmStatus'));

        $query = 'select count(oxid) from oxpricealarm';
        $this->assertEquals(0, DatabaseProvider::getDb()->getOne($query));
    }

    /**
     * Test incorrect email.
     */
    public function testAddmeIncorectEmail()
    {
        $priceAlarm = $this->getProxyClass('oecaptchapricealarm');
        oxTestModules::addFunction('oeCaptcha', 'passCaptcha', '{return true;}');

        $this->setRequestParameter('pa', array('email' => 'ladyGaga'));
        $priceAlarm->addme();

        $this->assertEquals(0, $priceAlarm->getNonPublicVar('_iPriceAlarmStatus'));

        $query = 'select count(oxid) from oxpricealarm';
        $this->assertEquals(0, DatabaseProvider::getDb()->getOne($query));
    }

    /**
     * Test all is well.
     */
    public function testAddmeSavesAndSendsPriceAlarm()
    {
        $priceAlarm = $this->getProxyClass('pricealarm');
        oxTestModules::addFunction('oeCaptcha', 'passCaptcha', '{return true;}');
        oxTestModules::addFunction('oxEmail', 'sendPricealarmNotification', '{return 999;}');

        $this->getSession()->setVariable('usr', 'testUserId');
        $parameters['email'] = 'goodemail@ladyGagaFans.lt';
        $parameters['aid'] = '_testArtId';
        $parameters['price'] = '10';

        $parameters['mano'] = '101';

        $this->setRequestParameter('pa', $parameters);
        $priceAlarm->addme();

        $this->assertEquals(999, $priceAlarm->getNonPublicVar('_iPriceAlarmStatus'));

        $query = 'select * from oxpricealarm';
        $alarm = DatabaseProvider::getDb(oxDB::FETCH_MODE_ASSOC)->getRow($query);

        $this->assertEquals($parameters['email'], $alarm['OXEMAIL']);
        $this->assertEquals($parameters['aid'], $alarm['OXARTID']);
        $this->assertEquals($parameters['price'], $alarm['OXPRICE']);
        $this->assertEquals('testUserId', $alarm['OXUSERID']);
        $this->assertEquals('EUR', $alarm['OXCURRENCY']);
        $this->assertEquals(0, $alarm['OXLANG']);
    }

    /**
     * Test saving active language.
     */
    public function testAddmeSavesCurrentActiveLanguage()
    {
        $priceAlarm = $this->getProxyClass('pricealarm');
        oxTestModules::addFunction('oeCaptcha', 'passCaptcha', '{return true;}');
        oxTestModules::addFunction('oxEmail', 'sendPricealarmNotification', '{return 999;}');

        $this->getSession()->setVariable('usr', 'testUserId');
        $parameters['email'] = 'goodemail@ladyGagaFans.lt';

        oxRegistry::getLang()->setBaseLanguage(1);
        $this->setRequestParameter('pa', $parameters);

        $priceAlarm->addme();

        $query = 'select oxlang from oxpricealarm';
        $language = DatabaseProvider::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($query);

        $this->assertEquals(1, $language);
    }

}
