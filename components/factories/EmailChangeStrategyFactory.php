<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 2.1
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\components\factories;

use app\components\strategies\DefaultEmailChangeStrategy;
use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Factory\EmailChangeStrategyFactory as BaseEmailChangeStrategyFactory;
use Da\User\Strategy\InsecureEmailChangeStrategy;
use Da\User\Strategy\SecureEmailChangeStrategy;

/**
 * Class EmailChangeStrategyFactory
 * @package app\components\factories
 */
class EmailChangeStrategyFactory extends BaseEmailChangeStrategyFactory
{
    protected static $map = [
        MailChangeStrategyInterface::TYPE_INSECURE => InsecureEmailChangeStrategy::class,
        MailChangeStrategyInterface::TYPE_DEFAULT => DefaultEmailChangeStrategy::class,
        MailChangeStrategyInterface::TYPE_SECURE => SecureEmailChangeStrategy::class,
    ];
}