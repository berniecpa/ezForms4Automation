<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\controllers;

use app\components\analytics\Analytics;
use app\components\behaviors\Preferences;
use app\events\FormEvent;
use app\helpers\ArrayHelper;
use app\helpers\FormDOM;
use app\helpers\SubmissionHelper;
use app\models\Form;
use app\models\FormChart;
use app\models\FormConfirmation;
use app\models\FormConfirmationRule;
use app\models\FormData;
use app\models\FormEmail;
use app\models\FormRule;
use app\models\forms\FormBuilder;
use app\models\FormSubmission;
use app\models\FormUI;
use app\models\Template;
use app\models\User;
use Exception;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AjaxController
 * @package app\controllers
 */
class AjaxController extends Controller
{

    /**
     * @event FormEvent an event fired when a form is updated.
     */
    const EVENT_FORM_UPDATED = 'app.form.updated';

    /**
     * @event FormEvent an event fired when a form is copied.
     */
    const EVENT_FORM_COPIED = 'app.form.copied';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'count-notifications' => ['post'],
                    'load-notifications' => ['post'],
                    'clean-notifications' => ['post'],
                    'delete-form-image' => ['post'],
                    'form-page-settings' => ['post'],
                    'form-preferences' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['actions' => ['init-form'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['init-template'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['builder-phrases'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['builder-components'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['field-list', 'user-list', 'fields'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['theme-settings'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['grid-view-settings'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['submission-manager-settings'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['count-notifications', 'load-notifications', 'clean-notifications'], 'allow' => true, 'roles' => ['@']],
                    ['actions' => ['create-form'], 'allow' => true, 'roles' => ['createForms']],
                    ['actions' => ['update-form'], 'allow' => true, 'roles' => ['updateForms'], 'roleParams' => function() {
                        return ['model' => Form::findOne(['id' => Yii::$app->request->get('id')])];
                    }],
                    ['actions' => ['copy-form'], 'allow' => true, 'roles' => ['copyForms'], 'roleParams' => function() {
                        return ['model' => Form::findOne(['id' => Yii::$app->request->post('copy-form-id')])];
                    }],
                    ['actions' => ['delete-form-image', 'form-page-settings', 'form-preferences'], 'allow' => true, 'roles' => ['configureForms'], 'roleParams' => function() {
                        return ['model' => Form::findOne(['id' => Yii::$app->request->post('id')])];
                    }],
                    ['actions' => ['create-template'], 'allow' => true, 'roles' => ['createTemplates']],
                    ['actions' => ['update-template'], 'allow' => true, 'roles' => ['updateTemplates'], 'roleParams' => function() {
                        return ['model' => Template::findOne(['id' => Yii::$app->request->get('id')])];
                    }],
                    ['actions' => ['report'], 'allow' => true, 'roles' => ['accessFormReports'], 'roleParams' => function() {
                        return ['model' => Form::findOne(['id' => Yii::$app->request->get('id')])];
                    }],
                    ['actions' => ['analytics', 'stats'], 'allow' => true, 'roles' => ['accessFormStats'], 'roleParams' => function() {
                        return ['model' => Form::findOne(['id' => Yii::$app->request->get('id')])];
                    }],
                ],
            ],
        ];
    }

    /**********************************************
    /* Builder i18n
    /**********************************************/

    /**
     * @return string
     */
    public function actionBuilderPhrases()
    {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            $i18n = [
                "phrases" => [
                    "app.name" => str_replace(' ', '+', Yii::$app->settings->get('app.name')),
                    "form.name" => Yii::t('app', 'Form Name'),
                    "form.description" => Yii::t('app', 'Used for identify the form on administration pages.'),
                    "form.layout" => Yii::t('app', 'Form Layout'),
                    "form.disabled" => Yii::t('app', 'Disable form elements'),
                    "form.sourceCode" => Yii::t('app', 'Source Code preview'),
                    "form.copy" => Yii::t('app', 'Copy'),
                    "form.copied" => Yii::t('app', 'Copied!'),
                    "form.copyToClipboard" => Yii::t('app', 'Copy to clipboard'),
                    "form.save" => Yii::t('app', 'Save Form'),
                    "form.saving" => Yii::t('app', 'Saving...'),
                    "formSteps.title" => Yii::t('app', 'Form Steps'),
                    "formSteps.id" => Yii::t('app', 'ID / Name'),
                    "formSteps.steps" => Yii::t('app', 'Steps'),
                    "formSteps.progressBar" => Yii::t('app', 'Progress Bar'),
                    "formSteps.noTitles" => Yii::t('app', 'No Titles'),
                    "formSteps.noStages" => Yii::t('app', 'No Stages'),
                    "formSteps.noSteps" => Yii::t('app', 'No Form Steps'),
                    "popover.save" => Yii::t('app', 'Save'),
                    "popover.copy" => Yii::t('app', 'Copy'),
                    "popover.delete" => Yii::t('app', 'Delete'),
                    "popover.cancel" => Yii::t('app', 'Cancel'),
                    "popover.more" => Yii::t('app', 'More'),
                    "popover.show" => Yii::t('app', 'Show'),
                    "popover.choice" => Yii::t('app', 'Choice'),
                    "popover.value" => Yii::t('app', 'Value'),
                    "popover.values" => Yii::t('app', 'Values'),
                    "popover.bulkEditor" => Yii::t('app', 'Bulk Editor'),
                    "popover.image" => Yii::t('app', 'Image'),
                    "popover.images" => Yii::t('app', 'Images'),
                    "popover.attribute" => Yii::t('app', 'Attribute'),
                    "popover.copyID" => Yii::t('app', 'Copy ID'),
                    "tab.fields" => Yii::t('app', 'Fields'),
                    "tab.settings" => Yii::t('app', 'Settings'),
                    "tab.code" => Yii::t('app', 'Code'),
                    "alert.warning" => Yii::t('app', 'Warning!'),
                    "alert.errorSavingData" => Yii::t('app', 'There was a problem saving data. Please retry later'),
                    "alert.unsavedChanges" => Yii::t(
                        'app',
                        'YOU HAVE UNSAVED CHANGES! ALL CHANGES IN THE FORM WILL BE LOST!'
                    ),
                    "alert.confirmToDeleteField" => Yii::t(
                        'app',
                        "Are you sure you want to delete this field? If you do, any data associated with this field will be deleted too. If this form has at least one form submission, you should export your data first."
                    ),
                    "alert.unsplashConnectionError" => Yii::t('app', 'Error connecting to Unsplash. Please try again after 5 minutes.'),
                    "widget.button" => Yii::t('app', 'Submit'),
                    "widget.checkbox" => Yii::t('app', 'Checkboxes'),
                    "widget.date" => Yii::t('app', 'Date Field'),
                    "widget.email" => Yii::t('app', 'Email Field'),
                    "widget.file" => Yii::t('app', 'File Upload'),
                    "widget.heading" => Yii::t('app', 'Heading'),
                    "widget.hidden" => Yii::t('app', 'Hidden Field'),
                    "widget.number" => Yii::t('app', 'Number Field'),
                    "widget.pageBreak" => Yii::t('app', 'Page Break'),
                    "widget.paragraph" => Yii::t('app', 'Paragraph'),
                    "widget.radio" => Yii::t('app', 'Radio Buttons'),
                    "widget.recaptcha" => Yii::t('app', 'reCaptcha'),
                    "widget.selectList" => Yii::t('app', 'Select List'),
                    "widget.snippet" => Yii::t('app', 'Snippet'),
                    "widget.text" => Yii::t('app', 'Text Field'),
                    "widget.textArea" => Yii::t('app', 'Text Area'),
                    "widget.spacer" => Yii::t('app', 'Spacer'),
                    "widget.signature" => Yii::t('app', 'Signature'),
                    "widget.matrix" => Yii::t('app', 'Matrix Field'),
                    "widget.nps" => Yii::t('app', 'Net Promoter Score'),
                    "heading.title" => Yii::t('app', 'Heading'),
                    "paragraph.title" => Yii::t('app', 'Paragraph'),
                    "text.title" => Yii::t('app', 'Text'),
                    "number.title" => Yii::t('app', 'Number'),
                    "date.title" => Yii::t('app', 'Date'),
                    "email.title" => Yii::t('app', 'Email'),
                    "textarea.title" => Yii::t('app', 'Text Area'),
                    "signature.title" => Yii::t('app', 'Signature'),
                    "matrix.title" => Yii::t('app', 'Matrix'),
                    "checkbox.title" => Yii::t('app', 'Checkbox'),
                    "radio.title" => Yii::t('app', 'Radio'),
                    "selectlist.title" => Yii::t('app', 'Select List'),
                    "hidden.title" => Yii::t('app', 'Hidden'),
                    "file.title" => Yii::t('app', 'File'),
                    "snippet.title" => Yii::t('app', 'Snippet'),
                    "recaptcha.title" => Yii::t('app', 'reCAPTCHA'),
                    "pagebreak.title" => Yii::t('app', 'Page Break'),
                    "spacer.title" => Yii::t('app', 'Spacer'),
                    "nps.title" => Yii::t('app', 'Net Promoter Score'),
                    "button.title" => Yii::t('app', 'Button'),
                    "pagebreak.prev" => Yii::t('app', 'Previous'),
                    "pagebreak.next" => Yii::t('app', 'Next'),
                    "component.id" => Yii::t('app', 'ID / Name'),
                    "component.text" => Yii::t('app', 'Text'),
                    "component.inputType" => Yii::t('app', 'Input Type'),
                    "component.type" => Yii::t('app', 'Type'),
                    "component.size" => Yii::t('app', 'Size'),
                    "component.label" => Yii::t('app', 'Label'),
                    "component.question" => Yii::t('app', 'Question'),
                    "component.labels" => Yii::t('app', 'Labels'),
                    "component.enterQuestion" => Yii::t('app', 'Enter Question'),
                    "component.placeholder" => Yii::t('app', 'Placeholder'),
                    "component.required" => Yii::t('app', 'Required'),
                    "component.predefinedValue" => Yii::t('app', 'Predefined Value'),
                    "component.helpText" => Yii::t('app', 'Help Text'),
                    "component.helpTextPlacement" => Yii::t('app', 'Help Text Placement'),
                    "component.prepend" => Yii::t('app', 'Prepend'),
                    "component.append" => Yii::t('app', 'Append'),
                    "component.labelPlacement" => Yii::t('app', 'Label Placement'),
                    "component.fieldSize" => Yii::t('app', 'Field Size'),
                    "component.groupName" => Yii::t('app', 'Group Name'),
                    "component.checkboxes" => Yii::t('app', 'Checkboxes'),
                    "component.radios" => Yii::t('app', 'Radios'),
                    "component.options" => Yii::t('app', 'Options'),
                    "component.accept" => Yii::t('app', 'Accept'),
                    "component.pattern" => Yii::t('app', 'Pattern'),
                    "component.integerPattern" => Yii::t('app', 'Integer Pattern'),
                    "component.numberPattern" => Yii::t('app', 'Number Pattern'),
                    "component.prev" => Yii::t('app', 'Text of Previous Button'),
                    "component.next" => Yii::t('app', 'Text of Next Button'),
                    "component.buttonText" => Yii::t('app', 'Button Text'),
                    "component.src" => Yii::t('app', 'Image Source'),
                    "component.inline" => Yii::t('app', 'Inline'),
                    "component.asButton" => Yii::t('app', 'As Button'),
                    "component.unique" => Yii::t('app', 'Unique'),
                    "component.readOnly" => Yii::t('app', 'Read Only'),
                    "component.integerOnly" => Yii::t('app', 'Integer Only'),
                    "component.minlength" => Yii::t('app', 'Min Length'),
                    "component.maxlength" => Yii::t('app', 'Max Length'),
                    "component.min" => Yii::t('app', 'Minimum'),
                    "component.max" => Yii::t('app', 'Maximum'),
                    "component.step" => Yii::t('app', 'Step'),
                    "component.minNumber" => Yii::t('app', 'Min number'),
                    "component.maxNumber" => Yii::t('app', 'Max number'),
                    "component.stepNumber" => Yii::t('app', 'Step number'),
                    "component.minDate" => Yii::t('app', 'Min date'),
                    "component.maxDate" => Yii::t('app', 'Max date'),
                    "component.minSize" => Yii::t('app', 'Min Size By File'),
                    "component.maxSize" => Yii::t('app', 'Max Size By File'),
                    "component.minFiles" => Yii::t('app', 'Min Files'),
                    "component.maxFiles" => Yii::t('app', 'Max Files'),
                    "component.htmlCode" => Yii::t('app', 'HTML Code'),
                    "component.theme" => Yii::t('app', 'Theme'),
                    "component.belowInputs" => Yii::t('app', 'Below inputs'),
                    "component.aboveInputs" => Yii::t('app', 'Above inputs'),                    "component.checkDNS" => Yii::t('app', 'Check DNS'),
                    "component.multiple" => Yii::t('app', 'Multiple'),
                    "component.disabled" => Yii::t('app', 'Disabled'),
                    "component.cssClass" => Yii::t('app', 'CSS Class'),
                    "component.inputGroupClass" => Yii::t('app', 'Input Group CSS Class'),
                    "component.labelClass" => Yii::t('app', 'Label CSS Class'),
                    "component.containerClass" => Yii::t('app', 'Container CSS Class'),
                    "component.tableClass" => Yii::t('app', 'Table CSS Class'),
                    "component.questionClass" => Yii::t('app', 'Question CSS Class'),
                    "component.buttonClass" => Yii::t('app', 'Button CSS Class'),
                    "component.alias" => Yii::t('app', 'Alias'),
                    "component.clear" => Yii::t('app', 'Clear'),
                    "component.clearText" => Yii::t('app', 'Clear Button Text'),
                    "component.undo" => Yii::t('app', 'Undo'),
                    "component.undoText" => Yii::t('app', 'Undo Button Text'),
                    "component.color" => Yii::t('app', 'Color'),
                    "component.width" => Yii::t('app', 'Width'),
                    "component.height" => Yii::t('app', 'Height'),
                    "component.questions" => Yii::t('app', 'Questions'),
                    "component.answers" => Yii::t('app', 'Answers'),
                    "component.customAttributes" => Yii::t('app', 'Custom Attributes'),
                    "style.global" => Yii::t('app', 'Global'),
                    "style.background" => Yii::t('app', 'Background'),
                    "style.text" => Yii::t('app', 'Text'),
                    "style.title" => Yii::t('app', 'Title'),
                    "style.spacing" => Yii::t('app', 'Spacing'),
                    "style.form" => Yii::t('app', 'Form'),
                    "style.border" => Yii::t('app', 'Border'),
                    "style.extra" => Yii::t('app', 'Extra'),
                    "style.formGroup" => Yii::t('app', 'Form Group'),
                    "style.formControl" => Yii::t('app', 'Form Control'),
                    "style.size" => Yii::t('app', 'Size'),
                    "style.focusEffect" => Yii::t('app', 'Focus Effect'),
                    "style.button" => Yii::t('app', 'Button'),
                    "style.hoverEffect" => Yii::t('app', 'Hover Effect'),
                    "style.label" => Yii::t('app', 'Label'),
                    "style.placeholder" => Yii::t('app', 'Placeholder'),
                    "style.heading" => Yii::t('app', 'Heading'),
                    "style.paragraph" => Yii::t('app', 'Paragraph'),
                    "style.helpText" => Yii::t('app', 'Help Text'),
                    "style.link" => Yii::t('app', 'Link'),
                    "style.formSteps" => Yii::t('app', 'Form Steps'),
                    "style.others" => Yii::t('app', 'Others'),
                    "style.currentStep" => Yii::t('app', 'Current Step'),
                    "style.succeedStep" => Yii::t('app', 'Succeed Step'),
                    "style.previousButton" => Yii::t('app', '"Previous" Button'),
                    "style.previousButtonHoverEffect" => Yii::t('app', '"Previous" Button Hover Effect'),
                    "style.nextButton" => Yii::t('app', '"Next" Button'),
                    "style.nextButtonHoverEffect" => Yii::t('app', '"Next" Button Hover Effect'),
                    "style.formAlerts" => Yii::t('app', 'Form Alerts'),
                    "style.successMessage" => Yii::t('app', 'Success Message'),
                    "style.errorMessage" => Yii::t('app', 'Error Message'),
                    "style.fieldValidation" => Yii::t('app', 'Field Validation'),
                    "style.asteriskSymbol" => Yii::t('app', 'Asterisk Symbol'),
                    "style.textMessage" => Yii::t('app', 'Text Message'),
                    "style.field" => Yii::t('app', 'Field'),
                    "style.otherComponents" => Yii::t('app', 'Other Components'),
                    "style.signaturePad" => Yii::t('app', 'Signature Pad'),
                    "style.checkbox" => Yii::t('app', 'Checkbox'),
                    "style.input" => Yii::t('app', 'Input'),
                    "style.customControl" => Yii::t('app', 'Custom Control'),
                    "style.checked" => Yii::t('app', 'Checked'),
                    "style.mark" => Yii::t('app', 'Mark'),
                    "style.radioButton" => Yii::t('app', 'Radio Button'),
                    "style.gradients" => Yii::t('app', 'Gradients'),
                    "style.patterns" => Yii::t('app', 'Patterns'),
                    "style.none" => Yii::t('app', 'None'),
                    "style.type" => Yii::t('app', 'Type'),
                    "style.radial" => Yii::t('app', 'Radial'),
                    "style.linear" => Yii::t('app', 'Linear'),
                    "style.repeatingRadial" => Yii::t('app', 'Repeating Radial'),
                    "style.repeatingLinear" => Yii::t('app', 'Repeating Linear'),
                    "style.direction" => Yii::t('app', 'Direction'),
                    "style.top" => Yii::t('app', 'Top'),
                    "style.right" => Yii::t('app', 'Right'),
                    "style.center" => Yii::t('app', 'Center'),
                    "style.bottom" => Yii::t('app', 'Bottom'),
                    "style.left" => Yii::t('app', 'Left'),
                    "style.backgroundColor" => Yii::t('app', 'Background Color'),
                    "style.selectColor" => Yii::t('app', 'Select color...'),
                    "style.color" => Yii::t('app', 'Color'),
                    "style.backgroundImageGradient" => Yii::t('app', 'Background Image / Gradient'),
                    "style.bgSize" => Yii::t('app', 'BG Size'),
                    "style.bgRepeat" => Yii::t('app', 'BG Repeat'),
                    "style.backgroundPosition" => Yii::t('app', 'Background Position'),
                    "style.borderStyle" => Yii::t('app', 'Border Style'),
                    "style.borderWidth" => Yii::t('app', 'Border Width'),
                    "style.borderColor" => Yii::t('app', 'Border Color'),
                    "style.borderRadius" => Yii::t('app', 'Border Radius'),
                    "style.boxShadow" => Yii::t('app', 'Box Shadow'),
                    "style.textShadow" => Yii::t('app', 'Text Shadow'),
                    "style.basic" => Yii::t('app', 'Basic'),
                    "style.hard" => Yii::t('app', 'Hard'),
                    "style.double" => Yii::t('app', 'Double'),
                    "style.downAndDistant" => Yii::t('app', 'Down and Distant'),
                    "style.closeAndHeavy" => Yii::t('app', 'Close and Heavy'),
                    "style.glowing" => Yii::t('app', 'Glowing'),
                    "style.superhero" => Yii::t('app', 'Superhero'),
                    "style.multipleLightSources" => Yii::t('app', 'Multiple Light Sources'),
                    "style.softEmboss" => Yii::t('app', 'Soft Emboss'),
                    "style.margin" => Yii::t('app', 'Margin'),
                    "style.padding" => Yii::t('app', 'Padding'),
                    "style.width" => Yii::t('app', 'Width'),
                    "style.height" => Yii::t('app', 'Height'),
                    "style.float" => Yii::t('app', 'Float'),
                    "style.textAlignment" => Yii::t('app', 'Text Alignment'),
                    "style.fontFamily" => Yii::t('app', 'Font Family'),
                    "style.selectFont" => Yii::t('app', 'Select font...'),
                    "style.fontSize" => Yii::t('app', 'Font Size'),
                    "style.fontWeight" => Yii::t('app', 'Font Weight'),
                    "style.textTransform" => Yii::t('app', 'Text Transform'),
                    "style.textDecoration" => Yii::t('app', 'Text Decoration'),
                    "style.lineHeight" => Yii::t('app', 'Line Height'),
                    "style.letterSpacing" => Yii::t('app', 'Letter Spacing'),
                    "style.transition" => Yii::t('app', 'Transition'),
                    "style.display" => Yii::t('app', 'Display'),
                    "style.choose" => Yii::t('app', 'Choose'),
                    "style.cancel" => Yii::t('app', 'Cancel'),
                    "style.inputGroup" => Yii::t('app', 'Input Group'),
                    "style.prependAppend" => Yii::t('app', 'Prepend & Append'),
                    "style.append" => Yii::t('app', 'Append'),
                    "style.progressBar" => Yii::t('app', 'Progress Bar'),
                    "style.progressBarContainer" => Yii::t('app', 'Progress Bar Container'),
                    "style.images" => Yii::t('app', 'Images'),
                    "style.searchImages" => Yii::t('app', 'Search Images'),
                    "style.latest" => Yii::t('app', 'Latest'),
                    "style.oldest" => Yii::t('app', 'Oldest'),
                    "style.popular" => Yii::t('app', 'Popular'),
                    "style.noResultsFound" => Yii::t('app', 'No Results Found'),
                    "style.byUnsplash" => Yii::t('app', 'By {unsplash}', [
                        'unsplash' => Html::a(
                            'Unsplash',
                            'https://unsplash.com/?utm_source='.str_replace(' ', '+', urldecode(Yii::$app->settings->get('app.name'))).'&utm_medium=referral',
                            ['target' => '_blank']),
                    ]),
                    "style.photoByUnsplash" => Yii::t('app', 'By {photographer} on {unsplash}', [
                        'photographer' => '{photographer}',
                        'unsplash' => Html::a(
                            'Unsplash',
                            'https://unsplash.com/?utm_source='.str_replace(' ', '+', urldecode(Yii::$app->settings->get('app.name'))).'&utm_medium=referral',
                            ['target' => '_blank']),
                    ]),
                ]
            ];

            return $i18n;
        }

        return '';
    }

    /**
     * @return string
     */
    public function actionBuilderComponents()
    {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            $i18n = [
                'heading' => Yii::t('app', 'Heading'),
                'paragraphText' => Yii::t('app', 'You can edit this paragraph by clicking here.'),
                'npsQuestion' => Yii::t('app', 'On a scale from 0-10, how likely are you to recommend our company to a friend or colleague?'),
                'notAtAllLikely' => Yii::t('app', 'Not at all likely'),
                'neutral' => Yii::t('app', 'Neutral'),
                'extremelyLikely' => Yii::t('app', 'Extremely likely'),
                'numberField' => Yii::t('app', 'Number Field'),
                'dateField' => Yii::t('app', 'Date Field'),
                'emailField' => Yii::t('app', 'Email Field'),
                'textArea' => Yii::t('app', 'Text Area'),
                'textField' => Yii::t('app', 'Text Field'),
                'checkAllThatApply' => Yii::t('app', 'Check All That Apply'),
                'selectAChoice' => Yii::t('app', 'Select a Choice'),
                'firstChoice' => Yii::t('app', 'First Choice'),
                'secondChoice' => Yii::t('app', 'Second Choice'),
                'thirdChoice' => Yii::t('app', 'Third Choice'),
                'signature' => Yii::t('app', 'Signature'),
                'answerTheFollowingQuestions' => Yii::t('app', 'Answer the following questions'),
                'firstQuestion' => Yii::t('app', 'First Question'),
                'secondQuestion' => Yii::t('app', 'Second Question'),
                'thirdQuestion' => Yii::t('app', 'Third Question'),
                'answerA' => Yii::t('app', 'Answer A'),
                'answerB' => Yii::t('app', 'Answer B'),
                'answerC' => Yii::t('app', 'Answer C'),
                'clear' => Yii::t('app', 'Clear'),
                'undo' => Yii::t('app', 'Undo'),
                'color' => Yii::t('app', 'Color'),
                'attachAFile' => Yii::t('app', 'Attach a File'),
                'belowInputs' => Yii::t('app', 'Below inputs'),
                'aboveInputs' => Yii::t('app', 'Above inputs'),
                'replaceThisCode' => Yii::t('app', 'Replace this {startTag}code{endTag} with your html snippet.', [
                    'startTag' => '<code>',
                    'endTag' => '</code>',
                ]),
                'buttonText' => Yii::t('app', 'Submit'),
                'previous' => Yii::t('app', 'Previous'),
                'next' => Yii::t('app', 'Next'),
            ];

            $json = <<<EOD
[
    {
        "name": "heading",
        "title": "heading.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "heading"
            },
            "text": {
                "label": "component.text",
                "type": "input",
                "value": "{$i18n['heading']}"
            },
            "type": {
                "label": "component.type",
                "type": "select",
                "value": [
                    {
                        "value": "h1",
                        "label": "H1",
                        "selected": false
                    },
                    {
                        "value": "h2",
                        "label": "H2",
                        "selected": true
                    },
                    {
                        "value": "h3",
                        "label": "H3",
                        "selected": false
                    },
                    {
                        "value": "h4",
                        "label": "H4",
                        "selected": false
                    },
                    {
                        "value": "h5",
                        "label": "H5",
                        "selected": false
                    },
                    {
                        "value": "h6",
                        "label": "H6",
                        "selected": false
                    }
                ]
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            }
        }
    },
    {
        "name": "paragraph",
        "title": "paragraph.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "paragraph"
            },
            "text": {
                "label": "component.text",
                "type": "textarea",
                "value": "{$i18n['paragraphText']}"
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            }
        }
    },
    {
        "name": "text",
        "title": "text.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "text"
            },
            "inputType": {
                "label": "component.inputType",
                "type": "select",
                "value": [
                    {
                        "value": "text",
                        "label": "Text",
                        "selected": true
                    },
                    {
                        "value": "tel",
                        "label": "Tel",
                        "selected": false
                    },
                    {
                        "value": "url",
                        "label": "URL",
                        "selected": false
                    },
                    {
                        "value": "color",
                        "label": "Color",
                        "selected": false
                    },
                    {
                        "value": "password",
                        "label": "Password",
                        "selected": false
                    }
                ]
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['textField']}"
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "predefinedValue": {
                "label": "component.predefinedValue",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "minlength": {
                "label": "component.minlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "maxlength": {
                "label": "component.maxlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "pattern": {
                "label": "component.pattern",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group input-group-flat",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "unique": {
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "number",
        "title": "number.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "number"
            },
            "inputType": {
                "label": "component.inputType",
                "type": "select",
                "value": [
                    {
                        "value": "number",
                        "label": "Number",
                        "selected": true
                    },
                    {
                        "value": "range",
                        "label": "Range",
                        "selected": false
                    }
                ]
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['numberField']}"
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "predefinedValue": {
                "label": "component.predefinedValue",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "min": {
                "label": "component.minNumber",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "max": {
                "label": "component.maxNumber",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "step": {
                "label": "component.stepNumber",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "integerPattern": {
                "label": "component.integerPattern",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "numberPattern": {
                "label": "component.numberPattern",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group input-group-flat",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "unique": {
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "integerOnly": {
                "label": "component.integerOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "date",
        "title": "date.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "date"
            },
            "inputType": {
                "label": "component.inputType",
                "type": "select",
                "value": [
                    {
                        "value": "date",
                        "label": "Date",
                        "selected": true
                    },
                    {
                        "value": "datetime-local",
                        "label": "DateTime-Local",
                        "selected": false
                    },
                    {
                        "value": "time",
                        "label": "Time",
                        "selected": false
                    },
                    {
                        "value": "month",
                        "label": "Month",
                        "selected": false
                    },
                    {
                        "value": "week",
                        "label": "Week",
                        "selected": false
                    }
                ]
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['dateField']}"
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "predefinedValue": {
                "label": "component.predefinedValue",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "min": {
                "label": "component.minDate",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "max": {
                "label": "component.maxDate",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "step": {
                "label": "component.stepNumber",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group input-group-flat",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "unique": {
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "email",
        "title": "email.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "email"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['emailField']}"
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "predefinedValue": {
                "label": "component.predefinedValue",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "minlength": {
                "label": "component.minlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "maxlength": {
                "label": "component.maxlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "pattern": {
                "label": "component.pattern",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group input-group-flat",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "unique": {
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "checkdns": {
                "label": "component.checkDNS",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "multiple": {
                "label": "component.multiple",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "textarea",
        "title": "textarea.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "textarea"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['textArea']}"
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "predefinedValue": {
                "label": "component.predefinedValue",
                "type": "textarea",
                "value": ""
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "minlength": {
                "label": "component.minlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "maxlength": {
                "label": "component.maxlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "fieldSize": {
                "label": "component.fieldSize",
                "type": "input",
                "value": 3,
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group input-group-flat",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "unique": {
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "checkbox",
        "title": "checkbox.title",
        "fields": {
            "id": {
                "label": "component.groupName",
                "type": "input",
                "value": "checkbox"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['checkAllThatApply']}"
            },
            "checkboxes": {
                "label": "component.checkboxes",
                "type": "choice",
                "value": [
                    "{$i18n['firstChoice']}|selected",
                    "{$i18n['secondChoice']}",
                    "{$i18n['thirdChoice']}"
                ]
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-check",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "inline": {
                "label": "component.inline",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "asButton": {
                "label": "component.asButton",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "radio",
        "title": "radio.title",
        "fields": {
            "id": {
                "label": "component.groupName",
                "type": "input",
                "value": "radio"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['selectAChoice']}"
            },
            "radios": {
                "label": "component.radios",
                "type": "choice",
                "value": [
                    "{$i18n['firstChoice']}|selected",
                    "{$i18n['secondChoice']}",
                    "{$i18n['thirdChoice']}"
                ]
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-check",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "inline": {
                "label": "component.inline",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "asButton": {
                "label": "component.asButton",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "selectlist",
        "title": "selectlist.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "selectlist"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['selectAChoice']}"
            },
            "options": {
                "label": "component.options",
                "type": "choice",
                "value": [
                    "{$i18n['firstChoice']}|selected",
                    "{$i18n['secondChoice']}",
                    "{$i18n['thirdChoice']}"
                ]
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-select",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "multiple": {
                "label": "component.multiple",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "hidden",
        "title": "hidden.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "hidden"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": ""
            },
            "predefinedValue": {
                "label": "component.predefinedValue",
                "type": "input",
                "value": ""
            },
             "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "unique": {
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "file",
        "title": "file.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "file"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['attachAFile']}"
            },
            "accept": {
                "label": "component.accept",
                "type": "input",
                "value": ".gif, .jpg, .png"
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "prepend": {
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "append": {
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "minFiles": {
                "label": "component.minFiles",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "maxFiles": {
                "label": "component.maxFiles",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "minSize": {
                "label": "component.minSize",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "maxSize": {
                "label": "component.maxSize",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "inputGroupClass": {
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
        "name": "snippet",
        "title": "snippet.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "snippet"
            },
            "snippet": {
                "label": "component.htmlCode",
                "type": "textarea",
                "value": "{$i18n['replaceThisCode']}"
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            }
        }
    },
    {
        "name": "recaptcha",
        "title": "recaptcha.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "recaptcha"
            },
            "theme": {
                "label": "component.theme",
                "type": "select",
                "value": [
                    {
                        "value": "light",
                        "label": "Light",
                        "selected": true
                    },
                    {
                        "value": "dark",
                        "label": "Dark",
                        "selected": false
                    }
                ]
            },
            "type": {
                "label": "component.type",
                "type": "select",
                "value": [
                    {
                        "value": "image",
                        "label": "Image",
                        "selected": true
                    },
                    {
                        "value": "audio",
                        "label": "Audio",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "size": {
                "label": "component.size",
                "type": "select",
                "value": [
                    {
                        "value": "normal",
                        "label": "Normal",
                        "selected": true
                    },
                    {
                        "value": "compact",
                        "label": "Compact",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            }
        }
    },
    {
        "name": "pagebreak",
        "title": "pagebreak.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "pagebreak"
            },
            "prev": {
                "label": "component.prev",
                "type": "input",
                "value": "{$i18n['previous']}"
            },
            "next": {
                "label": "component.next",
                "type": "input",
                "value": "{$i18n['next']}"
            }
        }
    },
    {
        "name": "spacer",
        "title": "spacer.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "spacer"
            },
            "height": {
                "label": "component.height",
                "type": "number",
                "value": "50"
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            }
        }
    },
    {
        "name": "nps",
        "title": "nps.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "nps"
            },
            "question": {
                "label": "component.question",
                "type": "input",
                "value": "{$i18n['npsQuestion']}"
            },
            "labels": {
                "label": "component.labels",
                "type": "choice",
                "value": [
                    "{$i18n['notAtAllLikely']}|selected",
                    "{$i18n['neutral']}",
                    "{$i18n['extremelyLikely']}|selected"
                ]
            },
            "labelPlacement": {
                "label": "component.labelPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": false
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": true
                    }
                ],
                "advanced": true
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "d-none",
                "advanced": true
            },
            "questionClass": {
                "label": "component.questionClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "buttonClass": {
                "label": "component.buttonClass",
                "type": "input",
                "value": "btn btn-primary btn-nps",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
	{
        "name": "matrix",
        "title": "matrix.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "matrix"
            },
            "inputType": {
                "label": "component.inputType",
                "type": "select",
                "value": [
                    {
                        "value": "radio",
                        "label": "Radio Button",
                        "selected": true
                    },
                    {
                        "value": "checkbox",
                        "label": "Checkbox",
                        "selected": false
                    },
                    {
                        "value": "select",
                        "label": "Select List",
                        "selected": false
                    },
                    {
                        "value": "text",
                        "label": "Text",
                        "selected": false
                    },
                    {
                        "value": "textarea",
                        "label": "Text Area",
                        "selected": false
                    },
                    {
                        "value": "number",
                        "label": "Number",
                        "selected": false
                    },
                    {
                        "value": "range",
                        "label": "Range",
                        "selected": false
                    },
                    {
                        "value": "email",
                        "label": "Email",
                        "selected": false
                    },
                    {
                        "value": "tel",
                        "label": "Tel",
                        "selected": false
                    },
                    {
                        "value": "url",
                        "label": "URL",
                        "selected": false
                    },
                    {
                        "value": "color",
                        "label": "Color",
                        "selected": false
                    },
                    {
                        "value": "password",
                        "label": "Password",
                        "selected": false
                    },
                    {
                        "value": "date",
                        "label": "Date",
                        "selected": false
                    },
                    {
                        "value": "datetime-local",
                        "label": "DateTime-Local",
                        "selected": false
                    },
                    {
                        "value": "time",
                        "label": "Time",
                        "selected": false
                    },
                    {
                        "value": "month",
                        "label": "Month",
                        "selected": false
                    },
                    {
                        "value": "week",
                        "label": "Week",
                        "selected": false
                    }
                ]
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "{$i18n['answerTheFollowingQuestions']}"
            },
            "questions": {
                "label": "component.questions",
                "type": "choice",
                "value": [
                    "{$i18n['firstQuestion']}",
                    "{$i18n['secondQuestion']}",
                    "{$i18n['thirdQuestion']}"
                ]
            },
            "answers": {
                "label": "component.answers",
                "type": "choice",
                "value": [
                    "{$i18n['answerA']}",
                    "{$i18n['answerB']}",
                    "{$i18n['answerC']}"
                ]
            },
            "placeholder": {
                "label": "component.placeholder",
                "type": "input",
                "value": ""
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": ""
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "pattern": {
                "label": "component.pattern",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "minlength": {
                "label": "component.minlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "maxlength": {
                "label": "component.maxlength",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "min": {
                "label": "component.min",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "max": {
                "label": "component.max",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "step": {
                "label": "component.step",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "form-control",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "tableClass": {
                "label": "component.tableClass",
                "type": "input",
                "value": "table table-striped table-hover",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            },
             "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "inline": {
                "label": "component.inline",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "multiple": {
                "label": "component.multiple",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    },
    {
		"name": "signature",
		"title": "signature.title",
		"fields": {
			"id": {
				"label": "component.id",
				"type": "input",
				"value": "signature"
			},
            "label": {
                "label": "component.label",
                "type": "input",
				"value": "{$i18n['signature']}"
            },
            "required": {
                "label": "component.required",
                "type": "checkbox",
                "value": false
            },
            "clear": {
                "label": "component.clear",
                "type": "checkbox",
                "value": true
            },
            "undo": {
                "label": "component.undo",
                "type": "checkbox",
                "value": true
            },
            "helpText": {
                "label": "component.helpText",
                "type": "textarea",
                "value": "",
                "advanced": true
            },
            "helpTextPlacement": {
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "component.belowInputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "component.aboveInputs",
                        "selected": false
                    }
                ],
                "advanced": true
            },
            "width": {
                "label": "component.width",
                "type": "input",
                "value": "400",
                "advanced": true
            },
            "height": {
                "label": "component.height",
                "type": "input",
                "value": "200",
                "advanced": true
            },
            "color": {
                "label": "component.color",
                "type": "input",
                "value": "black",
                "advanced": true
            },
            "clearText": {
                "label": "component.clearText",
                "type": "input",
                "value": "{$i18n['clear']}",
                "advanced": true
            },
            "undoText": {
                "label": "component.undoText",
                "type": "input",
                "value": "{$i18n['undo']}",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "alias": {
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            }
		}
	},
    {
        "name": "button",
        "title": "button.title",
        "fields": {
            "id": {
                "label": "component.id",
                "type": "input",
                "value": "button"
            },
            "inputType": {
                "label": "component.type",
                "type": "select",
                "value": [
                    {
                        "value": "submit",
                        "label": "Submit",
                        "selected": true
                    },
                    {
                        "value": "reset",
                        "label": "Reset",
                        "selected": false
                    },
                    {
                        "value": "image",
                        "label": "Image",
                        "selected": false
                    },
                    {
                        "value": "button",
                        "label": "Button",
                        "selected": false
                    }
                ]
            },
            "buttonText": {
                "label": "component.buttonText",
                "type": "input",
                "value": "{$i18n['buttonText']}"
            },
            "label": {
                "label": "component.label",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "src": {
                "label": "component.src",
                "type": "input",
                "value": "",
                "advanced": true
            },
            "cssClass": {
                "label": "component.cssClass",
                "type": "input",
                "value": "btn btn-primary",
                "advanced": true
            },
            "labelClass": {
                "label": "component.labelClass",
                "type": "input",
                "value": "form-label",
                "advanced": true
            },
            "containerClass": {
                "label": "component.containerClass",
                "type": "input",
                "value": "col-12",
                "advanced": true
            },
            "customAttributes": {
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            },
            "readOnly": {
                "label": "component.readOnly",
                "type": "checkbox",
                "value": false,
                "advanced": true
            },
            "disabled": {
                "label": "component.disabled",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }
        }
    }
]
EOD;

            $res = Json::decode($json, true);

            return $res;
        }

        return '';
    }

    /**********************************************
    /* Form
    /**********************************************/

    /**
     * Return form data for initialize the builder
     *
     * @param null $id
     * @param string $template
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionInitForm($id = null, $template = 'default')
    {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            $i18n = [
                'untitledForm' => Yii::t('app', 'Untitled Form'),
                'thisIsMyForm' => Yii::t('app', 'This is my form. Please fill it out. Thanks!'),
            ];

            // Default template
            $json = <<<EOD
{
    "initForm": [
        {
            "name": "heading",
            "title": "heading.title",
            "fields": {
                "id": {
                    "label": "component.id",
                    "type": "input",
                    "value": "heading_0"
                },
                "text": {
                    "label": "component.text",
                    "type": "input",
                    "value": "{$i18n['untitledForm']}"
                },
                "type": {
                    "label": "component.type",
                    "type": "select",
                    "value": [
                        {
                            "value": "h1",
                            "label": "H1",
                            "selected": true
                        },
                        {
                            "value": "h2",
                            "label": "H2",
                            "selected": false
                        },
                        {
                            "value": "h3",
                            "label": "H3",
                            "selected": false
                        },
                        {
                            "value": "h4",
                            "label": "H4",
                            "selected": false
                        },
                        {
                            "value": "h5",
                            "label": "H5",
                            "selected": false
                        },
                        {
                            "value": "h6",
                            "label": "H6",
                            "selected": false
                        }
                    ]
                },
                "cssClass": {
                    "label": "component.cssClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                },
                "containerClass": {
                    "label": "component.containerClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                }
            },
            "fresh": false
        },
        {
            "name": "paragraph",
            "title": "paragraph.title",
            "fields": {
                "id": {
                    "label": "component.id",
                    "type": "input",
                    "value": "paragraph_0"
                },
                "text": {
                    "label": "component.text",
                    "type": "textarea",
                    "value": "{$i18n['thisIsMyForm']}"
                },
                "cssClass": {
                    "label": "component.cssClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                },
                "containerClass": {
                    "label": "component.containerClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                }
            },
            "fresh": false
        }
    ],
    "settings": {
        "name": "{$i18n['untitledForm']}",
        "canvas": "#canvas",
        "disabledFieldset": false,
        "layoutSelected": "",
        "layouts": [
            {
                "id": "",
                "name": "Vertical"
            },
            {
                "id": "form-horizontal",
                "name": "Horizontal"
            },
            {
                "id": "form-inline",
                "name": "Inline"
            }
        ],
        "formSteps": {
            "title": "formSteps.title",
            "fields": {
                "id": {
                    "label": "formSteps.id",
                    "type": "input",
                    "value": "formSteps"
                },
                "steps": {
                    "label": "formSteps.steps",
                    "type": "textarea-split",
                    "value": []
                },
                "progressBar": {
                    "label": "formSteps.progressBar",
                    "type": "checkbox",
                    "value": false
                },
                "noTitles": {
                    "label": "formSteps.noTitles",
                    "type": "checkbox",
                    "value": false
                },
                "noStages": {
                    "label": "formSteps.noStages",
                    "type": "checkbox",
                    "value": false
                },
                "noSteps": {
                    "label": "formSteps.noSteps",
                    "type": "checkbox",
                    "value": false
                }
            }
        }
    }
}
EOD;

            if (is_null($id)) { // Create form

                if ($template !== 'default') {

                    $templateModel = Template::findOne([
                        'slug' => $template
                    ]);

                    // If template doesn't exist or if user doesn't have permission
                    if ($templateModel === null || !Yii::$app->user->can('viewTemplates', ['model' => $templateModel])) {
                        throw new InvalidParamException(Yii::t('app', 'Invalid template.'));
                    }

                    $json = $templateModel->builder;
                }

            } else { // Update form

                $formModel = $this->findFormModel($id);

                if ($formModel && isset($formModel->formData->builder)) {
                    $json = $formModel->formData->builder;
                }
            }

            return Json::decode($json, true);

        }

        return '';
    }

    /**
     * Create form
     *
     * @return array
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionCreateForm()
    {

        if (Yii::$app->request->isAjax) {

            // Response fornat
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Extract FormBuilder data from post request
            $post = Yii::$app->request->post();

            $data = [
                'FormBuilder' => Json::decode($post['FormBuilder'], true), // Convert to array
            ];

            $formBuilder = new FormBuilder();

            // Flags variables
            $success = false;
            $id = false;
            $message = '';
            $code = 0;

            // Form Builder Validation
            if ($formBuilder->load($data) && $formBuilder->validate()) {

                // Save data in single transaction

                $transaction = Form::getDb()->beginTransaction();

                try {

                    // Parse html form fields to array
                    $formDOM = new FormDOM();
                    // If two elements has same id, throw a exception
                    $formDOM->loadHTML($data['FormBuilder']['html']);
                    $formDOM->loadXpath();
                    $formDOM->loadFields();

                    // Filter reCaptcha component
                    $reCaptchaComponent = ArrayHelper::filter(
                        ArrayHelper::getValue($data, 'FormBuilder.data.initForm'),
                        'recaptcha',
                        'name'
                    );

                    // Update form name with form title
                    $name = ArrayHelper::getValue($data, 'FormBuilder.data.settings.name');
                    if ($name === Yii::t('app', 'Untitled Form')) {
                        $name = $formDOM->getFormTitle();
                        ArrayHelper::setValue($data, 'FormBuilder.data.settings.name', $name);
                    }

                    // Populate Form Model and Save
                    $postForm = [
                        'Form' => [
                            'name' => $name, // Form name
                            'language' => Yii::$app->language, // By default, the form language is the user language
                            // If reCaptchaComponent exists, enable reCaptcha validation
                            'recaptcha' => count($reCaptchaComponent) > 0 ? 1 : 0,
                        ]
                    ];

                    // Save Form Model
                    $formModel = new Form();
                    if ($formModel->load($postForm) && $formModel->save()) {
                        // Set form id
                        $id = $formModel->primaryKey;
                    } else {
                        // Log error
                        Yii::error("Error creating form model: " . json_encode($formModel->getErrors()));

                        if ($formModel->hasErrors('name')) {
                            throw new Exception($formModel->getFirstError('name'), 1);
                        }

                        throw new Exception(Yii::t('app', 'Error saving data'), 1);
                    }

                    // Post Form Data
                    $postFormData = [
                        'FormData' => [
                            'form_id' => $id, // Form Model id
                            'builder' => Json::htmlEncode(ArrayHelper::getValue($data, 'FormBuilder.data')),
                            'fields' => $formDOM->getFieldsAsJSON(), // (array) JSON
                            // Encodes special characters into HTML entities
                            'html' => Html::encode(ArrayHelper::getValue($data, 'FormBuilder.html')),
                            'height' => (int) ArrayHelper::getValue($data, 'FormBuilder.data.height'), // Form Height
                        ]
                    ];

                    // Populate FormData Model and Save
                    $formDataModel = new FormData();
                    if (!$formDataModel->load($postFormData) || !$formDataModel->save()) {
                        // Log error
                        Yii::error("Error creating form data model: " . json_encode($formDataModel->getErrors()));
                        throw new Exception(Yii::t('app', 'Error saving data'), 2);
                    }

                    // Save FormUI Model
                    $formUIModel = new FormUI();
                    $formUIModel->link('form', $formModel);

                    // Save FormConfirmation Model
                    $formConfirmationModel = new FormConfirmation();
                    $formConfirmationModel->link('form', $formModel);

                    // Save FormEmail Model
                    $formEmailModel = new FormEmail();
                    $formEmailModel->link('form', $formModel);

                    $transaction->commit();

                    // Change success flag and message
                    $success = true;
                    $message = Yii::t('app', 'The form has been successfully created.');

                } catch (Exception $e) {
                    // Rolls back the transaction
                    $transaction->rollBack();
                    // Log the exception
                    Yii::error($e);
                    $message = $e->getMessage();
                    $code = $e->getCode();
                }

            }

            // Response to Client
            $res = array(
                'success' => $success,
                'id'      => $id,
                'action'  => 'create',
                'message' => $message,
                'code'    => $code,
            );

            return $res;
        }

        return '';
    }

    /**
     * Update form
     *
     * @param $id
     * @return array|string
     * @throws \yii\db\Exception
     */
    public function actionUpdateForm($id)
    {
        if (Yii::$app->request->isAjax) {

            // Response fornat
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Extract FormBuilder data from post request
            $post = Yii::$app->request->post();

            $data = [
                'FormBuilder' => Json::decode($post['FormBuilder'], true), // Convert to array
            ];

            $formBuilder = new FormBuilder;
            $success = false;
            $message = '';
            $code    = 0;

            // Form Builder Validation
            if ($formBuilder->load($data) && $formBuilder->validate()) {

                // Save data in single transaction

                $transaction = Form::getDb()->beginTransaction();

                try {

                    // Parse html form fields to array
                    $formDOM = new FormDOM();
                    // If two elements has same id, throw a exception
                    $formDOM->loadHTML(ArrayHelper::getValue($data, 'FormBuilder.html'));
                    $formDOM->loadXpath();
                    $formDOM->loadFields();

                    // Get Form Model
                    $formModel = $this->findFormModel($id);
                    // Get FormData Model
                    $formDataModel = isset($formModel->formData->id) ? $formModel->formData : new FormData;
                    // Get FormEmail Model
                    $formEmailModel = $formModel->formEmail;

                    // Filter reCaptcha component
                    $reCaptchaComponent = ArrayHelper::filter(
                        ArrayHelper::getValue($data, 'FormBuilder.data.initForm'),
                        'recaptcha',
                        'name'
                    );

                    // Post Form
                    $postForm = [
                        'Form' => [
                            'name' => ArrayHelper::getValue(
                                $data,
                                'FormBuilder.data.settings.name'
                            ), // Extract the form name
                            // If reCaptchaComponent exists, enable reCaptcha validation
                            'recaptcha' => count($reCaptchaComponent) > 0 ? 1 : 0,
                        ]
                    ];

                    // Post Form Data
                    $postFormData = [
                        'FormData' => [
                            'form_id' => $id, // Form Model id
                            'builder' => Json::htmlEncode(ArrayHelper::getValue($data, 'FormBuilder.data')), // JSON
                            'fields' => $formDOM->getFieldsAsJSON(), // (array) JSON
                            // Encodes special characters into HTML entities
                            'html' => Html::encode(ArrayHelper::getValue($data, 'FormBuilder.html')),
                            'height' => (int) ArrayHelper::getValue($data, 'FormBuilder.data.height'), // Form Height
                        ]
                    ];

                    if (!$formModel->load($postForm) || !$formModel->save()) {
                        // Log error
                        Yii::error("Error updating form model: " . json_encode($formModel->getErrors()));
                        throw new Exception(Yii::t('app', 'Error saving data'), 1);
                    }

                    // Updates timestamp attribute to the current timestamp
                    $formModel->touch('updated_at');

                    // Old Form Data Model
                    $oldFormDataModel = clone $formDataModel;

                    if (!$formDataModel->load($postFormData) || !$formDataModel->validate() ||
                        !$formDataModel->save()) {
                        // Log error
                        Yii::error("Error updating form data model: " . json_encode($formDataModel->getErrors()));
                        throw new Exception(Yii::t('app', 'Error saving data'), 2);
                    }

                    // Trigger event
                    Yii::$app->trigger($this::EVENT_FORM_UPDATED, new FormEvent([
                        'sender' => $this,
                        'form' => $formModel,
                        'formData' => $formDataModel,
                        'oldFormData' => !empty($oldFormDataModel->id) ? $oldFormDataModel : $formDataModel,
                        'formDOM' => $formDOM,
                        'formBuilder' => $formBuilder,
                    ]));

                    $transaction->commit();

                    // Change success flag and message
                    $success = true;
                    $message = Yii::t('app', 'The form has been successfully updated');

                } catch (Exception $e) {
                    // Rolls back the transaction
                    $transaction->rollBack();
                    // Log the exception
                    Yii::error($e);
                    $message = $e->getMessage();
                    $code = $e->getCode();
                }

            }

            // Response to Client
            $res = array(
                'success' => $success,
                'id'      => $id,
                'action'  => 'update',
                'message' => $message,
                'code'    => $code,
            );

            return $res;
        }

        return '';
    }

    /**
     * Copy an existing Form model (and relations).
     * If the copy is successful, the browser will be redirected to the 'index' page.
     *
     * @throws NotFoundHttpException
     */
    public function actionCopyForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if ($request->isPost) {

            $id = $request->post('copy-form-id');
            $name = $request->post('copy-form-name');
            $options = $request->post('copy-form-options', []);

            // Source
            $form = $this->findFormModel($id);

            $transaction = Form::getDb()->beginTransaction();

            try {

                // Form
                $formModel = new Form();
                if (in_array('form', $options)) {
                    $formModel->attributes = $form->attributes;
                }
                $formModel->name = trim($name) !== '' && $name !== $form->name ? Html::encode($name) : $form->name . ' Copy';
                $formModel->id = null;
                $formModel->slug = null;
                $formModel->created_by = null;
                $formModel->isNewRecord = true;
                $formModel->save();

                // Form Data
                $formDataModel = new FormData();
                $formDataModel->attributes = $form->formData->attributes;
                $formDataModel->id = null;
                $formDataModel->form_id = $formModel->id;
                $formDataModel->isNewRecord = true;
                $formDataModel->setFormName($formModel->name);
                $formDataModel->save();

                // Confirmation
                $formConfirmationModel = new FormConfirmation();
                if (in_array('confirmation', $options)) {
                    $formConfirmationModel->attributes = $form->formConfirmation->attributes;
                }
                $formConfirmationModel->id = null;
                $formConfirmationModel->form_id = $formModel->id;
                $formConfirmationModel->isNewRecord = true;
                $formConfirmationModel->save();

                // Confirmation Rules
                if (in_array('confirmation', $options)) {
                    foreach($form->formConfirmationRules as $rule){
                        $formConfirmationRuleModel = new FormConfirmationRule();
                        $formConfirmationRuleModel->attributes = $rule->attributes;
                        $formConfirmationRuleModel->id = null;
                        $formConfirmationRuleModel->form_id = $formModel->id;
                        $formConfirmationRuleModel->isNewRecord = true;
                        $formConfirmationRuleModel->save();
                    }
                }

                // Notification
                $formEmailModel = new FormEmail();
                if (in_array('notification', $options)) {
                    $formEmailModel->attributes = $form->formEmail->attributes;
                }
                $formEmailModel->id = null;
                $formEmailModel->form_id = $formModel->id;
                $formEmailModel->isNewRecord = true;
                $formEmailModel->save();

                // UI
                $formUIModel = new FormUI();
                if (in_array('ui', $options)) {
                    $formUIModel->attributes = $form->ui->attributes;
                }
                $formUIModel->id = null;
                $formUIModel->form_id = $formModel->id;
                $formUIModel->isNewRecord = true;
                $formUIModel->save();

                // Conditional Rules
                if (in_array('rules', $options)) {
                    foreach($form->formRules as $rule){
                        $formRuleModel = new FormRule();
                        $formRuleModel->attributes = $rule->attributes;
                        $formRuleModel->id = null;
                        $formRuleModel->form_id = $formModel->id;
                        $formRuleModel->isNewRecord = true;
                        $formRuleModel->save();
                    }
                }

                // Report
                if (in_array('report', $options)) {
                    foreach($form->formCharts as $chart){
                        $formChartModel = new FormChart();
                        $formChartModel->attributes = $chart->attributes;
                        $formChartModel->form_id = $formModel->id;
                        $formChartModel->isNewRecord = true;
                        $formChartModel->save();
                    }
                }

                // Add-ons
                if (in_array('add-ons', $options)) {
                    Yii::$app->trigger(self::EVENT_FORM_COPIED, new FormEvent([
                        'sender' => $this,
                        'form' => $formModel,
                        'oldForm' => $form,
                    ]));
                }

                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'The form has been successfully copied'));

                $transaction->commit();
            } catch(\Exception $e) {
                Yii::error($e);
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'There was an error copying your form.'));
                return [
                    'success' => false,
                ];
            }
        }

        return [
            'success' => true,
        ];
    }

    /**********************************************
    /* Template
    /**********************************************/

    /**
     * Return template data for initialize the builder
     *
     * @param null $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionInitTemplate($id = null)
    {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            // For create forms
            if (!isset($id)) {

                $i18n = [
                    'untitledForm' => Yii::t('app', 'Untitled Form'),
                    'thisIsMyForm' => Yii::t('app', 'This is my form. Please fill it out. Thanks!'),
                ];

                // Default template
                $json = <<<EOD
{
    "initForm": [
        {
            "name": "heading",
            "title": "heading.title",
            "fields": {
                "id": {
                    "label": "component.id",
                    "type": "input",
                    "value": "heading_0"
                },
                "text": {
                    "label": "component.text",
                    "type": "input",
                    "value": "{$i18n['untitledForm']}"
                },
                "type": {
                    "label": "component.type",
                    "type": "select",
                    "value": [
                        {
                            "value": "h1",
                            "label": "H1",
                            "selected": false
                        },
                        {
                            "value": "h2",
                            "label": "H2",
                            "selected": false
                        },
                        {
                            "value": "h3",
                            "label": "H3",
                            "selected": true
                        },
                        {
                            "value": "h4",
                            "label": "H4",
                            "selected": false
                        },
                        {
                            "value": "h5",
                            "label": "H5",
                            "selected": false
                        },
                        {
                            "value": "h6",
                            "label": "H6",
                            "selected": false
                        }
                    ]
                },
                "cssClass": {
                    "label": "component.cssClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                },
                "containerClass": {
                    "label": "component.containerClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                }
            },
            "fresh": false
        },
        {
            "name": "paragraph",
            "title": "paragraph.title",
            "fields": {
                "id": {
                    "label": "component.id",
                    "type": "input",
                    "value": "paragraph_0"
                },
                "text": {
                    "label": "component.text",
                    "type": "textarea",
                    "value": "{$i18n['thisIsMyForm']}"
                },
                "cssClass": {
                    "label": "component.cssClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                },
                "containerClass": {
                    "label": "component.containerClass",
                    "type": "input",
                    "value": "",
                    "advanced": true
                }
            },
            "fresh": false
        }
    ],
    "settings": {
        "name": "{$i18n['untitledForm']}",
        "canvas": "#canvas",
        "disabledFieldset": false,
        "layoutSelected": "",
        "layouts": [
            {
                "id": "",
                "name": "Vertical"
            },
            {
                "id": "form-horizontal",
                "name": "Horizontal"
            },
            {
                "id": "form-inline",
                "name": "Inline"
            }
        ],
        "formSteps": {
            "title": "formSteps.title",
            "fields": {
                "id": {
                    "label": "formSteps.id",
                    "type": "input",
                    "value": "formSteps"
                },
                "steps": {
                    "label": "formSteps.steps",
                    "type": "textarea-split",
                    "value": []
                },
                "progressBar": {
                    "label": "formSteps.progressBar",
                    "type": "checkbox",
                    "value": false
                },
                "noTitles": {
                    "label": "formSteps.noTitles",
                    "type": "checkbox",
                    "value": false
                },
                "noStages": {
                    "label": "formSteps.noStages",
                    "type": "checkbox",
                    "value": false
                },
                "noSteps": {
                    "label": "formSteps.noSteps",
                    "type": "checkbox",
                    "value": false
                }
            }
        }
    }
}
EOD;
                return Json::decode($json);
            }

            // For update templates
            $templateModel = $this->findTemplateModel($id);

            if (!empty($templateModel)) {
                return Json::decode($templateModel->builder, true);
            }
        }

        return '';
    }

    /**
     * Create Template model
     * @return array
     */
    public function actionCreateTemplate()
    {

        if (Yii::$app->request->isAjax) {

            // Response fornat
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Extract FormBuilder data from post request
            $post = Yii::$app->request->post();

            $data = [
                'FormBuilder' => Json::decode($post['FormBuilder'], true), // Convert to array
            ];

            $formBuilder = new FormBuilder();

            // Flags variables
            $success = false;
            $id = false;
            $message = '';
            $code = 0;

            // Form Builder Validation
            if ($formBuilder->load($data) && $formBuilder->validate()) {

                // Save data in single transaction

                $transaction = Template::getDb()->beginTransaction();

                try {

                    // Parse html form fields to array
                    $formDOM = new FormDOM();
                    // If two elements has same id, throw a exception
                    $formDOM->loadHTML(ArrayHelper::getValue($data, 'FormBuilder.html'));

                    // Update form name with form title
                    $name = ArrayHelper::getValue($data, 'FormBuilder.data.settings.name');
                    if ($name === Yii::t('app', 'Untitled Form')) {
                        $name = $formDOM->getFormTitle();
                        ArrayHelper::setValue($data, 'FormBuilder.data.settings.name', $name);
                    }

                    // Populate Template Model and Save
                    $postTemplate = [
                        'Template' => [
                            'name' => $name, // Form name
                            'builder' => Json::htmlEncode(ArrayHelper::getValue($data, 'FormBuilder.data')),
                            // Encodes special characters into HTML entities
                            'html' => Html::encode(ArrayHelper::getValue($data, 'FormBuilder.html')),
                        ]
                    ];

                    // Save Template Model
                    $templateModel = new Template();
                    if ($templateModel->load($postTemplate) && $templateModel->save()) {
                        // Set form id
                        $id = $templateModel->primaryKey;
                    } else {
                        // Log error
                        Yii::error("Error creating template model: " . json_encode($templateModel->getErrors()));
                        throw new Exception(Yii::t('app', 'Error saving data'), 1);
                    }

                    $transaction->commit();

                    // Change success flag and message
                    $success = true;
                    $message = Yii::t('app', 'The template has been successfully created.');

                    // Show message after redirection
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app', 'The template has been successfully created.')
                    );

                } catch (Exception $e) {
                    // Rolls back the transaction
                    $transaction->rollBack();
                    // Log the exception
                    Yii::error($e);
                    $message = $e->getMessage();
                    $code = $e->getCode();
                }

            }

            // Response to Client
            $res = array(
                'success' => $success,
                'id'      => $id,
                'action'  => 'create',
                'message' => $message,
                'code'    => $code,
            );

            return $res;
        }

        return '';
    }

    /**
     * Update Template model
     *
     * @param $id
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionUpdateTemplate($id)
    {

        if (Yii::$app->request->isAjax) {

            // Response fornat
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Extract FormBuilder data from post request
            $post = Yii::$app->request->post();

            $data = [
                'FormBuilder' => Json::decode($post['FormBuilder'], true), // Convert to array
            ];

            $formBuilder = new FormBuilder;
            $success = false;
            $message = '';
            $code = 0;

            // Form Builder Validation
            if ($formBuilder->load($data) && $formBuilder->validate()) {

                // Save data in single transaction

                $transaction = Template::getDb()->beginTransaction();

                try {

                    // Parse html form fields to array
                    $formDOM = new FormDOM();
                    // If two elements has same id, throw a exception
                    $formDOM->loadHTML(ArrayHelper::getValue($data, 'FormBuilder.html'));

                    // Get Template Model
                    $templateModel = $this->findTemplateModel($id);

                    // Post Template
                    $postTemplate = [
                        'Template' => [
                            'name' => ArrayHelper::getValue($data, 'FormBuilder.data.settings.name'), // Form name
                            'builder' => Json::htmlEncode(ArrayHelper::getValue($data, 'FormBuilder.data')), // JSON
                            // Encodes special characters into HTML entities
                            'html' => Html::encode(ArrayHelper::getValue($data, 'FormBuilder.html')),
                        ]
                    ];

                    if (!$templateModel->load($postTemplate) || !$templateModel->save()) {
                        // Log error
                        Yii::error("Error updating template model: " . json_encode($templateModel->getErrors()));
                        throw new Exception(Yii::t('app', 'Error saving data'), 1);
                    }

                    $transaction->commit();

                    // Change success flag and message
                    $success = true;
                    $message = Yii::t('app', 'The template has been successfully updated');

                    // Show message after redirection
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app', 'The template has been successfully updated')
                    );

                } catch (Exception $e) {
                    // Rolls back the transaction
                    $transaction->rollBack();
                    // Log the exception
                    Yii::error($e);
                    $message = $e->getMessage();
                    $code = $e->getCode();
                }

            }

            // Response to Client
            $res = array(
                'success' => $success,
                'id'      => $id,
                'action'  => 'update',
                'message' => $message,
                'code'    => $code,
            );

            return $res;
        }

        return '';
    }

    /**********************************************
    /* Analytics
    /**********************************************/

    /**
     * Show & Save form report
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    public function actionReport($id)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        // Extract report data from post request
        $post = Yii::$app->request->post();

        // The raw report data
        $rawReport = $post['report'] ?? null;

        if (!is_null($rawReport)) {

            // Convert to charts array
            $charts = Json::decode($rawReport, true);

            // Save data in single transaction

            $transaction = FormChart::getDb()->beginTransaction();
            $success = false;

            try {

                // Delete old charts if there are
                FormChart::deleteAll(['form_id' => $id]);

                if (count($charts) > 0) {

                    // Populate each Form Chart Model and Save
                    foreach ($charts as $chart) {

                        $formChartModel = new FormChart();

                        // Add form_id to chart
                        $chart['form_id'] = $id;

                        // Prepare new model data
                        $postFormChart = [
                            'FormChart' => $chart
                        ];

                        // Load & Save the model
                        if (!$formChartModel->load($postFormChart) || !$formChartModel->save()) {
                            throw new Exception(Yii::t('app', 'Error saving the chart'));
                        }
                    }

                }

                $transaction->commit();

                // Change success flag and message
                $success = true;
                $message = Yii::t('app', 'The report has been successfully updated.');

            } catch (Exception $e) {
                // Rolls back the transaction
                $transaction->rollBack();
                // Rethrow the exception
                // throw $e;
                $message = $e->getMessage();
            }

            $res = array(
                'success' => $success,
                'id'      => $id,
                'action'  => 'update',
                'message' => $message
            );

            return $res;
        }

        $submissions = [];
        $fields = [];

        $formModel = Form::findOne(['id' => $id]);
        if ($formModel && $formModel->formData) {
            $fields = $formModel->formData->getFieldsForEmail();
            $fields = !empty($fields) ? array_fill_keys(array_keys($fields), '') : [];
        }

        foreach (FormSubmission::find()->select(['data', 'created_at'])->where(
            'form_id=:form_id',
            [':form_id' => $id]
        )->each(10) as $submissionModel) {
            $submission = $submissionModel->data;
            $submission = array_merge($fields, $submission);
            // Exclude Matrix fields
            $submission = array_filter($submission, function ($value, $field) {
                return !empty($field) && substr($field, 0, 6) !== 'matrix';
            }, ARRAY_FILTER_USE_BOTH);
            if (!empty($submission)) {
                $submission['created_at'] = $submissionModel->created_at;
                $submissions[] = $submission;
            }
        }

        return $submissions;

    }

    /**
     * Show form performance stats in csv format
     *
     * @param $id
     */
    public function actionAnalytics($id)
    {

        // Analytics Report
        $report = Analytics::report();

        // Set params
        $report->app($id)->report('performance')->prepare()->performAsCSV();

    }

    /**
     * Show form submissions stats in csv format
     *
     * @param $id
     */
    public function actionStats($id)
    {

        // Analytics Report
        $report = Analytics::report();

        // Set params
        $report->app($id)->report('submissions')->prepare()->performAsCSV();

    }

    /**
     * Save User Preference: Default Theme
     *
     * @return array
     */
    public function actionThemeSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $post = Yii::$app->request->post();

        if (!empty($post['mode'])) {
            Yii::$app->user->preferences->set('UI.theme.mode', $post['mode']);
            $success = Yii::$app->user->preferences->save();
        }

        return ['success' => $success];
    }

    /**
     * Save User Preference: Default GridView
     *
     * @return array
     */
    public function actionGridViewSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $post = Yii::$app->request->post();

        if (isset($post['show-filters'])) {
            Yii::$app->user->preferences->set('GridView.filters.state', $post['show-filters']);
            $success = Yii::$app->user->preferences->save();
        }

        if (!empty($post['page-size'])) {
            Yii::$app->user->preferences->set('GridView.pagination.pageSize', $post['page-size']);
            $success = Yii::$app->user->preferences->save();
        }

        return ['success' => $success];
    }

    /**
     * Save User Preference: Submission Manager GridView
     *
     * @return array
     */
    public function actionSubmissionManagerSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $post = Yii::$app->request->post();

        if (!empty($post['form_id'])) {
            Yii::$app->user->preferences->set('GridView.submissions.settings.'. $post['form_id'], $post['settings']);
        }

        if (isset($post['emptyFields'])) {
            Yii::$app->user->preferences->set('GridView.submissions.settings.emptyFields', $post['emptyFields']);
        }

        if (isset($post['showVirtualFields'])) {
            Yii::$app->user->preferences->set('GridView.submissions.settings.showVirtualFields', $post['showVirtualFields']);
        }

        if (isset($post['showDoiStatus'])) {
            Yii::$app->user->preferences->set('GridView.submissions.settings.showDoiStatus', $post['showDoiStatus']);
        }

        if (isset($post['showSenderInformation'])) {
            Yii::$app->user->preferences->set('GridView.submissions.settings.showSenderInformation', $post['showSenderInformation']);
        }

        if (isset($post['showAdditionalInformation'])) {
            Yii::$app->user->preferences->set('GridView.submissions.settings.showAdditionalInformation', $post['showAdditionalInformation']);
        }

        return ['success' => Yii::$app->user->preferences->save()];
    }

    /**
     * Save User Preference: Show Form Page Settings
     *
     * @return array
     */
    public function actionFormPageSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $post = Yii::$app->request->post();

        if (isset($post['show'])) {
            Yii::$app->user->preferences->set('Form.page.settings.show', $post['show']);
            $success = Yii::$app->user->preferences->save();
        }

        return ['success' => $success];
    }

    /**
     * Save Form Settings Preferences:
     * - Wysiwyg editors
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionFormPreferences($id): array
    {
        $formModel = $this->findFormModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $post = Yii::$app->request->post();

        if (isset($post['key'], $post['value']) && is_string($post['key'])) {
            $preferences = new Preferences($formModel);
            $preferences->set($post['key'], $post['value']);
            $success = $preferences->save();
        }

        return ['success' => $success];
    }

    /**
     * Return list of fields for Field Mapping
     *
     * @param int $all
     * @return array
     * @throws NotFoundHttpException
     * @deprecated Use AjaxController::actionFields()
     */
    public function actionFieldList($all = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        $fields = [];

        // Add Form Fields
        if (isset($post['id'])) {
            $formModel = Form::findOne(['id' => $post['id']]);
            if ($formModel !== null) {
                $formDataModel = $formModel->formData;
                $fields = $formDataModel->getFieldsForEmail(false, true);
            }
        }

        // Add Additional Fields
        if ($all === 1) {
            $fields = SubmissionHelper::getFieldsForFieldMapping($fields, true);
        }

        // Filter fields
        if (!empty($post['term'])) {
            $fields = array_filter($fields, function ($v) use ($post) {
                return strpos($v, $post['term']) !== false;
            });
        }

        if (count($fields) > 0) {
            return [
                'success' => true,
                'data' => $fields,
            ];
        }

        return ['success' => false];
    }

    /**
     * Return list of fields
     *
     * @param $form
     * @param $files
     * @param $virtual
     * @param $system
     * @param $addons
     * @param $alias
     * @return array|false[]
     */
    public function actionFields($form = 1, $files = 1, $virtual = 1, $system = 1, $addons = 0, $alias = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        $fields = [];

        // Fields according to configuration
        if (isset($post['id'])) {
            $formModel = Form::findOne(['id' => $post['id']]);
            if ($formModel !== null && $formModel->formData !== null) {
                $fields = $formModel->formData->getFieldList([
                    'form' => (bool) $form, // Include form fields
                    'files' => (bool) $files, // Include file fields
                    'virtual' => (bool) $virtual, // Include virtual fields
                    'alias' => (bool) $alias, // Add alias to field labels
                    'system' => (bool)  $system, // Include system fields
                    'addons' => (bool) $addons, // Include fields added by add-ons
                ]);
            }
        } else if ($system === 1) {
            // Fields according to configuration
            $fields = SubmissionHelper::getFieldsForFieldMapping($fields, (bool) $addons);
        }

        // Filter fields
        if (!empty($post['term'])) {
            $fields = array_filter($fields, function ($v) use ($post) {
                return strpos($v, $post['term']) !== false;
            });
        }

        if (count($fields) > 0) {
            return [
                'success' => true,
                'data' => $fields,
            ];
        }

        return ['success' => false];
    }

    /**
     * Return list of users
     *
     * @param string $q Query string
     * @param int $e Exclude this User ID
     * @return array
     */
    public function actionUserList(string $q = null, int $e = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var User[] $users */
        $query = User::find()
            ->select(['id', 'username'])
            ->where('username LIKE "%' . $q .'%"');
        if (!empty($e)) {
            $query->andWhere(['!=', 'id', $e]);
        }
        $users = $query->orderBy(['username' => SORT_ASC])->all();
        $items = [];
        foreach ($users as $user) {
            $items[] = [
                'id' => $user->id,
                'username' => $user->username,
            ];
        }
        return [
            'items' => $items,
        ];
    }

    /**
     * Count new form submissions of current user
     *
     * @return string|array
     */
    public function actionCountNotifications()
    {
        $request = Yii::$app->request;
        $currentUser = Yii::$app->user;

        if ($request->isPost) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            $forms = $currentUser->forms()->asArray()->all();
            $formIds = ArrayHelper::getColumn($forms, 'id');

            $data = FormSubmission::find()
                ->select(['form_id', 'created_at'])
                ->where([
                    'form_id' => $formIds,
                    'new' => 1
                ])
                ->orderBy('created_at DESC')
                ->asArray()
                ->all();

            return [
                'data' => $data,
                'timestamp' => time()
            ];
        }

        return '';
    }

    /**
     * Load new form submissions of current user
     *
     * @return string
     */
    public function actionLoadNotifications()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $this->layout = false;
            $currentUser = Yii::$app->user;
            $forms = $currentUser->forms()->asArray()->all();
            $formIds = ArrayHelper::getColumn($forms, 'id');
            $submissions = FormSubmission::find()
                ->where([
                    'form_id' => $formIds,
                    'new' => 1
                ])
                ->orderBy('created_at DESC')
                ->limit(7)
                ->all();

            return $this->render('load-notifications', [
                'submissions' => $submissions,
            ]);
        }

        return '';
    }

    /**
     * Clean new form submissions of current user
     *
     * @return array|string
     */
    public function actionCleanNotifications()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->layout = false;
            $currentUser = Yii::$app->user;
            $forms = $currentUser->forms()->asArray()->all();
            $formIds = ArrayHelper::getColumn($forms, 'id');
            $submissions = FormSubmission::updateAll([
                'new' => 0,
            ], [
                'form_id' => $formIds,
                'new' => 1
            ]);

            return [
                'submissions' => $submissions,
                'timestamp' => time()
            ];
        }

        return '';
    }

    /**
     * Delete Form Images
     *
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionDeleteFormImage()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->post('id');
            $image = $request->post('image');
            $formModel = $this->findFormModel($id);

            if (in_array($image, ['favicon', 'logo', 'meta_image'])) {
                $imagePath = $formModel->ui->{$image};
                // Check if same image is used with other fields
                $count = 0;
                if ($imagePath === $formModel->ui->favicon) {
                    ++$count;
                }
                if ($imagePath === $formModel->ui->logo) {
                    ++$count;
                }
                if ($imagePath === $formModel->ui->meta_image) {
                    ++$count;
                }
                // Remove from disk
                if ($count <= 1 && Yii::$app->fs->has($imagePath)) {
                    Yii::$app->fs->delete($imagePath);
                }
                // Remove from DB
                $formModel->ui->updateAttributes([
                    $image => null
                ]);
            }

            return [
                'success' => 1,
            ];
        }

        return '';
    }

    /**********************************************
    /* Models
    /**********************************************/

    /**
     * Finds the Form model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Form the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findFormModel($id)
    {
        if (($model = Form::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Finds the Template model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTemplateModel($id)
    {
        if (($model = Template::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
