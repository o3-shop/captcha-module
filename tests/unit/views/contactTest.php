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
class Unit_contactTest extends CaptchaTestCase
{
    /**
     * Test if send mail is not executed if CAPTCHA image is not entered
     * and warning message is displayed
     */
    public function testSaveWithoutCaptcha()
    {
        oxRegistry::getSession()->deleteVariable('Errors');

        $params['oxuser__oxusername'] = 'aaaa@aaa.com';
        $this->setRequestParameter('editval', $params);
        $contact = oxNew('Contact');

        $this->assertFalse($contact->send());

        //checking if warning was added to errors list
        $message = oxRegistry::getLang()->translateString('CAPTCHA_WRONG_VERIFICATION_CODE');
        $errors = oxRegistry::getSession()->getVariable('Errors');
        $error = unserialize($errors['default'][0]);

        $this->assertEquals($message, $error->getOxMessage());
    }

    /**
     * Test if send mail is not executed if user data is not entered
     * and warning message is displayed
     */
    public function testSaveWithoutUserData()
    {
        oxRegistry::getSession()->deleteVariable('Errors');
        oxTestModules::addFunction('oeCaptcha', 'pass', '{return true;}');

        $params['oxuser__oxusername'] = 'aaaa@aaa.com';
        $this->setRequestParameter('editval', $params);
        $contact = oxNew('Contact');

        $this->assertFalse($contact->send());

        //checking if warning was added to errors list
        $message = oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS');
        $errors  = oxRegistry::getSession()->getVariable('Errors');
        $error = unserialize($errors['default'][0]);

        $this->assertEquals($message, $error->getOxMessage());
    }

    /**
     * Test send mail
     */
    public function testSave()
    {
        oxTestModules::addFunction('oeCaptcha', 'pass', '{return true;}');
        oxTestModules::addFunction('oxemail', 'sendContactMail', '{return true;}');

        $params['oxuser__oxusername'] = 'aaaa@aaa.com';
        $params['oxuser__oxfname']    = 'first name';
        $params['oxuser__oxlname']    = 'last name';
        $this->setRequestParameter('editval', $params);
        $this->setRequestParameter('c_subject', 'testSubject');
        $contact = $this->getProxyClass('Contact');
        $contact->send();

        $this->assertEquals(1, $contact->getNonPublicVar('_blContactSendStatus'));
    }

    /**
     * Test getting object for handling CAPTCHA image
     */
    public function testGetCaptcha()
    {
        $contact = oxNew('Contact');
        $this->assertEquals(oxNew('oeCaptcha'), $contact->getCaptcha());
    }

    /**
     * Test case for bug #0002065: Contact-Mail shows MR or MRS instead of localized salutation
     */
    public function testSendForBugtrackEntry0002065()
    {
        $params = array(
            'oxuser__oxusername' => 'info@oxid-esales.com',
            'oxuser__oxfname'    => 'admin',
            'oxuser__oxlname'    => 'admin',
            'oxuser__oxsal'      => 'MR'
        );

        $this->setRequestParameter('editval', $params);
        $this->setRequestParameter('c_message', 'message');
        $this->setRequestParameter('c_subject', 'subject');

        $language    = oxRegistry::getLang();
        $message = $language->translateString('MESSAGE_FROM') . ' ' . $language->translateString('MR') .
                    ' admin admin(info@oxid-esales.com)<br /><br />message';

        $email = $this->getMock('oxemail', array('sendContactMail'));
        $email->expects($this->once())->method('sendContactMail')->with($this->equalTo('info@oxid-esales.com'),
            $this->equalTo('subject'), $this->equalTo($message))->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxemail', $email);

        $captcha = $this->getMock('oeCaptcha', array('passCaptcha'));
        $captcha->expects($this->once())->method('passCaptcha')->will($this->returnValue(true));

        $contact = $this->getMock('oecaptchacontact', array('getCaptcha'));
        $contact->expects($this->once())->method('getCaptcha')->will($this->returnValue($captcha));
        $contact->send();
    }

    /**
     * Testing method send()
     */
    public function testSendEmailNotSend()
    {
        $utils = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $utils->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo('ERROR_MESSAGE_CHECK_EMAIL'));
        oxTestModules::addModuleObject('oxUtilsView', $utils);

        $params = array(
            'oxuser__oxusername' => 'info@oxid-esales.com',
            'oxuser__oxfname'    => 'admin',
            'oxuser__oxlname'    => 'admin',
            'oxuser__oxsal'      => 'MR'
        );

        $this->setRequestParameter('editval', $params);
        $this->setRequestParameter('c_message', 'message');
        $this->setRequestParameter('c_subject', 'subject');

        $email = $this->getMock('oxemail', array('sendContactMail'));
        $email->expects($this->once())->method('sendContactMail')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxemail', $email);

        $captcha = $this->getMock('oeCaptcha', array('passCaptcha'));
        $captcha->expects($this->once())->method('passCaptcha')->will($this->returnValue(true));

        $contact = $this->getMock('oecaptchacontact', array('getCaptcha'));
        $contact->expects($this->once())->method('getCaptcha')->will($this->returnValue($captcha));
        $contact->send();
    }
}
