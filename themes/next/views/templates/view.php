<?php

use app\helpers\IconHelper;
use yii\helpers\Html;
use app\components\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Template */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <?= $this->render('@app/themes/next/views/partials/_breadcrumbs') ?>
            </div>
        </div>
    </div>
</div>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row">
            <div class="col">
                <h2 class="page-title">
                    <?= Yii::t('app', 'Theme Info') ?>
                </h2>
                <p class="text-muted">
                    <?= Html::encode($this->title) ?>
                </p>
            </div>
            <div class="col">
                <div class="float-end d-none d-md-block mt-1">
                    <?php if (Yii::$app->user->can('updateTemplates', ['model' => $model])) : ?>
                        <?= Html::a(IconHelper::show('pencil'), ['update', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Update'),
                            'class' => 'btn btn-icon btn-primary'
                        ]) ?>
                    <?php endif; ?>
                    <?php if (Yii::$app->user->can('deleteTemplates', ['model' => $model])) : ?>
                        <?= Html::a(IconHelper::show('trash'), ['delete', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Delete'),
                            'class' => 'btn btn-icon btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app',
                                    'Are you sure you want to delete this item? All data related to this item will be deleted. This action cannot be undone.'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="d-md-none mb-3">
                    <?php if (Yii::$app->user->can('updateTemplates', ['model' => $model])) : ?>
                        <?= Html::a(IconHelper::show('pencil') . ' ' .
                            Yii::t('app', 'Update'), ['update', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Update'),
                            'class' => 'btn btn-primary mb-1'
                        ]) ?>
                    <?php endif; ?>
                    <?php if (Yii::$app->user->can('deleteTemplates', ['model' => $model])) : ?>
                        <?= Html::a(IconHelper::show('trash') . ' ' .
                            Yii::t('app', 'Delete'),
                            ['delete', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Delete'),
                                'class' => 'btn btn-danger mb-1',
                                'data' => [
                                    'confirm' => Yii::t('app',
                                        'Are you sure you want to delete this item? All data related to this item will be deleted. This action cannot be undone.'),
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col">
                <?= DetailView::widget([
                    'model' => $model,
                    'condensed'=>false,
                    'hover'=>true,
                    'mode'=>DetailView::MODE_VIEW,
                    'enableEditMode'=> false,
                    'hideIfEmpty'=>true,
                    'options' => [
                        'class' => 'kv-view-mode', // Fix hideIfEmpty if enableEditMode is false
                    ],
                    'attributes' => [
                        'id',
                        'name',
                        [
                            'attribute' => 'category',
                            'value' => isset($model->category) ? Html::encode($model->category->name) : null,
                            'label' => Yii::t('app', 'Category'),
                        ],
                        'description',
                        [
                            'attribute' => 'html',
                            'format'=>'raw',
                            'value' => Html::decode($model->html),
                            'label' => Yii::t('app', 'Preview'),
                        ],
                        [
                            'attribute'=>'promoted',
                            'format'=>'raw',
                            'value'=> ($model->promoted === 1) ? '<span class="label label-success"> '.
                                Yii::t('app', 'ON').' </span>' : '<span class="label label-default"> '.
                                Yii::t('app', 'OFF').' </span>',
                            'type'=>DetailView::INPUT_SWITCH,
                            'widgetOptions' => [
                                'pluginOptions' => [
                                    'onText' => Yii::t('app', 'ON'),
                                    'offText' => Yii::t('app', 'OFF'),
                                ]
                            ],
                        ],
                        [
                            'attribute' => 'author',
                            'value' => $model->author->username ?? '',
                            'label' => Yii::t('app', 'Created by'),
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => $model->created,
                            'label' => Yii::t('app', 'Created'),
                        ],
                        [
                            'attribute' => 'lastEditor',
                            'value' => $model->lastEditor->username ?? '',
                            'label' => Yii::t('app', 'Last Editor'),
                        ],
                        [
                            'attribute' => 'updated_at',
                            'value' => $model->updated,
                            'label' => Yii::t('app', 'Updated'),
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php
// Disable form submit
$js = <<<JS
jQuery(document).ready(function(){
    jQuery('form').submit(function() {
        return false;
    });
});
JS;
$this->registerJs($js, $this::POS_END);
