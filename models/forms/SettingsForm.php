<?php

namespace app\models\forms;

use app\components\factories\EmailChangeStrategyFactory;
use app\models\User;
use Da\User\Form\SettingsForm as BaseForm;

class SettingsForm extends BaseForm
{
    /**
     * Saves new account settings.
     *
     * @throws \Exception
     * @return bool
     *
     */
    public function save()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user instanceof User) {
                $user->scenario = 'settings';
                $user->username = $this->username;
                $user->password = $this->new_password;
                if ($this->email === $user->email && $user->unconfirmed_email !== null) {
                    $user->unconfirmed_email = null;
                } elseif ($this->email !== $user->email) {
                    $strategy = EmailChangeStrategyFactory::makeByStrategyType(
                        $this->getModule()->emailChangeStrategy,
                        $this
                    );

                    return $strategy->run();
                }

                return $user->save();
            }
        }

        return false;
    }
}