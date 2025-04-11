<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 2.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
namespace app\components\actions;

use app\events\SubmissionEvent;
use app\models\FormSubmission;
use Yii;
use yii\db\ActiveRecord;
use yii\rest\UpdateAction;
use yii\web\ServerErrorHttpException;

/**
 * Class FormSubmissionUpdateAction
 * @package app\components\actions
 */
class FormSubmissionUpdateAction extends UpdateAction
{
    /**
     * Updates an existing model.
     *
     * @param $id
     * @return ActiveRecord
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /* @var $model FormSubmission */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'] ?? [];
        foreach ($data as $field => $value) {
            // Remove file fields from data
            if (substr($field, 0, 4) == 'file') {
                unset($data[$field]);
            }
        }
        $bodyParams['data'] = $data;
        $model->load($bodyParams, '');

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        Yii::$app->trigger(FormSubmission::EVENT_SUBMISSION_UPDATED, new SubmissionEvent([
            'sender' => $model,
            'form' => $model->form,
            'submission' => $model,
        ]));

        return $model;
    }
}