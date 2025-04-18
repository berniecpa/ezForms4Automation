<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.14
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\helpers;

use yii\helpers\Json;

/**
 * Class FormBuilderUpdater
 * @package app\helpers
 */
class FormBuilderUpdater
{

    /**
     * Update Form Builder Fields
     *
     * @param $builder
     * @param $version
     * @return string
     * @throws \Exception
     */
    public function updateFields($builder, $version): string
    {
        // Get Form Builder configuration
        $builder = !is_array($builder) ? Json::decode($builder, true) : $builder;

        // Get configuration of each field
        $fields = ArrayHelper::getValue($builder, 'initForm');
        $i = 0;

        foreach ($fields as $field) {
            // @since v1.6.6
            if (empty($version) || version_compare($version, '1.6.6') === -1) {
                $builder = $this->v1_6($builder, $field, $i);
            }

            // @since v1.11
            if (empty($version) || version_compare($version, '1.11') === -1) {
                $builder = $this->v1_11($builder, $field, $i);
            }

            // @since v1.14
            if (empty($version) || version_compare($version, '1.14') === -1) {
                $builder = $this->v1_13($builder, $field, $i);
            }

            // @since v2.0.5
            if (empty($version) || version_compare($version, '2.0.5') === -1) {
                $builder = $this->v2_0_5($builder, $field, $i);
            }

            // @since v2.2.1
            if (empty($version) || version_compare($version, '2.2.1') === -1) {
                $builder = $this->v2_2_1($builder, $field, $i);
            }

            ++$i;
        }

        return Json::htmlEncode($builder);
    }

    /**
     * Update to v1.66
     *
     * Log:
     * - Add Alias option to Field Settings
     *
     * @param $builder
     * @param $field
     * @param $i
     * @return mixed
     * @throws \Exception
     */
    public function v1_6($builder, $field, $i)
    {

        /**
         * 1. Set option
         */

        $alias = [
            'alias' => Json::decode('{
                "label": "component.alias",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)];

        /**
         * 2. Update fields
         */

        if (!in_array($field['name'], ['heading', 'paragraph', 'snippet', 'recaptcha', 'pagebreak', 'spacer', 'button'])) {
            $fields = $field['fields'];
            // Check if Alias doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.alias');
            if (!$exists) {
                if ($field['name'] === 'hidden') { // Insert before Disabled option
                    ArrayHelper::insert($fields, 'disabled', $alias);
                } else { // Insert after ContainerClass option
                    ArrayHelper::insert($fields, 'containerClass', $alias, true);
                }
                // Replace each settings of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        return $builder;
    }

    /**
     * Update to v1.11
     *
     * Log:
     * - Add Min Length option to Text Field Settings
     * - Add Max Length option to Text Field Settings
     * - Add Unique option to Hidden Field Settings
     * - Add Help Text Placement option to Field Settings
     * - Add Min Files option to File Field Settings
     * - Add Max Files option to File Field Settings
     *
     * @param $builder
     * @param $field
     * @param $i
     * @return mixed
     * @throws \Exception
     */
    public function v1_11($builder, $field, $i)
    {
        /**
         * 1. Set options
         */

        // Add Min Length option to Text Field Settings, if this option doesn't exists
        $minlength = [
            'minlength' => Json::decode('{
                "label": "component.minlength",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)];

        // Add Max Length option to Text Field Settings, if this option doesn't exists
        $maxlength = [
            'maxlength' => Json::decode('{
                "label": "component.maxlength",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)];

        // Add Unique option to Hidden Field Settings, if this option doesn't exists
        $unique = [
            'unique' => Json::decode('{
                "label": "component.unique",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }', true)];

        // Add Help Text Placement option to Field Settings, if this option doesn't exists
        $helpTextPlacement = [
            'helpTextPlacement' => Json::decode('{
                "label": "component.helpTextPlacement",
                "type": "select",
                "value": [
                    {
                        "value": "below",
                        "label": "Below inputs",
                        "selected": true
                    },
                    {
                        "value": "above",
                        "label": "Above inputs",
                        "selected": false
                    }
                ],
                "advanced": true
            }', true)];

        // Add Min Files option to File Field Settings, if this option doesn't exists
        $minFiles = [
            'minFiles' => Json::decode('{
                "label": "component.minFiles",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)];

        // Add Max Files option to File Field Settings, if this option doesn't exists
        $maxFiles = [
            'maxFiles' => Json::decode('{
                "label": "component.maxFiles",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)];

        /**
         * 2. Update fields
         */

        // Add Min Length and Max Length options only to Text field settings
        if (in_array($field['name'], ['text', 'email', 'textarea'])) {
            $fields = $field['fields'];
            // Check if "minlength" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.minlength');
            if (!$exists) {
                // Insert after "helpText" option
                ArrayHelper::insert($fields, 'helpText', $minlength, true);
                // Replace each settings of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
            // Check if "maxlength" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.maxlength');
            if (!$exists) {
                // Insert after "minlength" option
                ArrayHelper::insert($fields, 'minlength', $maxlength, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        // Add Unique option only to Hidden Field settings
        if (in_array($field['name'], ['hidden'])) {
            $fields = $field['fields'];
            // Check if "unique" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.unique');
            if (!$exists) {
                // Insert after "alias" option
                ArrayHelper::insert($fields, 'alias', $unique, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        // Add helpTextPlacement option to Field settings
        if (in_array($field['name'], ['checkbox', 'date', 'email', 'file', 'number', 'radio', 'selectlist', 'signature', 'text', 'textarea'])) {
            $fields = $field['fields'];
            // Check if "helpTextPlacement" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.helpTextPlacement');
            if (!$exists) {
                // Insert after "helpText" option
                ArrayHelper::insert($fields, 'helpText', $helpTextPlacement, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        // Add Min Files and Max Files options only to File field settings
        if (in_array($field['name'], ['file'])) {
            $fields = $field['fields'];
            // Check if "minFiles" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.minFiles');
            if (!$exists) {
                // Insert after "helpTextPlacement" option
                ArrayHelper::insert($fields, 'helpTextPlacement', $minFiles, true);
                // Replace each settings of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
            // Check if "maxFiles" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.maxFiles');
            if (!$exists) {
                // Insert after "minFiles" option
                ArrayHelper::insert($fields, 'minFiles', $maxFiles, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        return $builder;
    }

    /**
     * Update to v1.13
     *
     * Log
     * - Add Custom Attributes option to Field Settings
     *
     * @param $builder
     * @param $field
     * @param $i
     * @return mixed
     * @throws \Exception
     */
    public function v1_13($builder, $field, $i)
    {
        /**
         * 1. Set options
         */

        $customAttributes = [
            'customAttributes' => Json::decode('{
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            }', true)
        ];

        /**
         * 2. Update fields
         */

        if (in_array($field['name'], ['text', 'number', 'date', 'email', 'textarea', 'checkbox', 'radio', 'selectlist', 'hidden', 'file', 'button'])) {
            $fields = $field['fields'];
            // Check if "customAttributes" doesn't exist
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.customAttributes');
            if (!$exists) {
                if ($field['name'] === 'button') {
                    // Insert after "containerClass" option
                    ArrayHelper::insert($fields, 'containerClass', $customAttributes, true);
                } else {
                    // Insert after "alias" option
                    ArrayHelper::insert($fields, 'alias', $customAttributes, true);
                }
                // Replace each settings of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        return $builder;
    }

    /**
     * Update to v2.0.5
     *
     * Log
     * - Add "As Button" option to Radio Button and Checkbox
     *
     * @param $builder
     * @param $field
     * @param $i
     * @return mixed
     * @throws \Exception
     */
    public function v2_0_5($builder, $field, $i)
    {
        /**
         * 1. Set options
         */

        $asButton = [
            'asButton' => Json::decode('{
                "label": "component.asButton",
                "type": "checkbox",
                "value": false,
                "advanced": true
            }', true)
        ];

        /**
         * 2. Update fields
         */

        if (in_array($field['name'], ['checkbox', 'radio'])) {
            $fields = $field['fields'];
            // Check if "asButton" doesn't exist
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.asButton');
            if (!$exists) {
                // Insert after "inline" option
                ArrayHelper::insert($fields, 'inline', $asButton, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        return $builder;
    }


    /**
     * Update to v2.2.1
     *
     * Log
     * - Add "Custom Attributes" option to Matrix Field
     * - Add "Prepend" option to 'text', 'number', 'date',
     *  'email', 'textarea', 'selectlist', 'file' fields
     * - Add "Append" option to 'text', 'number', 'date',
     *  'email', 'textarea', 'selectlist', 'file' fields
     * - Add "Input Group CSS Class" option to 'text', 'number', 'date',
     *  'email', 'textarea', 'selectlist', 'file' fields
     *
     * @param $builder
     * @param $field
     * @param $i
     * @return mixed
     * @throws \Exception
     */
    public function v2_2_1($builder, $field, $i)
    {
        /**
         * 1. Set options
         */

        $customAttributes = [
            'customAttributes' => Json::decode('{
                "label": "component.customAttributes",
                "type": "choice",
                "value": [
                    ""
                ],
                "advanced": true
            }', true)
        ];

        $prepend = [
            'prepend' => Json::decode('{
                "label": "component.prepend",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)
        ];

        $append = [
            'append' => Json::decode('{
                "label": "component.append",
                "type": "input",
                "value": "",
                "advanced": true
            }', true)
        ];

        $inputGroupClass = [
            'inputGroupClass' => Json::decode('{
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group",
                "advanced": true
            }', true)
        ];

        $inputGroupClassFlat = [
            'inputGroupClass' => Json::decode('{
                "label": "component.inputGroupClass",
                "type": "input",
                "value": "input-group input-group-flat",
                "advanced": true
            }', true)
        ];

        /**
         * 2. Update fields
         */

        if ($field['name'] === 'matrix') {
            $fields = $field['fields'];
            // Check if "customAttributes" doesn't exist
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.customAttributes');
            if (!$exists) {
                // Insert after "alias" option
                ArrayHelper::insert($fields, 'alias', $customAttributes, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        if (in_array($field['name'], ['text', 'number', 'date', 'email', 'textarea', 'selectlist', 'file'])) {
            $fields = $field['fields'];
            // Check if "prepend" doesn't exist
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.prepend');
            if (!$exists) {
                // Insert after "helpTextPlacement" option
                ArrayHelper::insert($fields, 'helpTextPlacement', $prepend, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
            // Check if "append" doesn't exists
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.append');
            if (!$exists) {
                // Insert after "prepend" option
                ArrayHelper::insert($fields, 'prepend', $append, true);
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
            // Check if "inputGroupClass" doesn't exist
            $exists = ArrayHelper::getValue($builder, 'initForm.'.$i.'.fields.inputGroupClass');
            if (!$exists) {
                if (in_array($field['name'], ['selectlist', 'file'])) {
                    // Insert after "labelClass" option
                    ArrayHelper::insert($fields, 'labelClass', $inputGroupClass, true);
                } else {
                    // Insert after "labelClass" option
                    ArrayHelper::insert($fields, 'labelClass', $inputGroupClassFlat, true);
                }
                // Replace each setting of each field in the form
                ArrayHelper::setValue($builder, 'initForm.'.$i.'.fields', $fields);
            }
        }

        return $builder;
    }
}