<?php

if (!function_exists('get_admin')) {
    function get_admin(?string $field = null) {
        if ( is_null( $field ) ) {
            return auth()->guard( 'admin' )->user();
        }

        return auth()->guard( 'admin' )->user()->$field;
    }

}

if (!function_exists('current_admin_is_super_admin')) {
    function current_admin_is_super_admin() : bool
    {
        return (bool) get_admin( 'is_super_admin' );
    }
}

if (!function_exists('current_admin_profil')) {
    function current_admin_profil($admin) : bool
    {
        return get_admin('id') == $admin->id;
    }
}

if (!function_exists('get_admin_role')) {
    function get_admin_role($admin) : string
    {
        return $admin->isSuperAdmin() ? 'Super administrateur' : 'Administrateur';
    }
}

if (!function_exists('get_current_admin_role')) {
    function get_current_admin_role() : string
    {
        return get_admin_role(get_admin());
    }
}

if (!function_exists('translate_model_field')) {
    function translate_model_field(string $field_name, ?string $field_trans, ?string $locale = null, ?array $replace = []) : string
    {
        if($field_trans) return $field_trans;


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
