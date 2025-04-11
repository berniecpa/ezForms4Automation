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

namespace app\components\analytics\enricher;

use app\components\analytics\enricher\RefererParser\Parser;

/**
 * Class UrlEnrichment
 * @package app\components\analytics\enricher
 */
class UrlEnrichment
{
    public $url;
    public $referrer;
    public $referrerParsed;

    public function __construct($url, $referrer)
    {
        $this->url = !empty($url) ? parse_url($url) : null;
        $this->referrer = !empty($referrer) ? parse_url($referrer) : null;

        $refererParser = new Parser();
        $this->referrerParsed = !empty($url) && !empty($referrer) ? $refererParser->parse($referrer, $url) : null;
    }

    // Web-specific fields
    public function getData()
    {
        $data = array();

        if (isset($this->url)) {
            $data["page_urlscheme"] = $this->url["scheme"] ?? null;
            $data["page_urlhost"] = $this->url["host"] ?? null;
            $data["page_urlport"] = $this->url["port"] ?? null;
            $data["page_urlpath"] = $this->url["path"] ?? null;
            $data["page_urlquery"] = $this->url["query"] ?? null;
            $data["page_urlquery"] = !empty($this->url["query"])
                ? substr($this->url["query"], 0, 100)
                : null;
            $data["page_urlfragment"] = $this->url["fragment"] ?? null;
        }

        if (isset($this->referrer)) {
            $data["refr_urlscheme"] = $this->referrer["scheme"] ?? null;
            $data["refr_urlhost"] = $this->referrer["host"] ?? null;
            $data["refr_urlport"] = $this->referrer["port"] ?? null;
            $data["refr_urlpath"] = $this->referrer["path"] ?? null;
            $data["refr_urlquery"] = !empty($this->referrer["query"])
                ? substr($this->referrer["query"], 0, 100)
                : null;
            $data["refr_urlfragment"] = $this->referrer["fragment"] ?? null;
        }

        if (isset($this->referrerParsed)) {
            $data["refr_medium"] = $this->referrerParsed->getMedium();
            $data["refr_source"] = $this->referrerParsed->getSource();
            $data["refr_term"] = $this->referrerParsed->getSearchTerm();
        }

        $data = array_filter($data, function ($v) {
            return !is_null($v);
        });

        return $data;

    }
}
