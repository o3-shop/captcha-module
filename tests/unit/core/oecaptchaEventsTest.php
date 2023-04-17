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

class Unit_Core_oecaptchaEventsTest extends CaptchaTestCase
{
    /**
     * Set up the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        //Drop captcha table
        DatabaseProvider::getDB()->execute("DROP TABLE IF EXISTS `oecaptcha`");
    }

    /**
     * Tear down the fixture.
     */
    public function tearDown()
    {
        oeCaptchaEvents::addCaptchaTable();

        parent::tearDown();
    }

    /**
     * Test onActivate event.
     */
    public function testOnActivate()
    {
        oeCaptchaEvents::onActivate();

        $oDbMetaDataHandler = oxNew('oxDbMetaDataHandler');

        //If session is not available, table oecaptcha is where captcha information is stored
        $this->assertTrue($oDbMetaDataHandler->tableExists('oecaptcha'));

    }

    /**
     * Test onActivate event.
     */
    public function testOnDeactivate()
    {
        oeCaptchaEvents::onDeactivate();

        $oDbMetaDataHandler = oxNew('oxDbMetaDataHandler');

        //If session is not available, table oecaptcha is where captcha information is stored
        $this->assertFalse($oDbMetaDataHandler->tableExists('oecaptcha'));

    }

}
