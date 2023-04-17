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

require_once __DIR__ . '/../CaptchaTestCase.php';

/**
 * Testing details class
 */
class Unit_detailsTest extends CaptchaTestCase
{
    /**
     * Test get Captcha.
     *
     * @return null
     */
    public function testGetCaptcha()
    {
        $details = $this->getProxyClass('oecaptchadetails');
        $this->assertEquals(oxNew('oeCaptcha'), $details->getCaptcha());
    }

    /**
     * Invalid captcha test case.
     */
    public function testAddmeInvalidCaptcha()
    {
        $captcha = $this->getMock('oeCaptcha', array('passCaptcha'));
        $captcha->expects($this->once())->method('passCaptcha')->will($this->returnValue(false));

        $email = $this->getMock('oxEmail', array('sendPricealarmNotification'));
        $email->expects($this->never())->method('sendPricealarmNotification');
        oxTestModules::addModuleObject('oxEmail', $email);

        $details = $this->getMock($this->getProxyClassName('oecaptchadetails'), array('getCaptcha'));
        $details->expects($this->once())->method('getCaptcha')->will($this->returnValue($captcha));

        $details->addme();
        $this->assertSame(2, $details->getNonPublicVar('_iPriceAlarmStatus'));
    }

}


