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

namespace app\models\forms;

use app\components\validators\FileValidator;
use app\helpers\FileHelper;
use app\models\Form;
use Yii;
use yii\base\Model;

/**
 * Forgot password form
 */
class FormPageSettingsForm extends Model
{

    /**
     * @var $form_id
     */
    public $form_id;

    /**
     * @var $logo
     */
    public $logo;

    /**
     * @var $favicon
     */
    public $favicon;

    /**
     * @var $logo_link
     */
    public $logo_link;

    /**
     * @var $logo_link_url
     */
    public $logo_link_url;

    /**
     * @var $styles
     */
    public $styles;

    /**
     * @var $meta_title
     */
    public $meta_title;

    /**
     * @var $meta_description
     */
    public $meta_description;

    /**
     * @var $meta_image
     */
    public $meta_image;

    public function rules()
    {
        return [
            [['favicon'], 'file', 'skipOnEmpty' => true, 'extensions' => 'ico,png,gif', 'maxFiles' => 1, 'checkExtensionByMimeType' => false],
            [['logo', 'meta_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg,gif,svg', 'maxFiles' => 1],
            [['logo_link_url', 'styles', 'meta_title', 'meta_description'], 'string'],
            [['logo_link'], 'integer'],
            [['logo_link_url'], 'url', 'defaultScheme' => 'https'],
        ];
    }

    /**
     * Save Form Page Settings
     *
     * @return bool
     * @throws \Exception
     */
    public function save(): bool
    {
        if ($this->validate()) {
            $form = Form::findOne(['id' => $this->form_id]);
            if ($form && $form->ui) {
                if (!empty($this->logo)) {
                    $oldLogo = $form->ui->logo;
                    if (!empty($oldLogo) && Yii::$app->fs->has($oldLogo)) {
                        Yii::$app->fs->delete($oldLogo);
                    }
                    $newLogo = $form->getPublicFilesDirectory() . '/' . $this->logo->baseName . '.' . $this->logo->extension;
                    if (!empty($newLogo) && Yii::$app->fs->has($newLogo)) {
                        Yii::$app->fs->delete($newLogo);
                    }
                    if (FileHelper::save($newLogo, $this->logo->tempName)) {
                        $form->ui->logo = $newLogo;
                    }
                }
                if (!empty($this->favicon)) {
                    $oldFavicon = $form->ui->favicon;
                    if (!empty($oldFavicon) && Yii::$app->fs->has($oldFavicon)) {
                        Yii::$app->fs->delete($oldFavicon);
                    }
                    $newFavicon = $form->getPublicFilesDirectory() . '/' . $this->favicon->baseName . '.' . $this->favicon->extension;
                    if (!empty($newFavicon) && Yii::$app->fs->has($newFavicon)) {
                        Yii::$app->fs->delete($newFavicon);
                    }
                    if (FileHelper::save($newFavicon, $this->favicon->tempName)) {
                        $form->ui->favicon = $newFavicon;
                    }
                }
                if (!empty($this->meta_image)) {
                    $oldMetaImage = $form->ui->meta_image;
                    if (!empty($oldMetaImage) && Yii::$app->fs->has($oldMetaImage)) {
                        Yii::$app->fs->delete($oldMetaImage);
                    }
                    $newMetaImage = $form->getPublicFilesDirectory() . '/' . $this->meta_image->baseName . '.' . $this->meta_image->extension;
                    if (!empty($newMetaImage) && Yii::$app->fs->has($newMetaImage)) {
                        Yii::$app->fs->delete($newMetaImage);
                    }
                    if (FileHelper::save($newMetaImage, $this->meta_image->tempName)) {
                        $form->ui->meta_image = $newMetaImage;
                    }
                }
                $form->ui->logo_link = $this->logo_link;
                $form->ui->logo_link_url = $this->logo_link_url;
                $form->ui->meta_title = $this->meta_title;
                $form->ui->meta_description = $this->meta_description;
                $form->ui->styles = $this->styles;
                $form->ui->save();
            }
            return true;
        }

        return false;
    }

}
