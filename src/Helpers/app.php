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
