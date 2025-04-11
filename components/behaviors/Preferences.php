<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 2.2
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\components\behaviors;

use app\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Json;

class Preferences
{

    /**
     * The database model object.
     *
     * @var object
     */
    protected $model;

    /**
     * The preferences cache.
     *
     * @var array
     */
    protected $preferences = array();

    /**
     * Whether any preferences have been modified since being loaded.
     * We use an array so different constraints can be flagged as dirty separately.
     *
     * @var bool
     */
    protected $dirty = array();

    /**
     * Whether preferences have been loaded from the database (this session).
     * We use an array so different constraints can be loaded separately.
     *
     * @var array
     */
    protected $loaded = array();

    public function __construct($model = null)
    {
        if ($model
            && (property_exists($model, 'preferences')
                || ($model instanceof ActiveRecord && $model->hasAttribute('preferences')))) {
            $this->model = $model;
        }
    }

    /**
     * @throws Exception
     */
    public function setModel($model)
    {
        if (!property_exists($model, 'preferences')
            && !($model instanceof ActiveRecord && $model->hasAttribute('preferences'))) {
            throw new Exception('Preferences property not set');
        }

        $this->model = $model;
    }

    /**
     * Get preferences for external management
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }

    /**
     * Get the value of a specific setting.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, $default = null)
    {
        $this->check();
        $value = ArrayHelper::getValue($this->preferences, $key);
        if (is_null($value) && isset(Yii::$app->params[$key])) {
            $value = Yii::$app->params[$key];
        }
        return empty($value) ? $default : $value;
    }

    /**
     * Set the value of a specific setting.
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $this->check();
        $this->dirty = true;
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                ArrayHelper::setValue($this->preferences, $k, $v);
            }
        } else {
            ArrayHelper::setValue($this->preferences, $key, $value);
        }
    }

    /**
     * Unset a specific setting.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key)
    {
        $this->check();
        if (array_key_exists($key, $this->preferences)) {
            unset($this->preferences[$key]);
            $this->dirty = true;
        }
    }

    /**
     * Check for the existence of a specific setting.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $this->check();
        return array_key_exists($key, $this->preferences);
    }

    /**
     * Return the entire preferences array.
     *
     * @return array
     */
    public function all(): array
    {
        $this->check();
        return $this->preferences;
    }

    /**
     * Check if preferences have been loaded, load if not.
     *
     * @return void
     */
    protected function check()
    {
        if (empty($this->loaded)) {
            $this->load();
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function save(): bool
    {
        $this->check();
        if ($this->dirty) {
            $result = $this->model->updateAttributes([
                'preferences' => Json::encode($this->preferences)
            ]);
            $this->dirty = !$result;
            return (bool) $result;
        }
        $this->loaded = true;
        return false;
    }

    /**
     * @inheritDoc
     */
    public function load()
    {
        $json = $this->model->preferences ?? '';
        $this->preferences = Json::decode($json, true);
        $this->dirty = false;
        $this->loaded = true;
    }

}