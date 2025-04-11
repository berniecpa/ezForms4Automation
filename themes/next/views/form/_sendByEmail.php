<?php

use app\helpers\Html;
use app\components\widgets\ActiveForm;
use app\helpers\IconHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $sendByEmailForm app\models\forms\SendByEmailForm */
/* @var $formModel app\models\Form */
/* @var $formDataModel app\models\FormData */

?>

<?php $form = ActiveForm::begin([
    'id' => 'send-by-email-form',
]) ?>

<?= $form->field($sendByEmailForm, 'to')->textInput([
    'placeholder' => Yii::t('app', 'Enter email addresses separated by commas'),
]) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($sendByEmailForm, 'from_name')->textInput([
                'placeholder' => Yii::t('app', ''),
                'value' => Yii::$app->user->identity->username,
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($sendByEmailForm, 'reply_to')->textInput([
                'placeholder' => Yii::t('app', 'Enter email address'),
                'value' => Yii::$app->user->identity->email,
            ]) ?>
        </div>
    </div>

    <?= $form->field($sendByEmailForm, 'subject')->textInput([
        'placeholder' => Yii::t('app', 'Email subject'),
        'value' => $formModel->name,
    ]) ?>

    <?= $form->field($sendByEmailForm, 'message')->textarea([
        'placeholder' => Yii::t('app', 'Email body'),
        'value' => Yii::t('app', '<p>Hi,</p> <p>Please click on the link below to complete this form:</p> <p>{link}</p> <p>Thanks!</p>', [
            'link' => Html::a(Url::to(['/app/form', 'id' => $formModel->hashId], true), Url::to(['/app/form', 'id' => $formModel->hashId], true))
        ])
    ]) ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(IconHelper::show('send') . Yii::t('app', 'Send Email'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end() ?>

<?php


$script = <<<JS

    $(document).ready(function() {
        /**
         * Show Wysiwyg editor
         */
        if (typeof tinymce !== 'undefined') {
            var tinymceOptions = {
                selector: '#sendbyemailform-message',
                height: 300,
                valid_elements: '*[*]',
                entity_encoding: "raw",
                plugins: 'advlist autolink link image lists charmap print preview hr anchor ' +
                    'searchreplace wordcount visualblocks visualchars code fullscreen fullpage insertdatetime nonbreaking ' +
                    'table directionality paste',
                toolbar: 'undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent | hr link image table | preview fullscreen code',
                convert_urls: false
            };
            if ($('body').attr('data-bs-theme') === 'dark') {
                tinymceOptions.skin = 'oxide-dark';
                tinymceOptions.content_css = 'dark';
            }
            tinymce.init(tinymceOptions);
            $("#sendbyemailform-message").on('input', function() {
                tinymce.get("sendbyemailform-message").setContent($(this).val());
            });
        }
    });

JS;

$this->registerJs($script, $this::POS_END);

