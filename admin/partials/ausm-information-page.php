<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once( ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . '/admin/partials/header/plugin-header.php' );
?>
<div class="ausm-section-left">
    <div class="ausm-main-table res-cl">
        <h2><?php esc_html_e( 'USPS Plugin Quick Information', 'advanced-usps-shipping-method' ); ?></h2>
        <table class="form-table table-outer">
                <tbody>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Product Type', 'advanced-usps-shipping-method' ); ?></td>
                    <td class="fr-2"><?php esc_html_e( 'WooCommerce Plugin', 'advanced-usps-shipping-method' ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Product Name', 'advanced-usps-shipping-method' ); ?></td>
                    <td class="fr-2"><?php esc_html_e( ADVANCED_USPS_SHIPPING_METHOD_NAME, 'advanced-usps-shipping-method' ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Installed Version', 'advanced-usps-shipping-method' ); ?></td>
                    <td class="fr-2"><?php esc_html_e( ADVANCED_USPS_SHIPPING_METHOD_VERSION_TYPE, 'advanced-usps-shipping-method' ); ?>&nbsp;<?php echo esc_html_e( ADVANCED_USPS_SHIPPING_METHOD_VERSION, 'advanced-usps-shipping-method' ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'License & terms of use', 'advanced-usps-shipping-method' ); ?></td>
                    <td class="fr-2">
                        <a target="_blank" href="<?php echo esc_url( 'www.thedotstore.com/terms-and-conditions' ); ?>"><?php esc_html_e( 'Click here', 'advanced-usps-shipping-method' ); ?></a>
                        <?php esc_html_e( 'to view license and terms of use.', 'advanced-usps-shipping-method' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Help & Support', 'advanced-usps-shipping-method' ); ?></td>
                    <td class="fr-2">
                        <ul>
                            <li>
                                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'ausm-get-started' ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Quick Start', 'advanced-usps-shipping-method' ); ?></a>
                            </li>
                            <li><a target="_blank"
                                   href="<?php echo esc_url( 'https://docs.thedotstore.com/collection/478-advanced-usps-shipping-method' ); ?>"><?php esc_html_e( 'Guide Documentation', 'advanced-usps-shipping-method' ); ?></a>
                            </li>
                            <li><a target="_blank"
                                   href="<?php echo esc_url( 'www.thedotstore.com/support' ); ?>"><?php esc_html_e( 'Support Forum', 'advanced-usps-shipping-method' ); ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Localization', 'advanced-usps-shipping-method' ); ?></td>
                    <td class="fr-2"><?php esc_html_e( 'English, German', 'advanced-usps-shipping-method' ); ?></td>
                </tr>

                </tbody>
            </table>
    </div>
</div>
</div>
</div>
</div>
</div>