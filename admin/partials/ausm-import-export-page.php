<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once( ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . '/admin/partials/header/plugin-header.php' );
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
?>
<div class="ausm-section-left">
    <div class="ausm-main-table res-cl">
        <h2><?php esc_html_e( 'USPS Configuration Import / Export', 'advanced-usps-shipping-method' ); ?></h2>
        <table class="form-table table-outer usps-import-export-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="ausm_status"><?php esc_html_e( 'Export Settings', 'advanced-usps-shipping-method' ); ?></label>
                    </th>
                    <td class="forminp">
                        <p><?php esc_html_e( 'Click the button below to export the settings for this plugin.', 'advanced-usps-shipping-method' ); ?></p>
                        <p class="afrsm_container">
                            <a href="javascript:void(0);" class="button button-primary" id="ausm_export"><?php esc_html_e( 'Export','advanced-usps-shipping-method'); ?></a>
                            <?php echo wp_kses( wc_help_tip( esc_html__( 'Export the settings for this site as a .json file. This allows you to easily import the configuration into another site for this plugin.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                        </p>
                        <div class="ausm_export_msg"></div>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="ausm_status"><?php esc_html_e( 'Import Settings', 'advanced-usps-shipping-method' ); ?></label>
                    </th>
                    <td class="forminp">
                        <p class="afrsm_container">
                            <input type="file" id="ausm_import_file" name="import_file" />
                        </p>
                        <p class="afrsm_container">
                            <a href="javascript:void(0);" class="button button-primary" id="ausm_import"><?php esc_html_e( 'Import','advanced-usps-shipping-method'); ?></a>
                            <?php echo wp_kses( wc_help_tip( esc_html__( 'Import the settings from a .json file. This file can be obtained by exporting the settings from another site using the form above.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                        </p>
                        <div class="ausm_import_msg"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>