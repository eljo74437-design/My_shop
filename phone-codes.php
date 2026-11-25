<?php
/* =====================================================
   PHONE COUNTRY CODES WE ACCEPT
   Only Europe, South America and Asia (the places we
   deliver to). To add or remove a country, edit the
   lists below: 'Country name' => 'calling code'
   ===================================================== */

$PHONE_CODES = [

    'Europe' => [
        'Albania' => '355', 'Andorra' => '376', 'Austria' => '43', 'Belarus' => '375',
        'Belgium' => '32', 'Bosnia and Herzegovina' => '387', 'Bulgaria' => '359',
        'Croatia' => '385', 'Cyprus' => '357', 'Czechia' => '420', 'Denmark' => '45',
        'Estonia' => '372', 'Finland' => '358', 'France' => '33', 'Germany' => '49',
        'Greece' => '30', 'Hungary' => '36', 'Iceland' => '354', 'Ireland' => '353',
        'Italy' => '39', 'Kosovo' => '383', 'Latvia' => '371', 'Liechtenstein' => '423',
        'Lithuania' => '370', 'Luxembourg' => '352', 'Malta' => '356', 'Moldova' => '373',
        'Monaco' => '377', 'Montenegro' => '382', 'Netherlands' => '31',
        'North Macedonia' => '389', 'Norway' => '47', 'Poland' => '48', 'Portugal' => '351',
        'Romania' => '40', 'Russia' => '7', 'San Marino' => '378', 'Serbia' => '381',
        'Slovakia' => '421', 'Slovenia' => '386', 'Spain' => '34', 'Sweden' => '46',
        'Switzerland' => '41', 'Ukraine' => '380', 'United Kingdom' => '44',
        'Vatican City' => '39',
    ],

    'South America' => [
        'Argentina' => '54', 'Bolivia' => '591', 'Brazil' => '55', 'Chile' => '56',
        'Colombia' => '57', 'Ecuador' => '593', 'Guyana' => '592', 'Paraguay' => '595',
        'Peru' => '51', 'Suriname' => '597', 'Uruguay' => '598', 'Venezuela' => '58',
    ],

    'Asia' => [
        'Afghanistan' => '93', 'Armenia' => '374', 'Azerbaijan' => '994', 'Bahrain' => '973',
        'Bangladesh' => '880', 'Bhutan' => '975', 'Brunei' => '673', 'Cambodia' => '855',
        'China' => '86', 'Georgia' => '995', 'Hong Kong' => '852', 'India' => '91',
        'Indonesia' => '62', 'Iran' => '98', 'Iraq' => '964', 'Israel' => '972',
        'Japan' => '81', 'Jordan' => '962', 'Kazakhstan' => '7', 'Kuwait' => '965',
        'Kyrgyzstan' => '996', 'Laos' => '856', 'Lebanon' => '961', 'Malaysia' => '60',
        'Maldives' => '960', 'Mongolia' => '976', 'Myanmar' => '95', 'Nepal' => '977',
        'North Korea' => '850', 'Oman' => '968', 'Pakistan' => '92', 'Palestine' => '970',
        'Philippines' => '63', 'Qatar' => '974', 'Saudi Arabia' => '966', 'Singapore' => '65',
        'South Korea' => '82', 'Sri Lanka' => '94', 'Syria' => '963', 'Taiwan' => '886',
        'Tajikistan' => '992', 'Thailand' => '66', 'Timor-Leste' => '670', 'Turkey' => '90',
        'Turkmenistan' => '993', 'United Arab Emirates' => '971', 'Uzbekistan' => '998',
        'Vietnam' => '84', 'Yemen' => '967',
    ],
];

/* Is this a full phone number like +447911123456 with an accepted code? */
function phone_is_allowed($phone) {
    global $PHONE_CODES;
    $phone = preg_replace('/[\s\-().]/', '', (string)$phone);
    if (!preg_match('/^\+(\d{7,15})$/', $phone, $m)) return false;
    $digits = $m[1];
    foreach ($PHONE_CODES as $list) {
        foreach ($list as $code) {
            if (strpos($digits, $code) === 0) {
                $local = strlen($digits) - strlen($code);
                if ($local >= 6 && $local <= 12) return true;
            }
        }
    }
    return false;
}