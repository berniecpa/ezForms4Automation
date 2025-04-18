<?php

use app\components\widgets\ActiveForm;
use app\helpers\IconHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var app\components\widgets\ActiveForm $form
 * @var Da\User\Form\SettingsForm $model
 */

$this->title = Yii::t('app', 'Account Settings');
$this->params['breadcrumbs'][] = $this->title;

/** @var \Da\User\Module $module */
$module = Yii::$app->getModule('user');
?>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin(
            [
                'id' => $model->formName(),
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'fieldConfig' => [
                    'horizontalCssClasses' => [
                        'wrapper' => 'col-sm-9',
                    ],
                ],
            ]
        ); ?>

        <?= $form->field($model, 'email') ?>

        <?= $form->field($model, 'username') ?>

        <?= $form->field($model, 'new_password')->passwordInput() ?>

        <hr/>

        <?= $form->field($model, 'current_password')->passwordInput() ?>

        <div class="form-action">
            <?= Html::submitButton(
                IconHelper::show('check') .
                Yii::t('app', 'Save'), ['class' => 'btn btn-primary']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php if ($module->enableTwoFactorAuthentication): ?>
    <div class="modal fade" id="tfmodal" tabindex="-1" role="dialog" aria-labelledby="tfamodalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">
                        <?= Yii::t('app', 'Two Factor Authentication (2FA)') ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="spinner-border text-blue" role="status"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
                        <?= Yii::t('app', 'Close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-status-top bg-primary"></div>
        <div class="card-header">
            <h3 class="card-title"><?= Yii::t('app', 'Two Factor Authentication (2FA)') ?></h3>
        </div>
        <div class="card-body">
            <p>
                <?= Yii::t('app', 'Two factor authentication protects you in case of stolen credentials') ?>.
            </p>
            <div class="text-right">
                <?= Html::a(
                    Yii::t('app', 'Disable two factor authentication'),
                    ['two-factor-disable', 'id' => $model->getUser()->id],
                    [
                        'id' => 'disable_tf_btn',
                        'class' => 'btn btn-warning ' . ($model->getUser()->auth_tf_enabled ? '' : 'd-none'),
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'This will disable two factor authentication. Are you sure?'),
                    ]
                ) ?>
                <?= Html::a(
                    Yii::t('app', 'Enable two factor authentication'),
                    '#tfmodal',
                    [
                        'id' => 'enable_tf_btn',
                        'class' => 'btn btn-primary ' . ($model->getUser()->auth_tf_enabled ? 'd-none' : ''),
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#tfmodal'
                    ]
                ) ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($model->module->allowAccountDelete): ?>
    <div class="card">
        <div class="card-status-top bg-danger"></div>
        <div class="card-header">
            <h3 class="card-title"><?= Yii::t('app', 'Delete account') ?></h3>
        </div>
        <div class="card-body">
            <p>
                <?= Yii::t('app', 'Once you delete your account, there is no going back') ?>.
                <?= Yii::t('app', 'It will be deleted forever') ?>.
                <?= Yii::t('app', 'Please be certain') ?>.
            </p>
            <div class="text-right">
                <?= Html::a(
                    Yii::t('app', 'Delete account'),
                    ['delete'],
                    [
                        'class' => 'btn btn-danger',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'Are you sure? There is no going back'),
                    ]
                ) ?>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if ($module->enableTwoFactorAuthentication): ?>

    <?php
    // This script should be in fact in a module as an external file
    // consider overriding this view and include your very own approach
    $uri = Url::to(['two-factor', 'id' => $model->getUser()->id]);
    $verify = Url::to(['two-factor-enable', 'id' => $model->getUser()->id]);
    $js = <<<JS
$('#tfmodal')
    .on('show.bs.modal', function(){
        if(!$('img#qrCode').length) {
            $(this).find('.modal-body').load('{$uri}');
        } else {
            $('input#tfcode').val('');
        }
    })
    .on('hide.bs.modal	', function(){
        location.reload();
    });

$(document)
    .on('click', '.btn-submit-code', function(e) {
       e.preventDefault();
       var btn = $(this);
       btn.prop('disabled', true);
       
       $.getJSON('{$verify}', {code: $('#tfcode').val()}, function(data){
          btn.prop('disabled', false);
          if(data.success) {
              $('#enable_tf_btn, #disable_tf_btn').toggleClass('hide');
              $('#tfmessage').removeClass('alert-danger').addClass('alert-success').find('p').text(data.message);
              setTimeout(function() { $('#tfmodal').modal('hide'); }, 2000);
          } else {
              $('input#tfcode').val('');
              $('#tfmessage').removeClass('alert-info').addClass('alert-danger').find('p').text(data.message);
          }
       }).fail(function(){ btn.prop('disabled', false); });
    });
JS;

    $this->registerJs($js);
    ?>
<?php endif; ?>
