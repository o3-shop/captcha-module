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

class Unit_inviteTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxinvitations', 'oxuserid');

        parent::tearDown();
    }

    /**
     * Testing method getCaptcha()
     *
     * @return null
     */
    public function testGetCaptcha()
    {
        $invite = $this->getProxyClass('oecaptchainvite');
        $this->assertEquals(oxNew('oeCaptcha'), $invite->getCaptcha());
    }

    /**
     * Testing method send() - no captcha text
     */
    public function testSendWithoutCaptcha()
    {
        $this->setRequestParameter('editval', array('rec_email' => 'testRecEmail@oxid-esales.com',
                                                    'send_name' => 'testSendName',
                                                    'send_email' => 'testSendEmail@oxid-esales.com',
                                                    'send_message' => 'testSendMessage',
                                                    'send_subject' => 'testSendSubject'));
        $this->getConfig()->setConfigParam('blInvitationsEnabled', true);

        $captcha = $this->getMock('oeCaptcha', array('passCaptcha'));
        $captcha->expects($this->once())->method('passCaptcha')->will($this->returnValue(false));

        $invite = $this->getMock('oecaptchainvite', array('getCaptcha', 'getUser'));
        $invite->expects($this->never())->method('getUser');
        $invite->expects($this->once())->method('getCaptcha')->will($this->returnValue($captcha));
        $invite->send();
        $this->assertFalse($invite->getInviteSendStatus());
    }

    /**
     * Testing method send()
     */
    public function testSend()
    {
        $this->setRequestParameter('editval', array('rec_email' => array('testRecEmail@oxid-esales.com'), 'send_name' => 'testSendName', 'send_email' => 'testSendEmail@oxid-esales.com', 'send_message' => 'testSendMessage', 'send_subject' => 'testSendSubject'));
        $this->getConfig()->setConfigParam('blInvitationsEnabled', true);

        $email = $this->getMock('oxEmail', array('sendInviteMail'));
        $email->expects($this->once())->method('sendInviteMail')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxEmail', $email);

        $captcha = $this->getMock('oeCaptcha', array('passCaptcha'));
        $captcha->expects($this->once())->method('passCaptcha')->will($this->returnValue(true));

        $invite = $this->getMock('oecaptchainvite', array('getCaptcha', 'getUser'));
        $invite->expects($this->once())->method('getCaptcha')->will($this->returnValue($captcha));
        $invite->expects($this->any())->method('getUser')->will($this->returnValue(oxNew('oxUser')));
        $invite->send();
        $this->assertTrue($invite->getInviteSendStatus());
    }

    /**
     * Testing method render()
     *
     * @return null
     */
    public function testRender()
    {
        $this->getConfig()->setConfigParam('blInvitationsEnabled', true);

        $invite = $this->getMock('invite', array('getUser'));
        $invite->expects($this->any())->method('getUser')->will($this->returnValue(oxNew('oxUser')));

        $this->assertEquals('page/privatesales/invite.tpl', $invite->render());
    }

    /**
     * Testing method render() - mail was sent status
     */
    public function testRenderMailWasSent()
    {
        $this->getConfig()->setConfigParam('blInvitationsEnabled', true);

        $invite = $this->getProxyClass('invite');
        $invite->setNonPublicVar('_iMailStatus', 1);
        $invite->render();

        $this->assertTrue($invite->getInviteSendStatus());
    }

}
