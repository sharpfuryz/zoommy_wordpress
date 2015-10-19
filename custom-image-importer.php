<?php
/*
Plugin Name: Zoommy
Description: Import your liked photos from app to your wordpress instantly
Version: 1.0
*/

namespace CustomImageImporter;

if (!defined('ABSPATH')) {
    exit();
}

define('CustomImageImporterUrl', plugin_dir_url(__FILE__));

if (\is_admin()) {
    include_once __DIR__ . '/admin/Helper.php';
    Admin\Helper::getInstance();

    include_once __DIR__ . '/admin/RegisterScriptAndStyle.php';
    Admin\RegisterScriptAndStyle::getInstance();

    include_once __DIR__ . '/admin/UtilityPage.php';
    Admin\UtilityPage::getInstance();
}

\register_activation_hook(
    __FILE__,
    array(
        '\CustomImageImporter\Admin\Helper',
        'activation'
    )
);
