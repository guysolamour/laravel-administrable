<?php


if (!function_exists('translate_model_field')) {
    function translate_model_field(string $field_name, ?string $field_trans, ?string $locale = null, ?array $replace = []): string
    {
        if ($field_trans) return $field_trans;


        return trans_fb("validation.attributes.{$field_name}", $field_name, $locale, $replace);
    }
}

if (!function_exists('trans_fb')) {
    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @param string $key
     * @param null|string $fallback
     * @param null|string $locale
     * @param array|null $replace
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function trans_fb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        if (\Illuminate\Support\Facades\Lang::has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        return $fallback;
    }
}


if (!function_exists('delete_all_between')) {
    function delete_all_between(string $beginning, string $end, string $string): string
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
    }

}
