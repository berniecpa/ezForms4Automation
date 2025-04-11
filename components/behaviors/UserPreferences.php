<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.9.1
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
namespace app\components\behaviors;

use app\models\User;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;

class UserPreferences extends Preferences
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function save(): bool
    {
        $this->check();
        if ($this->dirty) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $user->preferences = Json::encode($this->preferences);
            $result = $user->save(false);
            $this->dirty = !$result;
            return $result;
        }
        $this->loaded = true;
        return false;
    }

    /**
     * @inheritDoc
     */
    public function load()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $json = $user ? $user->preferences : '';
        $this->preferences = Json::decode($json, true);
        $this->dirty = false;
        $this->loaded = true;
    }

}