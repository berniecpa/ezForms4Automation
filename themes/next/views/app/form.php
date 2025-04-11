<?php

use app\components\widgets\ActiveForm;
use app\helpers\Hashids;
use app\helpers\IconHelper;
use app\helpers\UrlHelper;
use app\models\forms\FormPageSettingsForm;
use kartik\file\FileInput;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $formModel app\models\Form */
/* @var $submissionModel array */
/* @var $showTheme boolean */
/* @var $showBox boolean */
/* @var $customJS boolean */
/* @var $page int */
/* @var $reset int */
/* @var $settingsForm FormPageSettingsForm */

// Home URL
$homeUrl = Url::home(true);

// Base URL without schema
$baseUrl = UrlHelper::removeScheme($homeUrl);

$this->title = $formModel->name;

// Add body background to show box design
if ($showBox) {
    $this->registerCss("body { background-color: #EFF3F6; } iframe { border-radius: 0 0 4px 4px; } ");
} else if ($showTheme && !empty($formModel->theme->css)) {
    // Add theme
    $this->registerCss($formModel->theme->css);
}

// Page Settings
if (!empty($formModel->ui->favicon)) {
    $this->params['favicon'] = Url::to(['@uploads/' . $formModel->ui->favicon], true);
}
if (!empty($formModel->ui->meta_title)) {
    $this->params['meta_title'] = Html::encode($formModel->ui->meta_title);
}
if (!empty($formModel->ui->meta_description)) {
    $this->params['meta_description'] = Html::encode($formModel->ui->meta_description);
}
if (!empty($formModel->ui->meta_image)) {
    $this->params['meta_image'] = Url::to(['@uploads/' . $formModel->ui->meta_image], true);
}
$this->params['meta_type'] = 'website';
$this->params['meta_updated_time'] = $formModel->ui->updated_at;

// Add css styles
if (!empty($formModel->ui->styles)) {
    $this->registerCss($formModel->ui->styles);
}

$showOffCanvas = Yii::$app->user->preferences->get('Form.page.settings.show', 0)
    ? ' show' : '';

if (!Yii::$app->user->isGuest
    && Yii::$app->user->can('configureForms', ['model' => $formModel])) {
    $this->registerJsFile('@web/themes/next/assets/js/tabler.min.js', ['position' => View::POS_HEAD]);
    $this->registerJsFile('@web/static_files/js/libs/ace.js', ['position' => View::POS_HEAD]);
}

// Allow / Disallow Edit a Form Submission
$sid = !empty($submissionModel['id'])
    ? Hashids::encode($submissionModel['id'])
    : 0;
?>
    <?php if ($showBox) : ?>
        <div class="container p-5">
            <div class="row">
                <div class="col-12 col-xl-8 offset-xl-2 col-xll-10 col-xll-1 card-container">
                    <div class="form-view">
                        <?php if (Yii::$app->request->get('message')): ?>
                            <div class="alert alert-<?= Html::encode(Yii::$app->request->get('type', 'success')) ?>">
                                <?= Html::encode(Yii::$app->request->get('message')) ?>
                            </div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-header bg-muted" style="padding: 10px 25px">
                                <?php if ($formModel->ui->logo): ?>
                                    <?php if ($formModel->ui->logo_link && $formModel->ui->logo_link_url): ?>
                                        <?= Html::a(Html::img(Url::to(['@uploads/' . $formModel->ui->logo], true)), $formModel->ui->logo_link_url) ?>
                                    <?php else: ?>
                                        <?= Html::img(Url::to(['@uploads/' . $formModel->ui->logo])) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- Brand -->
                                    <h3 class="card-title">
                                        <?= $this->render('@app/themes/next/views/partials/_brand', [
                                            'brandHeight' => '22px',
                                        ]) ?>
                                    </h3>
                                <?php endif; ?>
                            </div>
                            <div class="card-body" style="padding: 0; line-height: 0;">
                                <div id="c<?= $formModel->hashId ?>">
                                    <main class="p-5 d-flex justify-content-center align-items-center flex-wrap">
                                        <div class="spinner-border"></div>
                                    </main>
                                </div>
                                <script type="text/javascript">
                                    (function(d, t) {
                                        var s = d.createElement(t), options = {
                                            'id': '<?= $formModel->hashId ?>',
                                            'sid': '<?= $sid ?>',
                                            'page': <?= $page ?>,
                                            'theme': <?= $showTheme ?>,
                                            'customJS': <?= $customJS ?>,
                                            'reset': <?= $reset ?>,
                                            'container': 'c<?= $formModel->hashId ?>',
                                            'height': '<?= $formModel->formData->height ?>px',
                                            'form': '<?= UrlHelper::removeScheme(Url::to(['/app/embed'], true)) ?>'
                                        };
                                        s.type= 'text/javascript';
                                        s.src = '<?= Url::to('@web/static_files/js/form.widget.js', true) ?>';
                                        s.onload = s.onreadystatechange = function() {
                                            var rs = this.readyState; if (rs) if (rs !== 'complete') if (rs !== 'loaded') return;
                                            try { (new EasyForms()).initialize(options).display() } catch (e) { }
                                        };
                                        var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
                                    })(document, 'script');
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
    <div class="container-fluid">
        <?php if (Yii::$app->request->get('message')): ?>
            <div class="row row-no-gutters">
                <div class="col-12">
                    <div class="alert alert-<?= Html::encode(Yii::$app->request->get('type', 'success')) ?>">
                        <?= Html::encode(Yii::$app->request->get('message')) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row row-no-gutters">
            <div class="col-12">
                <div id="c<?= $formModel->hashId ?>">
                    <main class="p-5 d-flex justify-content-center align-items-center flex-wrap">
                        <div class="spinner-border"></div>
                    </main>
                </div>
                <script type="text/javascript">
                    (function(d, t) {
                        var s = d.createElement(t), options = {
                            'id': '<?= $formModel->hashId ?>',
                            'sid': '<?= $sid ?>',
                            'page': <?= $page ?>,
                            'theme': <?= $showTheme ?>,
                            'customJS': <?= $customJS ?>,
                            'reset': <?= $reset ?>,
                            'container': 'c<?= $formModel->hashId ?>',
                            'height': '<?= $formModel->formData->height ?>px',
                            'form': '<?= UrlHelper::removeScheme(Url::to(['/app/embed'], true)) ?>'
                        };
                        s.type= 'text/javascript';
                        s.src = '<?= Url::to('@web/static_files/js/form.widget.js', true) ?>';
                        s.onload = s.onreadystatechange = function() {
                            var rs = this.readyState; if (rs) if (rs !== 'complete') if (rs !== 'loaded') return;
                            try { (new EasyForms()).initialize(options).display() } catch (e) { }
                        };
                        var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
                    })(document, 'script');
                </script>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php if (!Yii::$app->user->isGuest
    && Yii::$app->user->can('configureForms', ['model' => $formModel])): ?>
    <style>
        .offcanvas-btn {
            left: 410px;
            visibility: visible;
        }

        /* optional junk to toggle the button text */
        .offcanvas-btn span:last-child,
        .offcanvas.show .offcanvas-btn span:first-child {
            display: none;
        }

        .offcanvas.show .offcanvas-btn span:last-child {
            display: inline;
        }

        #editor {
            min-height: 70px;
            resize: vertical;
            overflow: auto;
        }
    </style>
    <div class="offcanvas offcanvas-start<?= $showOffCanvas ?>" id="settings">
        <button class="btn btn-default offcanvas-btn position-absolute mt-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#settings">
        <span title="<?= Yii::t('app', 'Open') ?>">
            <?= IconHelper::show('settings', ['class' => 'm-0']) ?>
        </span>
            <span title="<?= Yii::t('app', 'Close') ?>">
            <?= IconHelper::show('x', ['class' => 'm-0']) ?>
        </span>
        </button>
        <div class="offcanvas-header">
            <h1 class="offcanvas-title">
                <?= Yii::t('app', 'Page Settings') ?>
            </h1>
        </div>
        <div class="offcanvas-body">
            <?php $form = ActiveForm::begin(); ?>

            <?php $removeLogoLink = !empty($settingsForm->logo) ? '<a href="#" class="file-caption-remove link-danger float-end" data-id="'.$formModel->id.'" data-image="logo"><i class="far fa-times"></i></a>' : ''; ?>
            <?= $form->field($settingsForm, 'logo')->widget(FileInput::class, [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'showCancel' => true,
                    'initialCaption' => !empty($settingsForm->logo) ? basename($settingsForm->logo) : '',
                    'layoutTemplates' => [
                        'caption' => "<div class='file-caption form-control {class}' style='height: 40px; padding: 0 10px; line-height: 40px;' tabindex='500'>
                        <input class='file-caption-name' style='width: 65%; border-color: transparent'>
                            {$removeLogoLink}
                        </div>",
                    ],
                ],
            ]) ?>

            <?php $settingsForm->logo_link = empty($settingsForm->logo_link) ? 0 : $settingsForm->logo_link; ?>
            <?= $form->field($settingsForm, 'logo_link')->widget(SwitchInput::class, [
                'pluginOptions' => ['size' => 'mini'],
                'pluginEvents' => [
                    "switchChange.bootstrapSwitch" => "function(event, state) {
                        if (state) {
                            $('.field-formpagesettingsform-logo_link_url').show()
                        } else {
                            $('.field-formpagesettingsform-logo_link_url').hide()
                        }
                    }",
                ],
            ]) ?>

            <?= $form->field($settingsForm, 'logo_link_url')->textInput() ?>

            <?php $removeFaviconLink = !empty($settingsForm->favicon) ? '<a href="#" class="file-caption-remove link-danger float-end" data-id="'.$formModel->id.'" data-image="favicon"><i class="far fa-times"></i></a>' : ''; ?>
            <?= $form->field($settingsForm, 'favicon')->widget(FileInput::class, [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'showCancel' => true,
                    'initialCaption' => !empty($settingsForm->favicon) ? basename($settingsForm->favicon) : '',
                    'layoutTemplates' => [
                        'caption' => "<div class='file-caption form-control {class}' style='height: 40px; padding: 0 10px; line-height: 40px;' tabindex='500'>
                        <input class='file-caption-name' style='width: 65%; border-color: transparent'>
                            {$removeFaviconLink}
                        </div>",
                    ],
                ],
            ]) ?>

            <?= $form->field($settingsForm, 'styles', [
                'template' => "{label}
        <div id='editor' class='form-control'></div>
        {input}{error}",
            ])->hiddenInput() ?>

            <div class="hr-text"><?= Yii::t('app', 'Metadata') ?></div>

            <?= $form->field($settingsForm, 'meta_title')->textInput() ?>

            <?= $form->field($settingsForm, 'meta_description')->textarea([
                'rows' => 2,
            ]) ?>

            <?php $removeMetaImageLink = !empty($settingsForm->meta_image) ? '<a href="#" class="file-caption-remove link-danger float-end" data-id="'.$formModel->id.'" data-image="meta_image"><i class="far fa-times"></i></a>' : ''; ?>
            <?= $form->field($settingsForm, 'meta_image')->widget(FileInput::class, [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'showCancel' => true,
                    'initialCaption' => !empty($settingsForm->meta_image) ? basename($settingsForm->meta_image) : '',
                    'layoutTemplates' => [
                        'caption' => "<div class='file-caption form-control {class}' style='height: 40px; padding: 0 10px; line-height: 40px;' tabindex='500'>
                        <input class='file-caption-name' style='width: 65%; border-color: transparent'>
                            {$removeMetaImageLink}
                        </div>",
                    ],
                ],
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php

$url = Url::to(['/ajax/delete-form-image'], true);
$formPageSettingsUrl = Url::to(['/ajax/form-page-settings'], true);

$script = <<<JS

    $(document).ready(function(){
        // Logo Link URL
        if($("#formpagesettingsform-logo_link").is(":checked") === false) {
            $('.field-formpagesettingsform-logo_link_url').hide();
        } else {
            $('.field-formpagesettingsform-logo_link_url').show();
        }

        // Create & configure the css editor
        editor = ace.edit("editor");
        editor.setTheme("ace/theme/clouds");
        editor.getSession().setMode("ace/mode/css");
        let css = $("#formpagesettingsform-styles");
        // Pass the content of textarea to the editor
        editor.getSession().setValue(css.val());
        // With each change, update the textarea value
        editor.getSession().on('change', function(){
            // Get styles form the editor
            var styles = editor.getSession().getValue();
            // Update the textarea
            css.val(styles);
        });
        
        // Handlers
        $('body').on('click', '.file-caption-remove', function(e){
            e.preventDefault();
            $.post("{$url}", {
                    id: $(this).attr('data-id'),
                    image: $(this).attr('data-image')
                }, function(response) {
                    window.location.replace(window.location.href);
                });
        });
    });

    var settings = document.getElementById('settings')
    settings.addEventListener('shown.bs.offcanvas', function () {
        $.post("{$formPageSettingsUrl}", {
            id: {$formModel->id},
            show: 1
        });
    })
    settings.addEventListener('hidden.bs.offcanvas', function () {
        $.post("{$formPageSettingsUrl}", {
            id: {$formModel->id},
            show: 0
        });
    })

JS;

$this->registerJs($script, $this::POS_END);
?>
<?php endif; ?>
