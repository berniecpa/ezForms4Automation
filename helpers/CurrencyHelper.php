<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.13
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\helpers;

/**
 * Class CurrencyHelper
 * @package app\helpers
 */
class CurrencyHelper
{

    /**
     * Return Currency Information
     *
     * @param $currency_code
     * @return array|null
     */
    public static function getCurrency($currency_code)
    {
        $currencies = self::currencies();
        return isset($currencies[$currency_code]) ? $currencies[$currency_code] : null;
    }

    /**
     * Get Currency Symbol
     *
     * @param $currency_code
     * @return string|null
     */
    public static function getCurrencySymbolAsHtmlEntity($currency_code)
    {
        $symbols = self::symbolsAsHtmlEntities();
        return isset($symbols[$currency_code]) ? $symbols[$currency_code] : null;
    }

    /**
     * Get Currency List
     *
     * @return array[]
     */
    public static function currencies()
    {
        return [
            'AFN' =>
                [
                    'alphabeticCode' => 'AFN',
                    'currency' => 'Afghani',
                    'minorUnit' => 2,
                    'numericCode' => 971,
                ],
            'EUR' =>
                [
                    'alphabeticCode' => 'EUR',
                    'currency' => 'Euro',
                    'minorUnit' => 2,
                    'numericCode' => 978,
                ],
            'ALL' =>
                [
                    'alphabeticCode' => 'ALL',
                    'currency' => 'Lek',
                    'minorUnit' => 2,
                    'numericCode' => 8,
                ],
            'DZD' =>
                [
                    'alphabeticCode' => 'DZD',
                    'currency' => 'Algerian Dinar',
                    'minorUnit' => 2,
                    'numericCode' => 12,
                ],
            'USD' =>
                [
                    'alphabeticCode' => 'USD',
                    'currency' => 'US Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 840,
                ],
            'AOA' =>
                [
                    'alphabeticCode' => 'AOA',
                    'currency' => 'Kwanza',
                    'minorUnit' => 2,
                    'numericCode' => 973,
                ],
            'XCD' =>
                [
                    'alphabeticCode' => 'XCD',
                    'currency' => 'East Caribbean Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 951,
                ],
            'ARS' =>
                [
                    'alphabeticCode' => 'ARS',
                    'currency' => 'Argentine Peso',
                    'minorUnit' => 2,
                    'numericCode' => 32,
                ],
            'AMD' =>
                [
                    'alphabeticCode' => 'AMD',
                    'currency' => 'Armenian Dram',
                    'minorUnit' => 2,
                    'numericCode' => 51,
                ],
            'AWG' =>
                [
                    'alphabeticCode' => 'AWG',
                    'currency' => 'Aruban Florin',
                    'minorUnit' => 2,
                    'numericCode' => 533,
                ],
            'AUD' =>
                [
                    'alphabeticCode' => 'AUD',
                    'currency' => 'Australian Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 36,
                ],
            'AZN' =>
                [
                    'alphabeticCode' => 'AZN',
                    'currency' => 'Azerbaijan Manat',
                    'minorUnit' => 2,
                    'numericCode' => 944,
                ],
            'BSD' =>
                [
                    'alphabeticCode' => 'BSD',
                    'currency' => 'Bahamian Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 44,
                ],
            'BHD' =>
                [
                    'alphabeticCode' => 'BHD',
                    'currency' => 'Bahraini Dinar',
                    'minorUnit' => 3,
                    'numericCode' => 48,
                ],
            'BDT' =>
                [
                    'alphabeticCode' => 'BDT',
                    'currency' => 'Taka',
                    'minorUnit' => 2,
                    'numericCode' => 50,
                ],
            'BBD' =>
                [
                    'alphabeticCode' => 'BBD',
                    'currency' => 'Barbados Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 52,
                ],
            'BYN' =>
                [
                    'alphabeticCode' => 'BYN',
                    'currency' => 'Belarusian Ruble',
                    'minorUnit' => 2,
                    'numericCode' => 933,
                ],
            'BZD' =>
                [
                    'alphabeticCode' => 'BZD',
                    'currency' => 'Belize Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 84,
                ],
            'XOF' =>
                [
                    'alphabeticCode' => 'XOF',
                    'currency' => 'CFA Franc BCEAO',
                    'minorUnit' => 0,
                    'numericCode' => 952,
                ],
            'BMD' =>
                [
                    'alphabeticCode' => 'BMD',
                    'currency' => 'Bermudian Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 60,
                ],
            'INR' =>
                [
                    'alphabeticCode' => 'INR',
                    'currency' => 'Indian Rupee',
                    'minorUnit' => 2,
                    'numericCode' => 356,
                ],
            'BTN' =>
                [
                    'alphabeticCode' => 'BTN',
                    'currency' => 'Ngultrum',
                    'minorUnit' => 2,
                    'numericCode' => 64,
                ],
            'BOB' =>
                [
                    'alphabeticCode' => 'BOB',
                    'currency' => 'Boliviano',
                    'minorUnit' => 2,
                    'numericCode' => 68,
                ],
            'BOV' =>
                [
                    'alphabeticCode' => 'BOV',
                    'currency' => 'Mvdol',
                    'minorUnit' => 2,
                    'numericCode' => 984,
                ],
            'BAM' =>
                [
                    'alphabeticCode' => 'BAM',
                    'currency' => 'Convertible Mark',
                    'minorUnit' => 2,
                    'numericCode' => 977,
                ],
            'BWP' =>
                [
                    'alphabeticCode' => 'BWP',
                    'currency' => 'Pula',
                    'minorUnit' => 2,
                    'numericCode' => 72,
                ],
            'NOK' =>
                [
                    'alphabeticCode' => 'NOK',
                    'currency' => 'Norwegian Krone',
                    'minorUnit' => 2,
                    'numericCode' => 578,
                ],
            'BRL' =>
                [
                    'alphabeticCode' => 'BRL',
                    'currency' => 'Brazilian Real',
                    'minorUnit' => 2,
                    'numericCode' => 986,
                ],
            'BND' =>
                [
                    'alphabeticCode' => 'BND',
                    'currency' => 'Brunei Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 96,
                ],
            'BGN' =>
                [
                    'alphabeticCode' => 'BGN',
                    'currency' => 'Bulgarian Lev',
                    'minorUnit' => 2,
                    'numericCode' => 975,
                ],
            'BIF' =>
                [
                    'alphabeticCode' => 'BIF',
                    'currency' => 'Burundi Franc',
                    'minorUnit' => 0,
                    'numericCode' => 108,
                ],
            'CVE' =>
                [
                    'alphabeticCode' => 'CVE',
                    'currency' => 'Cabo Verde Escudo',
                    'minorUnit' => 2,
                    'numericCode' => 132,
                ],
            'KHR' =>
                [
                    'alphabeticCode' => 'KHR',
                    'currency' => 'Riel',
                    'minorUnit' => 2,
                    'numericCode' => 116,
                ],
            'XAF' =>
                [
                    'alphabeticCode' => 'XAF',
                    'currency' => 'CFA Franc BEAC',
                    'minorUnit' => 0,
                    'numericCode' => 950,
                ],
            'CAD' =>
                [
                    'alphabeticCode' => 'CAD',
                    'currency' => 'Canadian Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 124,
                ],
            'KYD' =>
                [
                    'alphabeticCode' => 'KYD',
                    'currency' => 'Cayman Islands Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 136,
                ],
            'CLP' =>
                [
                    'alphabeticCode' => 'CLP',
                    'currency' => 'Chilean Peso',
                    'minorUnit' => 0,
                    'numericCode' => 152,
                ],
            'CLF' =>
                [
                    'alphabeticCode' => 'CLF',
                    'currency' => 'Unidad de Fomento',
                    'minorUnit' => 4,
                    'numericCode' => 990,
                ],
            'CNY' =>
                [
                    'alphabeticCode' => 'CNY',
                    'currency' => 'Yuan Renminbi',
                    'minorUnit' => 2,
                    'numericCode' => 156,
                ],
            'COP' =>
                [
                    'alphabeticCode' => 'COP',
                    'currency' => 'Colombian Peso',
                    'minorUnit' => 2,
                    'numericCode' => 170,
                ],
            'COU' =>
                [
                    'alphabeticCode' => 'COU',
                    'currency' => 'Unidad de Valor Real',
                    'minorUnit' => 2,
                    'numericCode' => 970,
                ],
            'KMF' =>
                [
                    'alphabeticCode' => 'KMF',
                    'currency' => 'Comorian Franc ',
                    'minorUnit' => 0,
                    'numericCode' => 174,
                ],
            'CDF' =>
                [
                    'alphabeticCode' => 'CDF',
                    'currency' => 'Congolese Franc',
                    'minorUnit' => 2,
                    'numericCode' => 976,
                ],
            'NZD' =>
                [
                    'alphabeticCode' => 'NZD',
                    'currency' => 'New Zealand Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 554,
                ],
            'CRC' =>
                [
                    'alphabeticCode' => 'CRC',
                    'currency' => 'Costa Rican Colon',
                    'minorUnit' => 2,
                    'numericCode' => 188,
                ],
            'HRK' =>
                [
                    'alphabeticCode' => 'HRK',
                    'currency' => 'Kuna',
                    'minorUnit' => 2,
                    'numericCode' => 191,
                ],
            'CUP' =>
                [
                    'alphabeticCode' => 'CUP',
                    'currency' => 'Cuban Peso',
                    'minorUnit' => 2,
                    'numericCode' => 192,
                ],
            'CUC' =>
                [
                    'alphabeticCode' => 'CUC',
                    'currency' => 'Peso Convertible',
                    'minorUnit' => 2,
                    'numericCode' => 931,
                ],
            'ANG' =>
                [
                    'alphabeticCode' => 'ANG',
                    'currency' => 'Netherlands Antillean Guilder',
                    'minorUnit' => 2,
                    'numericCode' => 532,
                ],
            'CZK' =>
                [
                    'alphabeticCode' => 'CZK',
                    'currency' => 'Czech Koruna',
                    'minorUnit' => 2,
                    'numericCode' => 203,
                ],
            'DKK' =>
                [
                    'alphabeticCode' => 'DKK',
                    'currency' => 'Danish Krone',
                    'minorUnit' => 2,
                    'numericCode' => 208,
                ],
            'DJF' =>
                [
                    'alphabeticCode' => 'DJF',
                    'currency' => 'Djibouti Franc',
                    'minorUnit' => 0,
                    'numericCode' => 262,
                ],
            'DOP' =>
                [
                    'alphabeticCode' => 'DOP',
                    'currency' => 'Dominican Peso',
                    'minorUnit' => 2,
                    'numericCode' => 214,
                ],
            'EGP' =>
                [
                    'alphabeticCode' => 'EGP',
                    'currency' => 'Egyptian Pound',
                    'minorUnit' => 2,
                    'numericCode' => 818,
                ],
            'SVC' =>
                [
                    'alphabeticCode' => 'SVC',
                    'currency' => 'El Salvador Colon',
                    'minorUnit' => 2,
                    'numericCode' => 222,
                ],
            'ERN' =>
                [
                    'alphabeticCode' => 'ERN',
                    'currency' => 'Nakfa',
                    'minorUnit' => 2,
                    'numericCode' => 232,
                ],
            'ETB' =>
                [
                    'alphabeticCode' => 'ETB',
                    'currency' => 'Ethiopian Birr',
                    'minorUnit' => 2,
                    'numericCode' => 230,
                ],
            'FKP' =>
                [
                    'alphabeticCode' => 'FKP',
                    'currency' => 'Falkland Islands Pound',
                    'minorUnit' => 2,
                    'numericCode' => 238,
                ],
            'FJD' =>
                [
                    'alphabeticCode' => 'FJD',
                    'currency' => 'Fiji Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 242,
                ],
            'XPF' =>
                [
                    'alphabeticCode' => 'XPF',
                    'currency' => 'CFP Franc',
                    'minorUnit' => 0,
                    'numericCode' => 953,
                ],
            'GMD' =>
                [
                    'alphabeticCode' => 'GMD',
                    'currency' => 'Dalasi',
                    'minorUnit' => 2,
                    'numericCode' => 270,
                ],
            'GEL' =>
                [
                    'alphabeticCode' => 'GEL',
                    'currency' => 'Lari',
                    'minorUnit' => 2,
                    'numericCode' => 981,
                ],
            'GHS' =>
                [
                    'alphabeticCode' => 'GHS',
                    'currency' => 'Ghana Cedi',
                    'minorUnit' => 2,
                    'numericCode' => 936,
                ],
            'GIP' =>
                [
                    'alphabeticCode' => 'GIP',
                    'currency' => 'Gibraltar Pound',
                    'minorUnit' => 2,
                    'numericCode' => 292,
                ],
            'GTQ' =>
                [
                    'alphabeticCode' => 'GTQ',
                    'currency' => 'Quetzal',
                    'minorUnit' => 2,
                    'numericCode' => 320,
                ],
            'GBP' =>
                [
                    'alphabeticCode' => 'GBP',
                    'currency' => 'Pound Sterling',
                    'minorUnit' => 2,
                    'numericCode' => 826,
                ],
            'GNF' =>
                [
                    'alphabeticCode' => 'GNF',
                    'currency' => 'Guinean Franc',
                    'minorUnit' => 0,
                    'numericCode' => 324,
                ],
            'GYD' =>
                [
                    'alphabeticCode' => 'GYD',
                    'currency' => 'Guyana Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 328,
                ],
            'HTG' =>
                [
                    'alphabeticCode' => 'HTG',
                    'currency' => 'Gourde',
                    'minorUnit' => 2,
                    'numericCode' => 332,
                ],
            'HNL' =>
                [
                    'alphabeticCode' => 'HNL',
                    'currency' => 'Lempira',
                    'minorUnit' => 2,
                    'numericCode' => 340,
                ],
            'HKD' =>
                [
                    'alphabeticCode' => 'HKD',
                    'currency' => 'Hong Kong Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 344,
                ],
            'HUF' =>
                [
                    'alphabeticCode' => 'HUF',
                    'currency' => 'Forint',
                    'minorUnit' => 2,
                    'numericCode' => 348,
                ],
            'ISK' =>
                [
                    'alphabeticCode' => 'ISK',
                    'currency' => 'Iceland Krona',
                    'minorUnit' => 0,
                    'numericCode' => 352,
                ],
            'IDR' =>
                [
                    'alphabeticCode' => 'IDR',
                    'currency' => 'Rupiah',
                    'minorUnit' => 2,
                    'numericCode' => 360,
                ],
            'XDR' =>
                [
                    'alphabeticCode' => 'XDR',
                    'currency' => 'SDR (Special Drawing Right]',
                    'minorUnit' => 0,
                    'numericCode' => 960,
                ],
            'IRR' =>
                [
                    'alphabeticCode' => 'IRR',
                    'currency' => 'Iranian Rial',
                    'minorUnit' => 2,
                    'numericCode' => 364,
                ],
            'IQD' =>
                [
                    'alphabeticCode' => 'IQD',
                    'currency' => 'Iraqi Dinar',
                    'minorUnit' => 3,
                    'numericCode' => 368,
                ],
            'ILS' =>
                [
                    'alphabeticCode' => 'ILS',
                    'currency' => 'New Israeli Sheqel',
                    'minorUnit' => 2,
                    'numericCode' => 376,
                ],
            'JMD' =>
                [
                    'alphabeticCode' => 'JMD',
                    'currency' => 'Jamaican Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 388,
                ],
            'JPY' =>
                [
                    'alphabeticCode' => 'JPY',
                    'currency' => 'Yen',
                    'minorUnit' => 0,
                    'numericCode' => 392,
                ],
            'JOD' =>
                [
                    'alphabeticCode' => 'JOD',
                    'currency' => 'Jordanian Dinar',
                    'minorUnit' => 3,
                    'numericCode' => 400,
                ],
            'KZT' =>
                [
                    'alphabeticCode' => 'KZT',
                    'currency' => 'Tenge',
                    'minorUnit' => 2,
                    'numericCode' => 398,
                ],
            'KES' =>
                [
                    'alphabeticCode' => 'KES',
                    'currency' => 'Kenyan Shilling',
                    'minorUnit' => 2,
                    'numericCode' => 404,
                ],
            'KPW' =>
                [
                    'alphabeticCode' => 'KPW',
                    'currency' => 'North Korean Won',
                    'minorUnit' => 2,
                    'numericCode' => 408,
                ],
            'KRW' =>
                [
                    'alphabeticCode' => 'KRW',
                    'currency' => 'Won',
                    'minorUnit' => 0,
                    'numericCode' => 410,
                ],
            'KWD' =>
                [
                    'alphabeticCode' => 'KWD',
                    'currency' => 'Kuwaiti Dinar',
                    'minorUnit' => 3,
                    'numericCode' => 414,
                ],
            'KGS' =>
                [
                    'alphabeticCode' => 'KGS',
                    'currency' => 'Som',
                    'minorUnit' => 2,
                    'numericCode' => 417,
                ],
            'LAK' =>
                [
                    'alphabeticCode' => 'LAK',
                    'currency' => 'Lao Kip',
                    'minorUnit' => 2,
                    'numericCode' => 418,
                ],
            'LBP' =>
                [
                    'alphabeticCode' => 'LBP',
                    'currency' => 'Lebanese Pound',
                    'minorUnit' => 2,
                    'numericCode' => 422,
                ],
            'LSL' =>
                [
                    'alphabeticCode' => 'LSL',
                    'currency' => 'Loti',
                    'minorUnit' => 2,
                    'numericCode' => 426,
                ],
            'ZAR' =>
                [
                    'alphabeticCode' => 'ZAR',
                    'currency' => 'Rand',
                    'minorUnit' => 2,
                    'numericCode' => 710,
                ],
            'LRD' =>
                [
                    'alphabeticCode' => 'LRD',
                    'currency' => 'Liberian Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 430,
                ],
            'LYD' =>
                [
                    'alphabeticCode' => 'LYD',
                    'currency' => 'Libyan Dinar',
                    'minorUnit' => 3,
                    'numericCode' => 434,
                ],
            'CHF' =>
                [
                    'alphabeticCode' => 'CHF',
                    'currency' => 'Swiss Franc',
                    'minorUnit' => 2,
                    'numericCode' => 756,
                ],
            'MOP' =>
                [
                    'alphabeticCode' => 'MOP',
                    'currency' => 'Pataca',
                    'minorUnit' => 2,
                    'numericCode' => 446,
                ],
            'MKD' =>
                [
                    'alphabeticCode' => 'MKD',
                    'currency' => 'Denar',
                    'minorUnit' => 2,
                    'numericCode' => 807,
                ],
            'MGA' =>
                [
                    'alphabeticCode' => 'MGA',
                    'currency' => 'Malagasy Ariary',
                    'minorUnit' => 2,
                    'numericCode' => 969,
                ],
            'MWK' =>
                [
                    'alphabeticCode' => 'MWK',
                    'currency' => 'Malawi Kwacha',
                    'minorUnit' => 2,
                    'numericCode' => 454,
                ],
            'MYR' =>
                [
                    'alphabeticCode' => 'MYR',
                    'currency' => 'Malaysian Ringgit',
                    'minorUnit' => 2,
                    'numericCode' => 458,
                ],
            'MVR' =>
                [
                    'alphabeticCode' => 'MVR',
                    'currency' => 'Rufiyaa',
                    'minorUnit' => 2,
                    'numericCode' => 462,
                ],
            'MRU' =>
                [
                    'alphabeticCode' => 'MRU',
                    'currency' => 'Ouguiya',
                    'minorUnit' => 2,
                    'numericCode' => 929,
                ],
            'MUR' =>
                [
                    'alphabeticCode' => 'MUR',
                    'currency' => 'Mauritius Rupee',
                    'minorUnit' => 2,
                    'numericCode' => 480,
                ],
            'XUA' =>
                [
                    'alphabeticCode' => 'XUA',
                    'currency' => 'ADB Unit of Account',
                    'minorUnit' => 0,
                    'numericCode' => 965,
                ],
            'MXN' =>
                [
                    'alphabeticCode' => 'MXN',
                    'currency' => 'Mexican Peso',
                    'minorUnit' => 2,
                    'numericCode' => 484,
                ],
            'MXV' =>
                [
                    'alphabeticCode' => 'MXV',
                    'currency' => 'Mexican Unidad de Inversion (UDI]',
                    'minorUnit' => 2,
                    'numericCode' => 979,
                ],
            'MDL' =>
                [
                    'alphabeticCode' => 'MDL',
                    'currency' => 'Moldovan Leu',
                    'minorUnit' => 2,
                    'numericCode' => 498,
                ],
            'MNT' =>
                [
                    'alphabeticCode' => 'MNT',
                    'currency' => 'Tugrik',
                    'minorUnit' => 2,
                    'numericCode' => 496,
                ],
            'MAD' =>
                [
                    'alphabeticCode' => 'MAD',
                    'currency' => 'Moroccan Dirham',
                    'minorUnit' => 2,
                    'numericCode' => 504,
                ],
            'MZN' =>
                [
                    'alphabeticCode' => 'MZN',
                    'currency' => 'Mozambique Metical',
                    'minorUnit' => 2,
                    'numericCode' => 943,
                ],
            'MMK' =>
                [
                    'alphabeticCode' => 'MMK',
                    'currency' => 'Kyat',
                    'minorUnit' => 2,
                    'numericCode' => 104,
                ],
            'NAD' =>
                [
                    'alphabeticCode' => 'NAD',
                    'currency' => 'Namibia Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 516,
                ],
            'NPR' =>
                [
                    'alphabeticCode' => 'NPR',
                    'currency' => 'Nepalese Rupee',
                    'minorUnit' => 2,
                    'numericCode' => 524,
                ],
            'NIO' =>
                [
                    'alphabeticCode' => 'NIO',
                    'currency' => 'Cordoba Oro',
                    'minorUnit' => 2,
                    'numericCode' => 558,
                ],
            'NGN' =>
                [
                    'alphabeticCode' => 'NGN',
                    'currency' => 'Naira',
                    'minorUnit' => 2,
                    'numericCode' => 566,
                ],
            'OMR' =>
                [
                    'alphabeticCode' => 'OMR',
                    'currency' => 'Rial Omani',
                    'minorUnit' => 3,
                    'numericCode' => 512,
                ],
            'PKR' =>
                [
                    'alphabeticCode' => 'PKR',
                    'currency' => 'Pakistan Rupee',
                    'minorUnit' => 2,
                    'numericCode' => 586,
                ],
            'PAB' =>
                [
                    'alphabeticCode' => 'PAB',
                    'currency' => 'Balboa',
                    'minorUnit' => 2,
                    'numericCode' => 590,
                ],
            'PGK' =>
                [
                    'alphabeticCode' => 'PGK',
                    'currency' => 'Kina',
                    'minorUnit' => 2,
                    'numericCode' => 598,
                ],
            'PYG' =>
                [
                    'alphabeticCode' => 'PYG',
                    'currency' => 'Guarani',
                    'minorUnit' => 0,
                    'numericCode' => 600,
                ],
            'PEN' =>
                [
                    'alphabeticCode' => 'PEN',
                    'currency' => 'Sol',
                    'minorUnit' => 2,
                    'numericCode' => 604,
                ],
            'PHP' =>
                [
                    'alphabeticCode' => 'PHP',
                    'currency' => 'Philippine Peso',
                    'minorUnit' => 2,
                    'numericCode' => 608,
                ],
            'PLN' =>
                [
                    'alphabeticCode' => 'PLN',
                    'currency' => 'Zloty',
                    'minorUnit' => 2,
                    'numericCode' => 985,
                ],
            'QAR' =>
                [
                    'alphabeticCode' => 'QAR',
                    'currency' => 'Qatari Rial',
                    'minorUnit' => 2,
                    'numericCode' => 634,
                ],
            'RON' =>
                [
                    'alphabeticCode' => 'RON',
                    'currency' => 'Romanian Leu',
                    'minorUnit' => 2,
                    'numericCode' => 946,
                ],
            'RUB' =>
                [
                    'alphabeticCode' => 'RUB',
                    'currency' => 'Russian Ruble',
                    'minorUnit' => 2,
                    'numericCode' => 643,
                ],
            'RWF' =>
                [
                    'alphabeticCode' => 'RWF',
                    'currency' => 'Rwanda Franc',
                    'minorUnit' => 0,
                    'numericCode' => 646,
                ],
            'SHP' =>
                [
                    'alphabeticCode' => 'SHP',
                    'currency' => 'Saint Helena Pound',
                    'minorUnit' => 2,
                    'numericCode' => 654,
                ],
            'WST' =>
                [
                    'alphabeticCode' => 'WST',
                    'currency' => 'Tala',
                    'minorUnit' => 2,
                    'numericCode' => 882,
                ],
            'STN' =>
                [
                    'alphabeticCode' => 'STN',
                    'currency' => 'Dobra',
                    'minorUnit' => 2,
                    'numericCode' => 930,
                ],
            'SAR' =>
                [
                    'alphabeticCode' => 'SAR',
                    'currency' => 'Saudi Riyal',
                    'minorUnit' => 2,
                    'numericCode' => 682,
                ],
            'RSD' =>
                [
                    'alphabeticCode' => 'RSD',
                    'currency' => 'Serbian Dinar',
                    'minorUnit' => 2,
                    'numericCode' => 941,
                ],
            'SCR' =>
                [
                    'alphabeticCode' => 'SCR',
                    'currency' => 'Seychelles Rupee',
                    'minorUnit' => 2,
                    'numericCode' => 690,
                ],
            'SLL' =>
                [
                    'alphabeticCode' => 'SLL',
                    'currency' => 'Leone',
                    'minorUnit' => 2,
                    'numericCode' => 694,
                ],
            'SGD' =>
                [
                    'alphabeticCode' => 'SGD',
                    'currency' => 'Singapore Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 702,
                ],
            'XSU' =>
                [
                    'alphabeticCode' => 'XSU',
                    'currency' => 'Sucre',
                    'minorUnit' => 0,
                    'numericCode' => 994,
                ],
            'SBD' =>
                [
                    'alphabeticCode' => 'SBD',
                    'currency' => 'Solomon Islands Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 90,
                ],
            'SOS' =>
                [
                    'alphabeticCode' => 'SOS',
                    'currency' => 'Somali Shilling',
                    'minorUnit' => 2,
                    'numericCode' => 706,
                ],
            'SSP' =>
                [
                    'alphabeticCode' => 'SSP',
                    'currency' => 'South Sudanese Pound',
                    'minorUnit' => 2,
                    'numericCode' => 728,
                ],
            'LKR' =>
                [
                    'alphabeticCode' => 'LKR',
                    'currency' => 'Sri Lanka Rupee',
                    'minorUnit' => 2,
                    'numericCode' => 144,
                ],
            'SDG' =>
                [
                    'alphabeticCode' => 'SDG',
                    'currency' => 'Sudanese Pound',
                    'minorUnit' => 2,
                    'numericCode' => 938,
                ],
            'SRD' =>
                [
                    'alphabeticCode' => 'SRD',
                    'currency' => 'Surinam Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 968,
                ],
            'SZL' =>
                [
                    'alphabeticCode' => 'SZL',
                    'currency' => 'Lilangeni',
                    'minorUnit' => 2,
                    'numericCode' => 748,
                ],
            'SEK' =>
                [
                    'alphabeticCode' => 'SEK',
                    'currency' => 'Swedish Krona',
                    'minorUnit' => 2,
                    'numericCode' => 752,
                ],
            'CHE' =>
                [
                    'alphabeticCode' => 'CHE',
                    'currency' => 'WIR Euro',
                    'minorUnit' => 2,
                    'numericCode' => 947,
                ],
            'CHW' =>
                [
                    'alphabeticCode' => 'CHW',
                    'currency' => 'WIR Franc',
                    'minorUnit' => 2,
                    'numericCode' => 948,
                ],
            'SYP' =>
                [
                    'alphabeticCode' => 'SYP',
                    'currency' => 'Syrian Pound',
                    'minorUnit' => 2,
                    'numericCode' => 760,
                ],
            'TWD' =>
                [
                    'alphabeticCode' => 'TWD',
                    'currency' => 'New Taiwan Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 901,
                ],
            'TJS' =>
                [
                    'alphabeticCode' => 'TJS',
                    'currency' => 'Somoni',
                    'minorUnit' => 2,
                    'numericCode' => 972,
                ],
            'TZS' =>
                [
                    'alphabeticCode' => 'TZS',
                    'currency' => 'Tanzanian Shilling',
                    'minorUnit' => 2,
                    'numericCode' => 834,
                ],
            'THB' =>
                [
                    'alphabeticCode' => 'THB',
                    'currency' => 'Baht',
                    'minorUnit' => 2,
                    'numericCode' => 764,
                ],
            'TOP' =>
                [
                    'alphabeticCode' => 'TOP',
                    'currency' => 'Pa’anga',
                    'minorUnit' => 2,
                    'numericCode' => 776,
                ],
            'TTD' =>
                [
                    'alphabeticCode' => 'TTD',
                    'currency' => 'Trinidad and Tobago Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 780,
                ],
            'TND' =>
                [
                    'alphabeticCode' => 'TND',
                    'currency' => 'Tunisian Dinar',
                    'minorUnit' => 3,
                    'numericCode' => 788,
                ],
            'TRY' =>
                [
                    'alphabeticCode' => 'TRY',
                    'currency' => 'Turkish Lira',
                    'minorUnit' => 2,
                    'numericCode' => 949,
                ],
            'TMT' =>
                [
                    'alphabeticCode' => 'TMT',
                    'currency' => 'Turkmenistan New Manat',
                    'minorUnit' => 2,
                    'numericCode' => 934,
                ],
            'UGX' =>
                [
                    'alphabeticCode' => 'UGX',
                    'currency' => 'Uganda Shilling',
                    'minorUnit' => 0,
                    'numericCode' => 800,
                ],
            'UAH' =>
                [
                    'alphabeticCode' => 'UAH',
                    'currency' => 'Hryvnia',
                    'minorUnit' => 2,
                    'numericCode' => 980,
                ],
            'AED' =>
                [
                    'alphabeticCode' => 'AED',
                    'currency' => 'UAE Dirham',
                    'minorUnit' => 2,
                    'numericCode' => 784,
                ],
            'USN' =>
                [
                    'alphabeticCode' => 'USN',
                    'currency' => 'US Dollar (Next day]',
                    'minorUnit' => 2,
                    'numericCode' => 997,
                ],
            'UYU' =>
                [
                    'alphabeticCode' => 'UYU',
                    'currency' => 'Peso Uruguayo',
                    'minorUnit' => 2,
                    'numericCode' => 858,
                ],
            'UYI' =>
                [
                    'alphabeticCode' => 'UYI',
                    'currency' => 'Uruguay Peso en Unidades Indexadas (UI]',
                    'minorUnit' => 0,
                    'numericCode' => 940,
                ],
            'UYW' =>
                [
                    'alphabeticCode' => 'UYW',
                    'currency' => 'Unidad Previsional',
                    'minorUnit' => 4,
                    'numericCode' => 927,
                ],
            'UZS' =>
                [
                    'alphabeticCode' => 'UZS',
                    'currency' => 'Uzbekistan Sum',
                    'minorUnit' => 2,
                    'numericCode' => 860,
                ],
            'VUV' =>
                [
                    'alphabeticCode' => 'VUV',
                    'currency' => 'Vatu',
                    'minorUnit' => 0,
                    'numericCode' => 548,
                ],
            'VES' =>
                [
                    'alphabeticCode' => 'VES',
                    'currency' => 'Bolívar Soberano',
                    'minorUnit' => 2,
                    'numericCode' => 928,
                ],
            'VND' =>
                [
                    'alphabeticCode' => 'VND',
                    'currency' => 'Dong',
                    'minorUnit' => 0,
                    'numericCode' => 704,
                ],
            'YER' =>
                [
                    'alphabeticCode' => 'YER',
                    'currency' => 'Yemeni Rial',
                    'minorUnit' => 2,
                    'numericCode' => 886,
                ],
            'ZMW' =>
                [
                    'alphabeticCode' => 'ZMW',
                    'currency' => 'Zambian Kwacha',
                    'minorUnit' => 2,
                    'numericCode' => 967,
                ],
            'ZWL' =>
                [
                    'alphabeticCode' => 'ZWL',
                    'currency' => 'Zimbabwe Dollar',
                    'minorUnit' => 2,
                    'numericCode' => 932,
                ],
            'XBA' =>
                [
                    'alphabeticCode' => 'XBA',
                    'currency' => 'Bond Markets Unit European Composite Unit (EURCO]',
                    'minorUnit' => 0,
                    'numericCode' => 955,
                ],
            'XBB' =>
                [
                    'alphabeticCode' => 'XBB',
                    'currency' => 'Bond Markets Unit European Monetary Unit (E.M.U.-6]',
                    'minorUnit' => 0,
                    'numericCode' => 956,
                ],
            'XBC' =>
                [
                    'alphabeticCode' => 'XBC',
                    'currency' => 'Bond Markets Unit European Unit of Account 9 (E.U.A.-9]',
                    'minorUnit' => 0,
                    'numericCode' => 957,
                ],
            'XBD' =>
                [
                    'alphabeticCode' => 'XBD',
                    'currency' => 'Bond Markets Unit European Unit of Account 17 (E.U.A.-17]',
                    'minorUnit' => 0,
                    'numericCode' => 958,
                ],
            'XTS' =>
                [
                    'alphabeticCode' => 'XTS',
                    'currency' => 'Codes specifically reserved for testing purposes',
                    'minorUnit' => 0,
                    'numericCode' => 963,
                ],
            'XXX' =>
                [
                    'alphabeticCode' => 'XXX',
                    'currency' => 'The codes assigned for transactions where no currency is involved',
                    'minorUnit' => 0,
                    'numericCode' => 999,
                ],
            'XAU' =>
                [
                    'alphabeticCode' => 'XAU',
                    'currency' => 'Gold',
                    'minorUnit' => 0,
                    'numericCode' => 959,
                ],
            'XPD' =>
                [
                    'alphabeticCode' => 'XPD',
                    'currency' => 'Palladium',
                    'minorUnit' => 0,
                    'numericCode' => 964,
                ],
            'XPT' =>
                [
                    'alphabeticCode' => 'XPT',
                    'currency' => 'Platinum',
                    'minorUnit' => 0,
                    'numericCode' => 962,
                ],
            'XAG' =>
                [
                    'alphabeticCode' => 'XAG',
                    'currency' => 'Silver',
                    'minorUnit' => 0,
                    'numericCode' => 961,
                ],
        ];
    }

    /**
     * Get Currency Symbols as Html Entities
     *
     * @return string[]
     */
    public static function symbolsAsHtmlEntities() {
        return [
            'AED' => '&#1583;.&#1573;', // ?
            'AFN' => '&#65;&#102;',
            'ALL' => '&#76;&#101;&#107;',
            'AMD' => '&#1380;',
            'ANG' => '&#402;',
            'AOA' => '&#75;&#122;', // ?
            'ARS' => '&#36;',
            'AUD' => '&#36;',
            'AWG' => '&#402;',
            'AZN' => '&#8380;',
            'BAM' => '&#75;&#77;',
            'BBD' => '&#36;',
            'BDT' => '&#2547;', // ?
            'BGN' => '&#1083;&#1074;',
            'BHD' => '.&#1583;.&#1576;', // ?
            'BIF' => '&#70;&#66;&#117;', // ?
            'BMD' => '&#36;',
            'BND' => '&#36;',
            'BOB' => '&#36;&#98;',
            'BRL' => '&#82;&#36;',
            'BSD' => '&#36;',
            'BTN' => '&#78;&#117;&#46;', // ?
            'BWP' => '&#80;',
            'BYR' => '&#112;&#46;',
            'BZD' => '&#66;&#90;&#36;',
            'CAD' => '&#36;',
            'CDF' => '&#70;&#67;',
            'CHF' => '&#67;&#72;&#70;',
            'CLF' => '&#85;&#70;', // ?
            'CLP' => '&#36;',
            'CNY' => '&#165;',
            'COP' => '&#36;',
            'CRC' => '&#8353;',
            'CUP' => '&#8396;',
            'CVE' => '&#36;', // ?
            'CZK' => '&#75;&#269;',
            'DJF' => '&#70;&#100;&#106;', // ?
            'DKK' => '&#107;&#114;',
            'DOP' => '&#82;&#68;&#36;',
            'DZD' => '&#1583;&#1580;', // ?
            'EGP' => 'E&#163;',
            'ETB' => '&#66;&#114;',
            'EUR' => '&#8364;',
            'FJD' => '&#36;',
            'FKP' => '&#163;',
            'GBP' => '&#163;',
            'GEL' => '&#4314;', // ?
            'GHS' => '&#162;',
            'GIP' => '&#163;',
            'GMD' => '&#68;', // ?
            'GNF' => '&#70;&#71;', // ?
            'GTQ' => '&#81;',
            'GYD' => '&#36;',
            'HKD' => '&#36;',
            'HNL' => '&#76;',
            'HRK' => '&#107;&#110;',
            'HTG' => '&#71;', // ?
            'HUF' => '&#70;&#116;',
            'IDR' => '&#82;&#112;',
            'ILS' => '&#8362;',
            'INR' => '&#8377;',
            'IQD' => '&#1593;.&#1583;', // ?
            'IRR' => '&#65020;',
            'ISK' => '&#107;&#114;',
            'JEP' => '&#163;',
            'JMD' => '&#74;&#36;',
            'JOD' => '&#74;&#68;', // ?
            'JPY' => '&#165;',
            'KES' => '&#75;&#83;&#104;', // ?
            'KGS' => '&#1083;&#1074;',
            'KHR' => '&#6107;',
            'KMF' => '&#67;&#70;', // ?
            'KPW' => '&#8361;',
            'KRW' => '&#8361;',
            'KWD' => '&#1583;.&#1603;', // ?
            'KYD' => '&#36;',
            'KZT' => '&#8376;',
            'LAK' => '&#8365;',
            'LBP' => '&#163;',
            'LKR' => '&#8360;',
            'LRD' => '&#36;',
            'LSL' => '&#76;', // ?
            'LTL' => '&#76;&#116;',
            'LVL' => '&#76;&#115;',
            'LYD' => '&#1604;.&#1583;', // ?
            'MAD' => '&#1583;.&#1605;.', //?
            'MDL' => '&#76;',
            'MGA' => '&#65;&#114;', // ?
            'MKD' => '&#1076;&#1077;&#1085;',
            'MMK' => '&#75;',
            'MNT' => '&#8366;',
            'MOP' => '&#77;&#79;&#80;&#36;', // ?
            'MRO' => '&#85;&#77;', // ?
            'MUR' => '&#8360;', // ?
            'MVR' => '.&#1923;', // ?
            'MWK' => '&#77;&#75;',
            'MXN' => '&#36;',
            'MYR' => '&#82;&#77;',
            'MZN' => '&#77;&#84;',
            'NAD' => '&#36;',
            'NGN' => '&#8358;',
            'NIO' => '&#67;&#36;',
            'NOK' => '&#107;&#114;',
            'NPR' => '&#8360;',
            'NZD' => '&#36;',
            'OMR' => '&#65020;',
            'PAB' => '&#66;&#47;&#46;',
            'PEN' => '&#83;&#47;&#46;',
            'PGK' => '&#75;', // ?
            'PHP' => '&#8369;',
            'PKR' => '&#8360;',
            'PLN' => '&#122;&#322;',
            'PYG' => '&#71;&#115;',
            'QAR' => '&#65020;',
            'RON' => '&#108;&#101;&#105;',
            'RSD' => '&#1044;&#1080;&#1085;&#46;',
            'RUB' => '&#8381;',
            'RWF' => '&#1585;.&#1587;',
            'SAR' => '&#65020;',
            'SBD' => '&#36;',
            'SCR' => '&#8360;',
            'SDG' => '&#163;', // ?
            'SEK' => '&#107;&#114;',
            'SGD' => '&#36;',
            'SHP' => '&#163;',
            'SLL' => '&#76;&#101;', // ?
            'SOS' => '&#83;',
            'SRD' => '&#36;',
            'STD' => '&#68;&#98;', // ?
            'SVC' => '&#36;',
            'SYP' => '&#163;',
            'SZL' => '&#76;', // ?
            'THB' => '&#3647;',
            'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
            'TMT' => '&#109;',
            'TND' => '&#1583;.&#1578;',
            'TOP' => '&#84;&#36;',
            'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
            'TTD' => '&#36;',
            'TWD' => '&#78;&#84;&#36;',
            'TZS' => '&#84;&#83;&#104;',
            'UAH' => '&#8372;',
            'UGX' => '&#85;&#83;&#104;',
            'USD' => '&#36;',
            'UYU' => '&#36;&#85;',
            'UZS' => '&#1083;&#1074;',
            'VEF' => '&#66;&#115;',
            'VND' => '&#8363;',
            'VUV' => '&#86;&#84;',
            'WST' => '&#87;&#83;&#36;',
            'XAF' => '&#70;&#67;&#70;&#65;',
            'XCD' => '&#36;',
            'XDR' => '&#83;&#68;&#82;',
            'XOF' => '&#70;&#67;&#70;&#65;',
            'XPF' => '&#70;',
            'YER' => '&#65020;',
            'ZAR' => '&#82;',
            'ZMK' => '&#90;&#75;', // ?
            'ZWL' => '&#90;&#36;',
        ];
    }
}