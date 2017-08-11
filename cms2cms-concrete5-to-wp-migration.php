<?php
/*
    Plugin Name: CMS2CMS Concrete5 to WordPress migration
    Plugin URI: http://www.cms2cms.com
    Description: Migrate your website content from Concrete5 to WordPress easily and automatedly in just a few simple steps.
    Version: 3.7.0
    Author: MagneticOne
    Author URI: http://magneticone.com
    License: GPLv2
*/
/*  Copyright 2013  MagneticOne  (email : contact@magneticone.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once 'includes/cms2cms-functions.php';
include_once 'includes/cms2cms-bridge-loader.php';

define( 'CMS2CMS_VERSION', '3.7.0' );

/* ****************************************************** */

function cms2cms_concrete5_plugin_menu() {
    $viewProvider = new CmsPluginFunctionsConcrete5();
    add_plugins_page(
        $viewProvider->getPluginNameLong(),
        $viewProvider->getPluginNameShort(),
        'activate_plugins',
        'cms2cms-concrete5-migration',
        'cms2cms_concrete5_menu_page'
    );
}
add_action('admin_menu', 'cms2cms_concrete5_plugin_menu');

function cms2cms_concrete5_menu_page(){
    include 'includes/cms2cms-view.php';
}

function cms2cms_concrete5_plugin_init() {
    load_plugin_textdomain( 'cms2cms-concrete5-migration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'cms2cms_concrete5_plugin_init');

function cms2cms_concrete5_install() {
    $dataProvider = new CmsPluginFunctionsConcrete5();
    $dataProvider->install();
}
register_activation_hook( __FILE__, 'cms2cms_concrete5_install' );


/* ******************************************************* */
/* Assets */
/* ******************************************************* */

function cms2cms_concrete5_wp_admin_style() {
    $dataProvider = new CmsPluginFunctionsConcrete5();

    wp_register_style( 'cms2cms-concrete5-admin-css', $dataProvider->getFrontUrl() . 'css/cms2cms.css', false, CMS2CMS_VERSION );
    wp_enqueue_style( 'cms2cms-concrete5-admin-css' );

    wp_register_script( 'cms2cms-concrete5-jsonp', $dataProvider->getFrontUrl() . 'js/jsonp.js', false, CMS2CMS_VERSION );
    wp_enqueue_script( 'cms2cms-concrete5-jsonp' );

    wp_register_script( 'cms2cms-concrete5-admin-js', $dataProvider->getFrontUrl() . 'js/cms2cms.js', array('jquery', 'cms2cms-concrete5-jsonp'), CMS2CMS_VERSION );

    load_plugin_textdomain( 'cms2cms-migration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    $fileStart = 'cms2cms-migration';
    $translation_array = array(
        'Unknown error, please contact us http://www.cms2cms.com/contacts/' => __('Unknown error, please contact us http://www.cms2cms.com/contacts/', $fileStart),
        'New version' => __('New version', $fileStart),
        'Something gone wrong, please try again later.' => __('Something gone wrong, please try again later.', $fileStart),
        'Mapping options:' => __('Mapping options:', $fileStart),
        'Additional options:' => __('Additional options:', $fileStart)
    );
    wp_localize_script( 'cms2cms-concrete5-admin-js', 'localization_cms_j', $translation_array );
    wp_enqueue_script( 'cms2cms-concrete5-admin-js' );
}
add_action( 'admin_enqueue_scripts', 'cms2cms_concrete5_wp_admin_style' );


/* ******************************************************* */
/* AJAX */
/* ******************************************************* */

/**
 * Save Access key and email
 */
function cms2cms_concrete5_save_options() {
    $dataProvider = new CmsPluginFunctionsConcrete5();
    $response = $dataProvider->saveOptions();

    echo json_encode($response);
    die(); // this is required to return a proper result
}
add_action('wp_ajax_cms2cms_concrete5_save_options', 'cms2cms_concrete5_save_options');

/**
 * Get auth string
 */

function cms2cms_concrete5_get_options() {
    $dataProvider = new CmsPluginFunctionsConcrete5();
    $response = $dataProvider->getOptions();

    echo json_encode($response);
    die(); // this is required to return a proper result
}
add_action('wp_ajax_cms2cms_concrete5_get_options', 'cms2cms_concrete5_get_options');

