<?php

if (!function_exists('validate_phone')) {
    function validate_phone($phone)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $parse = $phoneUtil->parse($phone);
            $country_code = $parse->getCountryCode();
            $phoneNumber = $parse->getNationalNumber();
            return "+" . $country_code . $phoneNumber;
        } catch (\libphonenumber\NumberParseException $e) {
            return $phone;
        }
    }
}


if (!function_exists('format_phone')) {
    function format_phone($phone)
    {
        $result = [
            'country_code' => null,
            'national_number' => null
        ];
        try {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $parse = $phoneUtil->parse($phone);
            $country_code = $parse->getCountryCode();
            $national_number = $parse->getNationalNumber();
            $result['country_code'] = $country_code;
            $result['national_number'] = $national_number;
            $result['phone'] = "+" . $country_code . $national_number;
        } catch (\libphonenumber\NumberParseException $e) {
            $result['phone'] = $phone;
        }
        return $result;
    }
}
