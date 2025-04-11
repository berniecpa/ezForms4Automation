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

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "form_ui".
 *
 * @property integer $id
 * @property integer $form_id
 * @property integer $theme_id
 * @property string $js_file
 * @property string $favicon
 * @property string $logo
 * @property integer $logo_link
 * @property string $logo_link_url
 * @property string $meta_image
 * @property string $meta_title
 * @property string $meta_description
 * @property string $styles
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Form $form
 */
class FormUI extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_ui}}';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_id'], 'required'],
            [['favicon', 'logo', 'logo_link_url', 'meta_image', 'meta_title', 'meta_description', 'styles'], 'string'],
            [['form_id', 'theme_id', 'logo_link', 'created_at', 'updated_at'], 'integer'],
            [['js_file'], 'safe'],
            // ensure empty values are stored as NULL in the database
            ['theme_id', 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'form_id' => Yii::t('app', 'Form ID'),
            'theme_id' => Yii::t('app', 'Theme ID'),
            'js_file' => Yii::t('app', 'JavaScript UI Widgets'),
            'favicon' => Yii::t('app', 'Favicon'),
            'logo' => Yii::t('app', 'Logo'),
            'logo_link' => Yii::t('app', 'Logo Link'),
            'logo_link_url' => Yii::t('app', 'Logo Link Url'),
            'styles' => Yii::t('app', 'CSS Styles'),
            'meta_title' => Yii::t('app', 'Meta Title'),
            'meta_description' => Yii::t('app', 'Meta Description'),
            'meta_image' => Yii::t('app', 'Meta Image'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->js_file = explode(',', $this->js_file ?? '');

        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (is_array($this->js_file)) {
            $this->js_file = implode(',', $this->js_file);
        }

        return parent::beforeValidate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Form::class, ['id' => 'form_id']);
    }
}
