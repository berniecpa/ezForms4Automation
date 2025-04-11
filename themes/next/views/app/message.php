<?php

use app\models\Form;
use yii\web\View;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $messageType string */
/* @var app\models\Form $formModel */

// PHP options required by embed.js
$options = array(
    "id" => $formModel->id,
    "hashId" => $formModel->hashId,
);
// Pass php options to javascript
$this->registerJs("var options = ".json_encode($options).";", View::POS_BEGIN, 'form-options');

$message = '';
if ($messageType === Form::INACTIVE_MESSAGE_TYPE) {
    $message = !empty($formModel->message) ?
        $formModel->message :
        Html::tag('h3', Yii::t('app', 'This form is no longer accepting new submissions.'), [
            'class' => 'text-center p-5'
        ]);
} elseif ($messageType === Form::UNAUTHORIZED_MESSAGE_TYPE) {
    $message = !empty($formModel->authorized_urls_error_message) ?
        $formModel->authorized_urls_error_message :
        Html::tag('h3', Yii::t('app', 'You are not authorized to access this form.'), [
            'class' => 'text-center p-5'
        ]);
}
?>
<div class="app-message">
    <?= Html::decode($message) ?>
</div>

<?php
// Utilities required for javascript
$this->registerJsFile('@web/static_files/js/form.utils.min.js', ['depends' => \yii\web\JqueryAsset::class]);

$js = <<<JS
    jQuery(document).ready(function(){

        // Send the new height to the parent window
        Utils.postMessage({
            height: $("html").height()
        });

        // Send message to display the restricted form
        Utils.postMessage({
            action: 'view'
        });

    });
JS;

$this->registerJs($js, $this::POS_END, 'message');

?>