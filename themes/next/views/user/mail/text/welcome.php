<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

/**
 * @var \Da\User\Model\User  $user
 * @var \Da\User\Model\Token $token
 * @var \Da\User\Module      $module
 * @var bool                 $showPassword
 */
?>
<?= Yii::t('app', 'Hello') ?>,

<?= Yii::t('app', 'Your account on {0} has been created', Yii::$app->settings->get("app.name")) ?>.
<?php if ($showPassword || $module->generatePasswords): ?>
    <?= Yii::t('app', 'We have generated a password for you') ?>:
    <?= $user->password ?>
<?php endif ?>

<?php if ($token !== null): ?>
    <?= Yii::t('app', 'In order to complete your registration, please click the link below') ?>.
    <?= $token->url ?>
    <?= Yii::t('app', 'If you cannot click the link, please try pasting the text into your browser') ?>.
<?php endif ?>

<?= Yii::t('app', 'If you did not make this request you can ignore this email') ?>.
