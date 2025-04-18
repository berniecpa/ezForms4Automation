<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 *
 * Based on Yii2-ActionBar (MIT license)
 * @author Oleg Belostotskiy <olgblst@gmail.com>
 * @link https://github.com/mickgeek/yii2-actionbar
 */

namespace app\components\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use app\components\widgets\ActionBar;

/**
 * Class DeleteMultipleAction
 * Deletes selected rows of a GridView.
 * @package app\components\actions
 */
class DeleteMultipleAction extends Action
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;
    /**
     * @var string the primary key name.
     */
    public $primaryKey = 'id';
    /**
     * @var callable a callback that will be called before deleting selected items.
     *
     * The signature of the callback should be as follows:
     *
     * ~~~
     * function ($action)
     * ~~~
     *
     * where `$action` is the current [[Action]] object.
     */
    public $beforeDeleteCallback;
    /**
     * @var callable a callback that will be called after deleting selected items.
     *
     * The signature of the callback should be as follows:
     *
     * ~~~
     * function ($action)
     * ~~~
     *
     * where `$action` is the current [[Action]] object.
     */
    public $afterDeleteCallback;
    /**
     * @var string|array the URL to be redirected to after deleting.
     */
    public $redirectUrl;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->modelClass)) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
    }

    /**
     * Runs the action.
     *
     * @throws \yii\web\NotFoundHttpException the models is not found.
     */
    public function run()
    {
        if (isset($this->beforeDeleteCallback)) {
            call_user_func($this->beforeDeleteCallback, $this);
        }

        /* @var $modelClass \yii\db\ActiveRecord */
        $modelClass = $this->modelClass;
        $models = $modelClass::findAll([$this->primaryKey => Yii::$app->request->post('ids')]);
        if (empty($models)) {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        } else {
            foreach ($models as $model) {
                $model->delete();
            }
            if (isset($this->afterDeleteCallback)) {
                call_user_func($this->afterDeleteCallback, $this);
            }

            return $this->redirect();
        }
    }

    /**
     * Redirects the browser to the previous page or the specified URL from [[redirectUrl]].
     */
    public function redirect()
    {
        $previous = Url::previous(ActionBar::RETURN_URL_PARAM);

        return !empty($this->redirectUrl) ? $this->controller->redirect($this->redirectUrl)
            : $this->controller->redirect($previous);
    }
}
