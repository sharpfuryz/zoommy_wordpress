<?php
namespace CustomImageImporter\Admin;

class RegisterScriptAndStyle
{
    private static $instance = false;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        \add_action(
            'admin_enqueue_scripts',
            array(
                $this,
                'loadAdminStyleScript'
            )
        );
    }

    public function loadAdminStyleScript($hook)
    {
        if ($hook !== 'tools_page_custom-image-importer') {
            return;
        }

        if (\defined('CustomImageImporterUrl') && CustomImageImporterUrl !== '') {

            $assetsPath = CustomImageImporterUrl . 'admin/assets';

            \wp_enqueue_style(
                'custom-image-importer.css',
                $assetsPath . '/css/admin.css'
            );
            \wp_enqueue_script(
                'custom-image-importer-angular-min.js',
                $assetsPath . '/js/angular.min.js'
            );
            \wp_enqueue_script(
                'custom-image-importer-angular-deckgrid.js',
                $assetsPath . '/js/angular-deckgrid.js'
            );
            \wp_enqueue_script(
                'custom-image-importer-angular-controller.js',
                $assetsPath . '/js/controller.js'
            );
        }
    }
}
