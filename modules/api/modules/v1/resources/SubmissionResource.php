<?php

namespace app\modules\api\modules\v1\resources;

use app\models\FormSubmission;
use yii\helpers\Json;

class SubmissionResource extends FormSubmission
{

    public function fields()
    {
        return [
            'id',
            'hashId',
            'form_id',
            'number',
            'ip',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'status',
            'new',
            'answers',
        ];
    }

    public function extraFields()
    {
        return [
            'files',
            'comments',
        ];
    }

    public function getAnswers()
    {
        $dataModel = $this->form->formData;
        $fields = $dataModel->getFieldsForApi();
        $answers = [];
        $data = $this->getSubmissionData();

        foreach ($fields as $field) {

            $answer = !empty($data[$field['name']]) ? $data[$field['name']] : null;

            if (!empty($answer) && is_string($answer)) {
                if ($field['type'] === 'signature') {
                    // Decode signature
                    $answer = Json::decode($answer);
                }
            }

            $field['answer'] = $answer;

            if (in_array($field['type'], ['radio', 'checkbox', 'selectlist'])) {
                $field['answer_label'] = !empty($data[$field['name'].':labels'])
                    ? $data[$field['name'].':labels']
                    : null;
            }

            $answers[] = $field;
        }

        return $answers;
    }

    /**
     * @inheritdoc
     */
    public function getFiles()
    {
        return $this->hasMany(SubmissionFileResource::class, ['submission_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getComments()
    {
        return $this->hasMany(SubmissionCommentResource::class, ['submission_id' => 'id']);
    }

}