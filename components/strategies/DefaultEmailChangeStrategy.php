<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 2.1
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\components\strategies;

use Da\User\Factory\MailFactory;
use Da\User\Factory\TokenFactory;
use Da\User\Strategy\DefaultEmailChangeStrategy as BaseEmailChangeStrategy;
use Yii;

/**
 * Class DefaultEmailChangeStrategy
 * @package app\components\strategies
 */
class DefaultEmailChangeStrategy extends BaseEmailChangeStrategy
{
    public function run()
    {
        $this->form->getUser()->unconfirmed_email = $this->form->email;
        $this->form->getUser()->confirmed_at = null;

        $token = TokenFactory::makeConfirmNewMailToken($this->form->getUser()->id);

        $mailService = MailFactory::makeReconfirmationMailerService($this->form->getUser(), $token);

        if ($mailService->run()) {
            Yii::$app
                ->session
                ->setFlash('info', Yii::t('app', 'A confirmation message has been sent to your new email address'));

            return $this->form->getUser()->save();
        }

        return false;
    }
}