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

abstract class CaptchaTestCase extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Fixture set up.
     */
    protected function setUp()
    {
        parent::setUp();

        $query = "CREATE TABLE IF NOT EXISTS `oecaptcha` (
                  `OXID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Captcha id',
                  `OXHASH` char(32) NOT NULL default '' COMMENT 'Hash',
                  `OXTIME` int(11) NOT NULL COMMENT 'Validation time',
                  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
                  PRIMARY KEY (`OXID`),
                  KEY `OXID` (`OXID`,`OXHASH`),
                  KEY `OXTIME` (`OXTIME`)
                ) ENGINE=MEMORY AUTO_INCREMENT=1 COMMENT 'If session is not available, this is where captcha information is stored';
                ";

        DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Fixture set up.
     */
    protected function tearDown()
    {
        $query = "DROP TABLE `oecaptcha`";
        DatabaseProvider::getDb()->execute($query);

        parent::tearDown();
    }

}
