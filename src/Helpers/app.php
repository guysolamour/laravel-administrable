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
