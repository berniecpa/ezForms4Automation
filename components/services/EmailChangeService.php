<?php

namespace app\components\services;

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Service\EmailChangeService as BaseEmailChangeService;
use Yii;

class EmailChangeService extends BaseEmailChangeService
{
    public function run()
    {
        /** @var Token $token */
        $token = $this->tokenQuery
            ->whereUserId($this->model->id)
            ->whereCode($this->code)
            ->whereIsTypes([Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL])
            ->one();

        if ($token === null || $token->getIsExpired()) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Your confirmation token is invalid or expired'));

            return false;
        }
        $token->delete();
        if (empty($this->model->unconfirmed_email)) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'An error occurred processing your request'));
        } elseif ($this->userQuery->whereEmail($this->model->unconfirmed_email)->exists() === false) {
            if ($this->getModule()->emailChangeStrategy === MailChangeStrategyInterface::TYPE_SECURE) {
                if ($token->type === Token::TYPE_CONFIRM_NEW_EMAIL) {
                    $this->model->flags |= User::NEW_EMAIL_CONFIRMED;
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(
                            'app',
                            'Awesome, almost there. Now you need to click the confirmation link sent to your old email address.'
                        )
                    );
                } elseif ($token->type === Token::TYPE_CONFIRM_OLD_EMAIL) {
                    $this->model->flags |= User::OLD_EMAIL_CONFIRMED;
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(
                            'app',
                            'Awesome, almost there. Now you need to click the confirmation link sent to your new email address.'
                        )
                    );
                }
            }
            if ((($this->model->flags & User::NEW_EMAIL_CONFIRMED) && ($this->model->flags & User::OLD_EMAIL_CONFIRMED))
                || $this->getModule()->emailChangeStrategy === MailChangeStrategyInterface::TYPE_DEFAULT
            ) {
                $this->model->email = $this->model->unconfirmed_email;
                $this->model->unconfirmed_email = null;
                $this->model->confirmed_at = time();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Your email address has been changed'));
            }

            return $this->model->save(false);
        }

        return false;
    }
}