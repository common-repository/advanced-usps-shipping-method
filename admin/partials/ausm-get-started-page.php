<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once( ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . '/admin/partials/header/plugin-header.php' );
?>

    <div class="ausm-getting-started res-cl">
        <h2><?php esc_html_e( 'USPS Getting Started Page', 'advanced-usps-shipping-method' ); ?></h2>
        <div class="getting-started-wrap">
            <p class="block ausm-gs-title"><?php esc_html_e( 'Getting Started', 'advanced-usps-shipping-method' ); ?></p>
            <p class="block ausm-gs-text">
                <?php esc_html_e( 'Connect with USPS API and fetch shipping services rate.', 'advanced-usps-shipping-method' ); ?>
            </p>
            <p class="block ausm-gs-text">
                <?php esc_html_e( 'Below are the basic steps to configure plugin:', 'advanced-usps-shipping-method' ); ?>
            </p>
            <p class="block ausm-gs-step">
                <?php echo sprintf( wp_kses( __( '<strong>Step 1: </strong>Enable this option to show USPS Shipping Methods on cart/checkout page.', 'advanced-usps-shipping-method' ) , array( 'strong' => array() ) ) ); ?>
                <span class="gettingstarted">
                    <img src="<?php echo esc_url( ADVANCED_USPS_SHIPPING_METHOD_URL . 'admin/images/getting-started/Screenshot-1.png' ); ?>">
                </span>
            </p>
            <p class="block ausm-gs-step">
                <?php echo sprintf( wp_kses( __( '<strong>Step 2: </strong>Add USPS Shipping Account ID which helps you to connect with USPS services.', 'advanced-usps-shipping-method' ) , array( 'strong' => array() ) ) ); ?>
                <span class="gettingstarted">
                    <img src="<?php echo esc_url( ADVANCED_USPS_SHIPPING_METHOD_URL . 'admin/images/getting-started/Screenshot-2.png' ); ?>">
                </span>
            </p>
            <p class="block ausm-gs-step">
                <?php echo sprintf( wp_kses( __( '<strong>Step 3: </strong>Simply click on the "Check Connection" button and you will get notified about USPS account user ID whether is connected or not.', 'advanced-usps-shipping-method' ) , array( 'strong' => array() ) ) ); ?>
                <span class="gettingstarted">
                    <img src="<?php echo esc_url( ADVANCED_USPS_SHIPPING_METHOD_URL . 'admin/images/getting-started/Screenshot-3.png' ); ?>">
                </span>
            </p>
            <p class="block ausm-gs-step">
                <?php echo sprintf( wp_kses( __( '<strong>Step 4: </strong>Now, you can save the configuration settings and check it on cart/checkout page.', 'advanced-usps-shipping-method' ) , array( 'strong' => array() ) ) ); ?>
            </p>
            <p class="block ausm-gs-step">
                <?php echo sprintf( wp_kses( __( 'Thank you for choosing out plugin.', 'advanced-usps-shipping-method' ) , array( 'strong' => array() ) ) ); ?>
            </p>
        </div>
    </div>
</div>
</div>
</div>
</div>