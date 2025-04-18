<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.10
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
namespace app\components\rbac\rules;

use app\components\User;
use yii;
use yii\rbac\Rule;

/**
 * Class AuthorRule
 * Verifies if resource author/owner matches current user
 * @package app\components\rbac\rules
 */
class AuthorRule extends Rule
{

    public $name = 'isAuthor';

    /**
     * @param string|integer $user the user ID.
     * @param yii\rbac\Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['listing']) && $params['listing']) {
            // Used by Data Providers to filter a collection
            return true;
        } elseif (isset($params['model'])) {
            $model = $params['model'];
            return isset($model->created_by) && $model->created_by == $user;
        } elseif (isset($params['ids'], $params['modelClass'])) {
            /** @var User $user */
            $user = Yii::$app->user;
            $ownIds = $user->getOwnModelIds($params['modelClass']);
            foreach ($params['ids'] as $id) {
                if (!in_array($id, $ownIds)) {
                    return false;
                }
            }
            return true;
        }
        // Denied access by default
        return false;
    }
}
