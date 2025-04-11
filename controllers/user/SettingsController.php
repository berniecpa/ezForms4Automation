<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.9.2
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
namespace app\controllers\user;

use app\components\services\EmailChangeService;
use app\components\services\TwoFactorQrCodeUriGeneratorService;
use app\models\forms\SettingsForm;
use app\models\User;
use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Controller\SettingsController as BaseSettingsController;
use Da\User\Event\UserEvent;
use Da\User\Validator\AjaxRequestModelValidator;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class SettingsController
 * @package app\controllers\user
 */
class SettingsController extends BaseSettingsController
{

    public $layout = "/admin"; // In @app/views/layouts

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'disconnect' => ['post'],
                    'delete' => ['post'],
                    'two-factor-disable' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'profile',
                            'account',
                            'export',
                            'networks',
                            'privacy',
                            'gdpr-delete',
                            'disconnect',
                            'delete',
                            'two-factor',
                            'two-factor-enable',
                            'two-factor-disable',
                            'preferences',
                            'api',
                        ],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Saves new account settings.
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAccount()
    {
        /** @var SettingsForm $form */
        $form = $this->make(SettingsForm::class);
        $event = $this->make(UserEvent::class, [$form->getUser()]);

        $this->make(AjaxRequestModelValidator::class, [$form])->validate();

        if ($form->load(Yii::$app->request->post())) {
            $this->trigger(UserEvent::EVENT_BEFORE_ACCOUNT_UPDATE, $event);

            if ($form->save()) {
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app', 'Your account details have been updated')
                );
                $this->trigger(UserEvent::EVENT_AFTER_ACCOUNT_UPDATE, $event);

                return $this->refresh();
            }
        }

        return $this->render(
            'account',
            [
                'model' => $form,
            ]
        );
    }

    /**
     * User Preferences
     */
    public function actionPreferences()
    {
        // Default values
        if ($post = Yii::$app->request->post()) {
            if (isset($post['action']) && $post['action'] === 'session') {
                $timeout = Yii::$app->request->post('session_timeout_value', 0);
                Yii::$app->user->preferences->set('App.User.SessionTimeout.value', $timeout);
                Yii::$app->user->preferences->save();
                // Show success alert
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app', 'Your preferences have been successfully updated.')
                );
            }
        }
        return $this->render('preferences');
    }

    /**
     * User API
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionApi()
    {
        if (Yii::$app->settings->get("app.restApiKey") !== 1) {
            throw new NotFoundHttpException();
        }

        // Default values
        if ($post = Yii::$app->request->post()) {
            if (isset($post['action']) && ($post['action'] === 'generate' || $post['action'] === 'regenerate')) {
                /** @var User $user */
                $user = Yii::$app->user->identity;
                $user->access_token = Yii::$app->security->generateRandomString(40);
                if ($user->save()) {
                    // Show success alert
                    $message = Yii::t('app', 'Your API Key has been successfully generated.');
                    if ($post['action'] === 'regenerate') {
                        $message = Yii::t('app', 'Your API Key has been successfully regenerated.');
                    }
                    Yii::$app->getSession()->setFlash('success', $message);
                } else {
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app', 'An error occurred while processing your request. Please try again.')
                    );
                }
            } elseif (isset($post['action']) && $post['action'] === 'delete') {
                /** @var User $user */
                $user = Yii::$app->user->identity;
                $user->access_token = null;
                $user->save(false);
                // Show success alert
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app', 'Your API Key has been successfully deleted.')
                );
            }
        }
        return $this->render('api');
    }

    public function actionTwoFactor($id)
    {
        /** @var User $user */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        $uri = $this->make(TwoFactorQrCodeUriGeneratorService::class, [$user])->run();

        return $this->renderAjax('two-factor', ['id' => $id, 'uri' => $uri]);
    }

    public function actionConfirm($id, $code)
    {
        $user = $this->userQuery->whereId($id)->one();

        if ($user === null || MailChangeStrategyInterface::TYPE_INSECURE === $this->module->emailChangeStrategy) {
            throw new NotFoundHttpException();
        }
        $event = $this->make(UserEvent::class, [$user]);

        $this->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION, $event);
        if ($this->make(EmailChangeService::class, [$code, $user])->run()) {
            $this->trigger(UserEvent::EVENT_AFTER_CONFIRMATION, $event);
        }

        return $this->redirect(['account']);
    }
}