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
                <h2>Sign in into account</h2>
                <table class="form-table">
                  <tbody>
                    <tr>
                      <th scope="row"><label for="email">Email:</label></th>
                      <td>
                        <input name="email" type="email" ng-model="accountEmail" id="account_email" class="regular-text">
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label for="password">Password</label></th>
                      <td>
                        <input name="password" type="password" ng-model="accountPassword" id="account_password" class="regular-text">
                      </td>
                    </tr>
                  </tbody>
                </table>
                <p class="submit">
                  <button class="button button-primary" ng-click="loginToAccount()">Sign in</button>
                </p>
            </div>
            </div>
            <div class="images_window" ng-hide="isTokenStored == false" ng-class="isLoading ? 'loading' : ''">
              <div style="display: block;margin: 10px auto;padding-left: 5px;">
                <span class="nav_item" ng-click="getItems()" ng-class="{active: (selectedMode == 'favorites')}">Favorites <small>{{favoritesSize}}</small></span>
                <span class="nav_item" ng-repeat="c in collectionsList" ng-click="selectCollection(c)" ng-class="{active: (selectedCollectionId == c.id)}">{{c.title}}<small>{{c.items_count}}</small></span>
              </div>
              <div deckgrid source="itemsList" class="deckgrid">
                  <div class="a-card" ng-class="card.loading ? 'loading' : ''">
                      <img width="{{card.preview_width}}" height="{{card.preview_height}}" style="background-color: {{card.primary_color}}" src="{{card.preview_url}}" alt="{{card.description}}" data-ng-click="mother.selectItem(card)">
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
