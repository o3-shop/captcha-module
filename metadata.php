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

use O3\SimpleCaptcha\Application\Component\UserComponentCaptcha;
use O3\SimpleCaptcha\Application\Component\Widget\ArticleDetailsCaptcha;
use O3\SimpleCaptcha\Application\Controller\ArticleDetailsControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\ContactControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\ForgotPasswordControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\InviteControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\NewsletterControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\PriceAlarmControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\RegisterControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\SuggestControllerCaptcha;
use O3\SimpleCaptcha\Application\Controller\UserControllerCaptcha;
use O3\SimpleCaptcha\Application\Core\Events;
use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Component\Widget\ArticleDetails;
use OxidEsales\Eshop\Application\Controller\ArticleDetailsController;
use OxidEsales\Eshop\Application\Controller\ContactController;
use OxidEsales\Eshop\Application\Controller\ForgotPasswordController;
use OxidEsales\Eshop\Application\Controller\InviteController;
use OxidEsales\Eshop\Application\Controller\NewsletterController;
use OxidEsales\Eshop\Application\Controller\PriceAlarmController;
use OxidEsales\Eshop\Application\Controller\RegisterController;
use OxidEsales\Eshop\Application\Controller\SuggestController;
use OxidEsales\Eshop\Application\Controller\UserController;

$sMetadataVersion = '2.1';

$aModule = [
    'id'          => 'o3-captcha',
    'title'       => 'Simple Captcha',
    'description' => 'O3-Shop Simple Captcha Module',
    'thumbnail'   => 'logo.png',
    'version'     => '1.0.0',
    'author'      => 'OXID eSales AG, O3-Shop',
    'url'         => 'https://www.o3-shop.com/',
    'email'       => 'info@o3-shop.com',
    'extend'      => [
        ArticleDetailsController::class           => ArticleDetailsControllerCaptcha::class,
        ContactController::class           => ContactControllerCaptcha::class,
        ForgotPasswordController::class         => ForgotPasswordControllerCaptcha::class,
        InviteController::class            => InviteControllerCaptcha::class,
        NewsletterController::class        => NewsletterControllerCaptcha::class,
        PriceAlarmController::class        => PriceAlarmControllerCaptcha::class,
        RegisterController::class =>        RegisterControllerCaptcha::class,
        SuggestController::class           => SuggestControllerCaptcha::class,
        ArticleDetails::class => ArticleDetailsCaptcha::class,
        UserComponent::class   => UserComponentCaptcha::class,
        UserController::class => UserControllerCaptcha::class
    ],
    'templates'   => [
        'oecaptcha.tpl' => 'o3-shop/captcha/Application/views/tpl/oecaptcha.tpl',
        'oecaptcha_wave.tpl' => 'o3-shop/captcha/Application/views/tpl/oecaptcha_wave.tpl',
    ],
    'blocks'      => [
        [
            'template' => 'form/contact.tpl',
            'block'=>'captcha_form',
            'theme' => 'flow',
            'file'=>'Application/views/blocks/captcha_form_contact_flow.tpl'
        ],
        [
            'template' => 'form/newsletter.tpl',
            'block'=>'captcha_form',
            'theme' => 'flow',
            'file'=>'Application/views/blocks/captcha_form_newsletter_flow.tpl'
        ],
        [
            'template' => 'form/privatesales/invite.tpl',
            'theme' => 'flow',
            'block'=>'captcha_form',
            'file'=>'Application/views/blocks/captcha_form_invite_flow.tpl'
        ],
        [
            'template' => 'form/pricealarm.tpl',
            'block'=>'captcha_form',
            'theme' => 'flow',
            'file'=>'Application/views/blocks/captcha_form_pricealarm_flow.tpl'
        ],
        [
            'template' => 'form/suggest.tpl',
            'block'=>'captcha_form',
            'theme' => 'flow',
            'file'=>'Application/views/blocks/captcha_form_suggest_flow.tpl'
        ],
        [
            'template' => 'form/forgotpwd_email.tpl',
            'block'=>'captcha_form',
            'theme' => 'flow',
            'file'=>'Application/views/blocks/captcha_form_forgotpwd_flow.tpl'
        ],
        [
            'template' => 'form/fieldset/user_billing.tpl',
            'block'=>'captcha_form',
            'theme' => 'flow',
            'file'=>'Application/views/blocks/captcha_form_user_billing_flow.tpl'
        ],

        [
            'template' => 'form/contact.tpl',
            'block'=>'captcha_form',
            'theme' => 'wave',
            'file'=>'Application/views/blocks/captcha_form_contact_wave.tpl'
        ],
        [
            'template' => 'form/newsletter.tpl',
            'block'=>'captcha_form',
            'theme' => 'wave',
            'file'=>'Application/views/blocks/captcha_form_newsletter_wave.tpl'
        ],
        [
            'template' => 'form/privatesales/invite.tpl',
            'theme' => 'wave',
            'block'=>'captcha_form',
            'file'=>'Application/views/blocks/captcha_form_invite_wave.tpl'
        ],
        [
            'template' => 'form/pricealarm.tpl',
            'block'=>'captcha_form',
            'theme' => 'wave',
            'file'=>'Application/views/blocks/captcha_form_pricealarm_wave.tpl'
        ],
        [
            'template' => 'form/suggest.tpl',
            'block'=>'captcha_form',
            'theme' => 'wave',
            'file'=>'Application/views/blocks/captcha_form_suggest_wave.tpl'
        ],
        [
            'template' => 'form/forgotpwd_email.tpl',
            'block'=>'captcha_form',
            'theme' => 'wave',
            'file'=>'Application/views/blocks/captcha_form_forgotpwd_wave.tpl'
        ],
        [
            'template' => 'form/fieldset/user_billing.tpl',
            'block'=>'captcha_form',
            'theme' => 'wave',
            'file'=>'Application/views/blocks/captcha_form_user_billing_wave.tpl'
        ],
    ],
    'events'       => [
        'onActivate'   => Events::class.'::onActivate',
        'onDeactivate' => Events::class.'::onDeactivate'
    ],
];
