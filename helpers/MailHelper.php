<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.1
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\helpers;

use app\components\queue\MailQueue;
use app\components\queue\Message;
use app\components\Settings;
use app\events\SubmissionMailEvent;
use app\models\Form;
use app\models\FormSubmission;
use Yii;

/**
 * Class Mailer
 * @package app\helpers
 *
 * Business logic to send emails
 */
class MailHelper
{

    const EVENT_BEFORE_CONFIGURE_FORM_MAILER = 'beforeConfigureFormMailer';

    /**
     * Return the sender email address according to app configuration
     *
     * @param string $sender Email by default
     * @return string Email address
     */
    public static function from(string $sender = '')
    {

        /** @var MailQueue $mailer */
        $mailer = Yii::$app->mailer;
        /** @var Settings $settings */
        $settings = Yii::$app->settings;

        // Check for messageConfig before sending (for backwards-compatible purposes)
        if (isset($mailer->messageConfig["from"]) && !empty($mailer->messageConfig["from"])) {
            $sender = $mailer->messageConfig["from"];
        } if (isset(Yii::$app->params['App.Modules.SMTP.username']) &&
            !empty(Yii::$app->params['App.Modules.SMTP.username']) &&
            (!filter_var(Yii::$app->params['App.Modules.SMTP.username'], FILTER_VALIDATE_EMAIL) === false)) {
            $sender = Yii::$app->params['App.Modules.SMTP.username'];
        } elseif (isset(Yii::$app->params['App.Mailer.transport']) &&
            Yii::$app->params['App.Mailer.transport'] === 'smtp' &&
            (!filter_var($settings->get("smtp.username"), FILTER_VALIDATE_EMAIL) === false)) {
            // Set smtp username as sender
            $sender = $settings->get("smtp.username");
        }

        // Add name to Sender
        if (is_string($sender)
            && (!filter_var($sender, FILTER_VALIDATE_EMAIL) === false)) {
            $from = $sender;
            if (!empty(Yii::$app->params['App.Modules.SMTP.fromName'])
                && $senderName = trim(Yii::$app->params['App.Modules.SMTP.fromName']) ?? '') {
                $from = [$sender => $senderName];
            } elseif ($senderName = trim($settings->get("app.defaultFromName") ?? '')) {
                $from = [$sender => $senderName];
            } elseif ($senderName = trim($settings->get("app.name") ?? '')) {
                $from = [$sender => $senderName];
            }
            $sender = $from;
        }

        return $sender;
    }

    /**
     * Check if the email should be asynchronous
     *
     * @return bool
     */
    public static function async()
    {
        /** @var Settings $settings */
        $settings = Yii::$app->settings;

        // Async Email
        $async = !empty(Yii::$app->params['App.Mailer.async']) && Yii::$app->params['App.Mailer.async'] === 1;
        $async = (boolean) $settings->get('async', 'app', $async);
        return $async && $settings->get('mailerTransport', 'app', 'php') === 'smtp';
    }

    /**
     * Send Notification Message By E-Mail
     *
     * @param Form $formModel
     * @param FormSubmission $submissionModel
     * @param array $filePaths
     * @return bool
     * @throws \Exception
     */
    public static function sendNotificationByEmail(Form $formModel, FormSubmission $submissionModel, array $filePaths = []): bool
    {
        $result = false;
        // Each email address should be unique between to, cc, and bcc
        $toCcBcc = [];

        /** @var Settings $settings */
        $settings = Yii::$app->settings;

        $dataModel = $formModel->formData;
        $emailModel = $formModel->formEmail;
        $submissionData = $submissionModel->getSubmissionData();

        // If file paths are empty, find them by model relation
        if (empty($filePaths)) {
            $fileModels = $submissionModel->files;
            foreach ($fileModels as $fileModel) {
                $filePaths[] = $fileModel->getUrl();
            }
        }

        $mailTo = [];

        if (!empty($emailModel->to)) {
            $emails = explode(',', $emailModel->to);
            foreach ($emails as $email) {
                if (!in_array($email, $toCcBcc)) {
                    $mailTo[] = trim($email);
                    $toCcBcc[] = trim($email);
                }
            }
        }

        if (!empty($emailModel->field_to)) {
            foreach ($emailModel->field_to as $fieldTo) {
                // To (Get value of email field)
                $emails = !empty($submissionData[$fieldTo])
                    ? explode(',', $submissionData[$fieldTo])
                    : [];
                foreach ($emails as $email) {
                    // Remove all illegal characters from email
                    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                    // Validate e-mail
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        if (!in_array($email, $toCcBcc)) {
                            $mailTo[] = trim($email);
                            $toCcBcc[] = trim($email);
                        }
                    }
                }
            }
        }

        // Check first: Recipient and Sender are required
        if (isset($emailModel->from) && !empty($mailTo)) {

            // Async Email
            $async = static::async();

            // Form fields
            $fieldsForEmail = $dataModel->getFieldList([
                'form' => true, // Include form fields
                'files' => false, // Include file fields
            ]);
            // Submission data in an associative array
            $tokens = SubmissionHelper::prepareDataForReplacementToken($submissionData, $fieldsForEmail);
            // Submission data in a multidimensional array: [0 => ['label' => '', 'value' => '']]
            $fieldData = SubmissionHelper::prepareDataForSubmissionTable($submissionData, $fieldsForEmail);

            // Trigger Event
            Yii::$app->trigger(self::EVENT_BEFORE_CONFIGURE_FORM_MAILER, new SubmissionMailEvent([
                'submission' => $submissionModel,
                'type' => SubmissionMailEvent::EVENT_TYPE_NOTIFICATION,
                'message' => null,
                'async' => $async,
                'tokens' => $tokens,
            ]));

            // Sender by default: No-Reply Email
            $fromEmail = static::from($settings->get("app.noreplyEmail"));
            // Sender verification
            if (empty($fromEmail)) {
                return false;
            }

            // Data
            $data = [
                'fieldValues' => $tokens, // Submission data for replacement
                'fieldData' => $fieldData, // Submission data for print details
                'formID' => $formModel->id,
                'submissionID' => isset($submissionModel->primaryKey) ? $submissionModel->primaryKey : null,
                'message' => $emailModel->message,
                'receipt_copy' => (boolean) $emailModel->receipt_copy, // Add submission copy
            ];

            // Views
            $notificationViews = $emailModel->getEmailViews();
            // Subject
            $subject = !empty($emailModel->subject) ?
                $emailModel->subject :
                $formModel->name . ' - ' . Yii::t('app', 'New Submission');
            // Token replacement in subject
            $subject = SubmissionHelper::replaceTokens($subject, $tokens);
            $subject = Liquid::render($subject, $tokens);
            // When a submission is updated
            if (!empty($submissionModel->updated_at) &&
                $submissionModel->updated_at > $submissionModel->created_at) {
                $subject = $subject . ' - ' . Yii::t('app', 'Edited');
            }

            // Add name to From
            if (!empty($emailModel->from_name)) {
                $fromName = isset($submissionData[$emailModel->from_name]) ?
                    $submissionData[$emailModel->from_name] : $emailModel->from_name;
                if (!empty($fromName)) {
                    $fromName = is_array($fromName) ? implode(",", $fromName) : $fromName;
                    if (is_array($fromEmail)) {
                        $fromEmail = [
                            key($fromEmail) => $fromName,
                        ];
                    } else {
                        $fromEmail = [
                            $fromEmail => $fromName,
                        ];
                    }
                }
            }

            // Compose email
            /** @var Message $message */
            $message = Yii::$app->mailer->compose($notificationViews, $data)
                ->setFrom($fromEmail)
                ->setTo($mailTo)
                ->setSubject($subject);

            // Reply to
            if (!empty($emailModel->from)) {
                // ReplyTo (can be an email or an email field)
                $replyToField = isset($submissionData[$emailModel->from]) ? $submissionData[$emailModel->from] : null;
                $replyTo = $emailModel->fromIsEmail() ? $emailModel->from :
                    $replyToField;

                // Add name to Reply to
                if (!empty($replyTo) && !empty($emailModel->from_name)) {
                    $fromName = isset($submissionData[$emailModel->from_name]) ?
                        $submissionData[$emailModel->from_name] : $emailModel->from_name;
                    $fromName = is_array($fromName) ? implode(",", $fromName) : $fromName;

                    if (is_array($replyTo)) {
                        $replyTo = [
                            key($replyTo) => $fromName,
                        ];
                    } else {
                        $replyTo = [
                            $replyTo => $fromName,
                        ];
                    }
                }

                if (!empty($replyTo)) {
                    $message->setReplyTo($replyTo);
                }
            }

            // Add cc
            if (is_array($emailModel->cc)) {
                $ccEmails = [];
                foreach ($emailModel->cc as $cc) {
                    if (!empty($submissionData[$cc])) {
                        // An email field can have multiple emails
                        $emails = explode(',', $submissionData[$cc]);
                        foreach ($emails as $email) {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                                if (!in_array($email, $toCcBcc)) {
                                    $ccEmails[] = trim($email);
                                    $toCcBcc[] = trim($email);
                                }
                            }
                        }
                    } elseif (!filter_var($cc, FILTER_VALIDATE_EMAIL) === false) {
                        if (!in_array($cc, $toCcBcc)) {
                            $ccEmails[] = trim($cc);
                            $toCcBcc[] = trim($cc);
                        }
                    }
                }
                if (!empty($ccEmails)) {
                    $message->setCc($ccEmails);
                }
            }

            // Add bcc
            if (is_array($emailModel->bcc)) {
                $bccEmails = [];
                foreach ($emailModel->bcc as $bcc) {
                    if (!empty($submissionData[$bcc])) {
                        // An email field can have multiple emails
                        $emails = explode(',', $submissionData[$bcc]);
                        foreach ($emails as $email) {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                                if (!in_array($email, $toCcBcc)) {
                                    $bccEmails[] = trim($email);
                                    $toCcBcc[] = trim($email);
                                }
                            }
                        }
                    } elseif (!filter_var($bcc, FILTER_VALIDATE_EMAIL) === false) {
                        if (!in_array($bcc, $toCcBcc)) {
                            $bccEmails[] = trim($bcc);
                            $toCcBcc[] = trim($bcc);
                        }
                    }
                }
                if (!empty($bccEmails)) {
                    $message->setBcc($bccEmails);
                }
            }

            // Attach files
            if ($emailModel->attach && count($filePaths) > 0) {
                foreach ($filePaths as $filePath) {
                    $message->attach(Yii::getAlias('@app') . DIRECTORY_SEPARATOR . $filePath);
                }
            }

            // Trigger Event
            Yii::$app->trigger(SubmissionMailEvent::EVENT_NAME, new SubmissionMailEvent([
                'submission' => $submissionModel,
                'type' => SubmissionMailEvent::EVENT_TYPE_NOTIFICATION,
                'message' => $message,
                'async' => $async,
                'tokens' => $tokens,
            ]));

            // Send E-mail
            if ($async) {
                $result = $message->queue();
            } else {
                $result = $message->send();
            }

        }

        return $result;
    }

    /**
     * Send Confirmation Message By E-Mail
     *
     * @param Form $formModel
     * @param FormSubmission $submissionModel
     * @param array $filePaths
     * @return bool
     * @throws \Exception
     */
    public static function sendConfirmationByEmail(Form $formModel, FormSubmission $submissionModel, array $filePaths = []): bool
    {

        $result = false;
        // Each email address should be unique between to, cc, and bcc
        $toCcBcc = [];

        /** @var Settings $settings */
        $settings = Yii::$app->settings;

        $dataModel = $formModel->formData;
        $confirmationModel = $formModel->formConfirmation;
        $submissionData = $submissionModel->getSubmissionData();

        // If file paths are empty, find them by model relation
        if (empty($filePaths)) {
            $fileModels = $submissionModel->files;
            foreach ($fileModels as $fileModel) {
                $filePaths[] = $fileModel->getUrl();
            }
        }

        // Check first: Send email must be active and Recipient is required
        if ($confirmationModel->send_email && !empty($confirmationModel->mail_to)) {

            // Async Email
            $async = static::async();

            // Form fields
            $fieldsForEmail = $dataModel->getFieldList([
                'form' => true, // Include form fields
                'files' => false, // Include file fields
            ]);
            // Submission data in an associative array
            $tokens = SubmissionHelper::prepareDataForReplacementToken($submissionData, $fieldsForEmail);
            // Submission data in a multidimensional array: [0 => ['label' => '', 'value' => '']]
            $fieldData = SubmissionHelper::prepareDataForSubmissionTable($submissionData, $fieldsForEmail);

            // Trigger Event
            Yii::$app->trigger(self::EVENT_BEFORE_CONFIGURE_FORM_MAILER, new SubmissionMailEvent([
                'submission' => $submissionModel,
                'type' => SubmissionMailEvent::EVENT_TYPE_CONFIRMATION,
                'message' => null,
                'async' => $async,
                'tokens' => $tokens,
            ]));

            // Sender by default: No-Reply Email
            $fromEmail = static::from($settings->get("app.noreplyEmail"));
            // Sender verification
            if (empty($fromEmail)) {
                return false;
            }

            $mailTo = [];

            foreach ($confirmationModel->mail_to as $fieldTo) {
                // To (Get value of email field)
                $to = $submissionData[$fieldTo] ?? null;
                // Remove all illegal characters from email
                $to = filter_var($to, FILTER_SANITIZE_EMAIL);
                // Validate e-mail
                if (!filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
                    if (!in_array($to, $toCcBcc)) {
                        $mailTo[] = trim($to);
                        $toCcBcc[] = trim($to);
                    }
                }
            }

            if (!empty($mailTo)) {

                // Views
                $confirmationViews = $confirmationModel->getEmailViews();

                // Message
                $data = [
                    'fieldValues' => $tokens, // Submission data for replacement
                    'fieldData' => $fieldData, // Submission data for print details
                    'mail_receipt_copy' => (boolean) $confirmationModel->mail_receipt_copy, // Add submission copy
                    'message' => !empty($confirmationModel->mail_message)
                        ? $confirmationModel->mail_message
                        : Yii::t('app', 'Thanks for your message'),
                ];

                // Subject
                $subject = !empty($confirmationModel->mail_subject)
                    ? $confirmationModel->mail_subject
                    : Yii::t('app', 'Thanks for your message');

                // Token replacement in subject
                $subject = SubmissionHelper::replaceTokens($subject, $tokens);
                $subject = Liquid::render($subject, $tokens);

                // Add name to From
                if (!empty($confirmationModel->mail_from_name)) {
                    if (is_array($fromEmail)) {
                        $fromEmail = [
                            key($fromEmail) => $confirmationModel->mail_from_name,
                        ];
                    } else {
                        $fromEmail = [
                            $fromEmail => $confirmationModel->mail_from_name,
                        ];
                    }
                }

                // Compose email
                /** @var Message $message */
                $message = Yii::$app->mailer->compose($confirmationViews, $data)
                    ->setFrom($fromEmail)
                    ->setTo($mailTo)
                    ->setSubject($subject);

                // Add reply to
                if (!empty($confirmationModel->mail_from)) {
                    $message->setReplyTo($confirmationModel->mail_from);
                }

                // Add cc
                if (!empty($confirmationModel->mail_cc)) {
                    $mailCc = explode(',', $confirmationModel->mail_cc);
                    $emails = [];
                    foreach ($mailCc as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                            if (!in_array($email, $toCcBcc)) {
                                $emails[] = trim($email);
                                $toCcBcc[] = trim($email);
                            }
                        }
                    }
                    $message->setCc($emails);
                }

                // Add bcc
                if (!empty($confirmationModel->mail_bcc)) {
                    $mailBcc = explode(',', $confirmationModel->mail_bcc);
                    $emails = [];
                    foreach ($mailBcc as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                            if (!in_array($email, $toCcBcc)) {
                                $emails[] = trim($email);
                                $toCcBcc[] = trim($email);
                            }
                        }
                    }
                    $message->setBcc($emails);
                }

                // Attach files
                if ($confirmationModel->mail_attach && count($filePaths) > 0) {
                    foreach ($filePaths as $filePath) {
                        $message->attach(Yii::getAlias('@app') . DIRECTORY_SEPARATOR . $filePath);
                    }
                }

                // Trigger Event
                Yii::$app->trigger(SubmissionMailEvent::EVENT_NAME, new SubmissionMailEvent([
                    'submission' => $submissionModel,
                    'type' => SubmissionMailEvent::EVENT_TYPE_CONFIRMATION,
                    'message' => $message,
                    'async' => $async,
                    'tokens' => $tokens,
                ]));

                // Send E-mail
                if ($async) {
                    $result = $message->queue();
                } else {
                    $result = $message->send();
                }
            }
        }

        return $result;
    }

    /**
     * Return Amazon SES Regions
     * https://docs.aws.amazon.com/general/latest/gr/ses.html
     *
     * @return string[]
     */
    public static function awsSesRegions(): array
    {
        return [
            'us-east-2' => 'US East (Ohio)',
            'us-east-1' => 'US East (N. Virginia)',
            'us-west-1' => 'US West (N. California)',
            'us-west-2' => 'US West (Oregon)',
            'ap-south-1' => 'Asia Pacific (Mumbai)',
            'ap-northeast-2' => 'Asia Pacific (Seoul)',
            'ap-southeast-1' => 'Asia Pacific (Singapore)',
            'ap-southeast-2' => 'Asia Pacific (Sydney)',
            'ap-northeast-1' => 'Asia Pacific (Tokyo)',
            'ca-central-1' => 'Canada (Central)',
            'eu-central-1' => 'Europe (Frankfurt)',
            'eu-west-1' => 'Europe (Ireland)',
            'eu-west-2' => 'Europe (London)',
            'eu-west-3' => 'Europe (Paris)',
            'eu-north-1' => 'Europe (Stockholm)',
            'me-south-1' => 'Middle East (Bahrain)',
            'sa-east-1' => 'South America (São Paulo)',
            'us-gov-west-1' => 'AWS GovCloud (US)',
        ];
    }

}
