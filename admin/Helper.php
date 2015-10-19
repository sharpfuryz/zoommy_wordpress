<?php
namespace CustomImageImporter\Admin;

class Helper
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
            'wp_ajax_customImageImporterSaveToken',
            array(
                $this,
                'actionSaveToken'
            )
        );

        \add_action(
            'wp_ajax_customImageImporterSaveImage',
            array(
                $this,
                'actionSaveImage'
            )
        );
    }

    public function actionSaveToken()
    {
        if (!empty($_POST['token'])) {
            \update_option('custom-image-importer-token', \esc_attr(\strip_tags($_POST['token'])));

            \wp_send_json(
                array(
                    'status' => 'success'
                )
            );
        }

        \wp_send_json(
            array(
                'status' => 'error'
            )
        );
    }

    public function actionSaveImage()
    {
        if (!empty($_POST['imageUrl'])) {
            $url = \parse_url($_POST['imageUrl']);

            if ($curlDescriptor = \curl_init($_POST['imageUrl'])) {
                \curl_setopt($curlDescriptor, CURLOPT_HEADER, 0);
                \curl_setopt($curlDescriptor, CURLOPT_RETURNTRANSFER, 1);
                \curl_setopt($curlDescriptor, CURLOPT_BINARYTRANSFER, 1);
                $rawImage = \curl_exec($curlDescriptor);
                \curl_close($curlDescriptor);

                if ($rawImage) {
                    include_once ABSPATH . 'wp-admin/includes/image.php';
                    include_once ABSPATH . 'wp-admin/includes/file.php';
                    include_once ABSPATH . 'wp-admin/includes/media.php';

                    $wpFileType = \wp_check_filetype(\basename($url['path']), null);

                    $tmpDir = \ini_get('upload_tmp_dir') ? \ini_get('upload_tmp_dir') : \sys_get_temp_dir();

                    $tempName = $tmpDir . '/' . \uniqid() . '.' . $wpFileType['ext'];
                    \file_put_contents($tempName, $rawImage);

                    $_FILES['async-upload'] = array(
                        'name' => \trim(\str_replace(' ', '', basename($tempName))),
                        'type' => $wpFileType['type'],
                        'tmp_name' => $tempName,
                        'error' => 0,
                        'size' => \filesize($tempName)
                    );

                    \media_handle_upload(
                        'async-upload',
                        0,
                        array(),
                        array(
                            'test_form' => false,
                            'action' => 'upload-attachment'
                        )
                    );

                    \wp_send_json(
                        array(
                            'status' => 'success'
                        )
                    );
                }
            }
        }

        \wp_send_json(
            array(
                'status' => 'error'
            )
        );
    }

    public function activation()
    {
        $role = \get_role('administrator');
        $role->add_cap('use_custom_image_importer_page');
    }
}
