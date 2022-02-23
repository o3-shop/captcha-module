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

$sMetadataVersion = '1.1';

$aModule = array(
    'id'          => 'oecaptcha',
    'title'       => array(
        'de' => 'Simple Captcha',
        'en' => 'Simple Captcha',
    ),
    'description' => array(
        'de' => 'OXID eSales Simple Captcha Module',
        'en' => 'OXID eSales Simple Captcha Module',
    ),
    'thumbnail'   => 'logo.png',
    'version'     => '2.0.4',
    'author'      => 'OXID eSales AG',
    'url'         => 'http://www.oxid-esales.com/',
    'email'       => '',
    'extend'      => array('details'           => 'oe/captcha/controllers/oecaptchadetails',
                           'contact'           => 'oe/captcha/controllers/oecaptchacontact',
                           'forgotpwd'         => 'oe/captcha/controllers/oecaptchaforgotpwd',
                           'invite'            => 'oe/captcha/controllers/oecaptchainvite',
                           'newsletter'        => 'oe/captcha/controllers/oecaptchanewsletter',
                           'pricealarm'        => 'oe/captcha/controllers/oecaptchapricealarm',
                           'suggest'           => 'oe/captcha/controllers/oecaptchasuggest',
                           'oxwarticledetails' => 'oe/captcha/application/component/widget/oecaptchawarticledetails',
                           \OxidEsales\Eshop\Application\Component\UserComponent::class   => 'oe/captcha/application/component/oeusercomponent',
                           'register' => 'oe/captcha/controllers/oecaptcharegister'
    ),
    'files'       => array(
        'oecaptcha'       => 'oe/captcha/core/oecaptcha.php',
        'oecaptchaEvents' => 'oe/captcha/core/oecaptchaevents.php',
    ),
    'templates'   => array(
        'oecaptcha.tpl' => 'oe/captcha/application/views/tpl/oecaptcha.tpl',
    ),
    'blocks'      => array(
        array('template' => 'form/contact.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form.tpl'),
        array('template' => 'form/newsletter.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form.tpl'),
        array('template' => 'form/privatesales/invite.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form.tpl'),
        array('template' => 'form/pricealarm.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form.tpl'),
        array('template' => 'form/suggest.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form.tpl'),
        array('template' => 'form/forgotpwd_email.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form_forgotpwd.tpl'),
        array('template' => 'form/fieldset/user_billing.tpl', 'block'=>'captcha_form', 'file'=>'/application/views/blocks/captcha_form.tpl'
        ),
    ),
    'settings'    => [],
    'events'       => array(
        'onActivate'   => 'oecaptchaevents::onActivate',
        'onDeactivate' => 'oecaptchaevents::onDeactivate'
    ),
);
