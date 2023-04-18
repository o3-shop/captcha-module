<?php

namespace O3\SimpleCaptcha\Application\Component\Widget {

    use OxidEsales\Eshop\Application\Component\Widget\ArticleDetails;

    class ArticleDetailsCaptcha_parent extends ArticleDetails{}
}

namespace O3\SimpleCaptcha\Application\Component {

    use OxidEsales\Eshop\Application\Component\UserComponent;

    class UserComponentCaptcha_parent extends UserComponent {}
}

namespace O3\SimpleCaptcha\Application\Controller {

    use OxidEsales\Eshop\Application\Controller\ContactController;
    use OxidEsales\Eshop\Application\Controller\ForgotPasswordController;
    use OxidEsales\Eshop\Application\Controller\InviteController;
    use OxidEsales\Eshop\Application\Controller\NewsletterController;
    use OxidEsales\Eshop\Application\Controller\PriceAlarmController;
    use OxidEsales\Eshop\Application\Controller\RegisterController;
    use OxidEsales\Eshop\Application\Controller\SuggestController;
    use OxidEsales\Eshop\Application\Controller\UserController;
    use OxidEsales\EshopCommunity\Application\Controller\ArticleDetailsController;

    class ArticleDetailsControllerCaptcha_parent extends ArticleDetailsController{}

    class ContactControllerCaptcha_parent extends ContactController {}

    class ForgotPasswordControllerCaptcha_parent extends ForgotPasswordController {}

    class InviteControllerCaptcha_parent extends InviteController {}

    class NewsletterControllerCaptcha_parent extends NewsletterController {}

    class PriceAlarmControllerCaptcha_parent extends PriceAlarmController {}

    class RegisterControllerCaptcha_parent extends RegisterController {}

    class SuggestControllerCaptcha_parent extends SuggestController {}

    class UserControllerCaptcha_parent extends UserController {}
}