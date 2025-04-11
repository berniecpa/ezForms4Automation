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

namespace app\models\forms;

use app\components\queue\Message;
use app\components\validators\MultipleEmailValidator;
use app\helpers\MailHelper;
use Yii;
use yii\base\Model;

/**
 * Send By Email Form
 */
class SendByEmailForm extends Model
{
    /**
     * @var string
     */
    public $to;
    /**
     * @var string
     */
    public $cc;
    /**
     * @var string
     */
    public $bcc;
    /**
     * @var string
     */
    public $subject;
    /**
     * @var string
     */
    public $message;
    /**
     * @var string
     */
    public $from_name;
    /**
     * @var string
     */
    public $reply_to;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['to', 'subject', 'message'], 'required'],
            [['to', 'cc', 'bcc', 'reply_to'], 'trim'],
            [['from_name'], 'string', 'max' => 45],
            [['subject'], 'string', 'max' => 988],
            [['message'], 'string'],
            [['to'], MultipleEmailValidator::class],
            [['reply_to'], MultipleEmailValidator::class, 'multiple' => false],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'subject' => Yii::t('app', 'Subject'),
            'message' => Yii::t('app', 'Message'),
            'to' => Yii::t('app', 'Send To'),
            'cc' => Yii::t('app', 'Carbon Copy (cc)'),
            'bcc' => Yii::t('app', 'Blind Carbon Copy (bcc)'),
            'from_name' => Yii::t('app', 'Sender Name'),
            'reply_to' => Yii::t('app', 'Reply-To Email'),
        ];
    }

    /**
     * Send Email
     *
     * @return bool
     */
    public function send()
    {
        if ($this->validate()) {
            $settings = Yii::$app->settings;
            // Sender by default: No-Reply Email
            $fromEmail = MailHelper::from($settings->get("app.noreplyEmail"));
            // Sender verification
            if (empty($fromEmail)) {
                return false;
            }
            // Mail To verification
            if (empty($this->to)) {
                return false;
            }
            // Recipients
            $mailTo = array_map('trim', explode(',', $this->to));
            // Add Name to Sender
            if (!empty($this->from_name)) {
                if (is_array($fromEmail)) {
                    $fromEmail = [
                        key($fromEmail) => $this->from_name,
                    ];
                } else {
                    $fromEmail = [
                        $fromEmail => $this->from_name,
                    ];
                }
            }
            // Compose email
            /** @var Message $message */
            $message = Yii::$app->mailer
                ->compose(null)
                ->setHtmlBody($this->message)
                ->setTextBody(strip_tags($this->message))
                ->setFrom($fromEmail)
                ->setTo($mailTo)
                ->setSubject($this->subject);
            // Add reply to
            if (!empty($this->reply_to)) {
                $message->setReplyTo($this->reply_to);
            }
            // Send email
            return $message->send();
        }

        return false;
    }
}