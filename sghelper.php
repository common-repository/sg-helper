<?php
/**
 * Plugin Name: SG Helper
 * Description: Plugin zum Deaktivieren des Dateitypen-Checks beim Dateiupload für Administratoren. 🤘 
 * Version: 1.0
 * Author: Markus Burgthaler @ sirconic group GmbH
 * Author URI: https://www.sirconic-group.de
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

// Erlaube es Administratoren, alle Dateitypen hochzuladen
function sg_helper_allow_unfiltered_upload( $caps, $cap, $user_id, $args ) {
    if ( 'unfiltered_upload' === $cap && user_can( $user_id, 'administrator' ) ) {
        $caps = array( 'unfiltered_upload' );
    }
    return $caps;
}
add_filter( 'map_meta_cap', 'sg_helper_allow_unfiltered_upload', 10, 4 );

// Deaktiviere den Dateitypen-Check für Uploads (nur für Administratoren)
function sg_helper_disable_upload_mime_check( $data, $file, $filename, $mimes, $real_mime ) {
    if ( current_user_can( 'administrator' ) ) {
        $wp_filetype = wp_check_filetype( $filename, $mimes );
        $ext = $wp_filetype['ext'];
        $type = $wp_filetype['type'];
        $proper_filename = $data['proper_filename'];

        if ( !$type ) {
            $data['ext'] = $ext;
            $data['type'] = $real_mime;
            $data['proper_filename'] = $proper_filename;
        }
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'sg_helper_disable_upload_mime_check', 10, 5);

// Erlaube allen Dateitypen das Hochladen (nur für Administratoren)
function sg_helper_allow_all_file_types( $mime_types ) {
    if ( current_user_can( 'administrator' ) ) {
        $mime_types['*'] = 'application/octet-stream';
    }
    return $mime_types;
}
add_filter('upload_mimes', 'sg_helper_allow_all_file_types');
