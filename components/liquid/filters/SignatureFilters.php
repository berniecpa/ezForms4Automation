<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.14.3
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\components\liquid\filters;

use app\helpers\Html;
use app\helpers\SubmissionHelper;
use Yii;

/**
 * Class SignatureFilters
 * @package app\components\liquid\filters
 */
class SignatureFilters
{
    const SIGNATURE_URL = 'url';
    const SIGNATURE_IMAGE = 'image';

    public $context;

    /**
     * Allow users to select what format to use to display a signature
     *
     * @param mixed $input
     * @param string $format
     *
     * @return string
     */
    public function signature($input, string $format = 'image'): string
    {

        // Default format
        if (!in_array($format, [self::SIGNATURE_IMAGE, self::SIGNATURE_URL])) {
            $format = self::SIGNATURE_IMAGE;
        }

        $output = Html::img($input, [
            'style' =>  'display:block;',
            'alt' => Yii::t('app', 'Signature')
        ]);

        if ($format === self::SIGNATURE_URL) {
            $output = $input;
        }

        return $output;
    }
}