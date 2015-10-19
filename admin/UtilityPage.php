<?php
namespace CustomImageImporter\Admin;

class UtilityPage
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
        if (!\function_exists('curl_init')) {
            \add_action(
                'admin_notices',
                array(
                    $this,
                    'adminNoticeRequired'
                )
            );
        } else {
            \add_action(
                'admin_menu',
                array(
                    $this,
                    'addAdminMenuItems'
                )
            );
        }
    }

    public function addAdminMenuItems()
    {
        \add_submenu_page(
            'tools.php',
            'Zoommy',
            'Zoommy',
            'use_custom_image_importer_page',
            'custom-image-importer',
            array(
                $this,
                'pluginUtilityPage'
            )
        );
    }

    public function pluginUtilityPage()
    {
        $token = \htmlspecialchars(\get_option('custom-image-importer-token'));
        ?>
          <div ng-App="zoommyApp">
            <script>
                window.zoommy_token = "<?php echo $token; ?>";
                window.ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php';
            </script>
            <div ng-controller="imagesController" data-ng-init="init()">
            <div class="token_window" ng-show="isTokenStored == false">
              <div class="wrap">
                <h2>Access token</h2>
                <table class="form-table">
                  <tbody>
                    <tr>
                      <th scope="row"><label for="token">Zoommy wordpress token</label></th>
                      <td>
                        <input name="token" type="text" ng-model="tokenModel" id="token" class="regular-text">
                        <p class="description" id="tagline-description">You can one at <a href="http://zoommyapp.com" target="_blank">account page</a></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <p class="submit">
                  <button class="button button-primary" ng-click="saveToken()">Save</button>
                </p>
            </div>
            </div>
            <div class="images_window" ng-hide="isTokenStored == false" ng-class="isLoading ? 'loading' : ''">
              <div deckgrid source="itemsList" class="deckgrid">
                  <div class="a-card" ng-class="card.loading ? 'loading' : ''">
                      <img width="{{card.preview_width}}" height="{{card.preview_height}}" style="background-color: {{card.primary_color}}" src="data:image/jpg;base64,{{card.base64}}" alt="{{card.description}}" data-ng-click="mother.selectItem(card)">
                  </div>
              </div>
            </div>

          </div>
        </div>
        <?php
    }

    public function adminNoticeRequired()
    {
        ?>
        <div class="error">
            <p>
                <strong>Zoommy</strong> requires curl to work properly
            </p>
        </div>
        <?php
    }
}
