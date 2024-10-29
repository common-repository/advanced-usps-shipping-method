<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$ausm_admin_object = new Advanced_Usps_Shipping_Method_Admin(ADVANCED_USPS_SHIPPING_METHOD_SLUG, ADVANCED_USPS_SHIPPING_METHOD_VERSION);
?>
<div id="dotsstoremain">
    <div class="all-pad">
        <header class="dots-header">
            <div class="dots-plugin-details">
                <div class="dots-header-left">
                    <div class="dots-logo-main">
                        <img src="<?php 
echo esc_url( ADVANCED_USPS_SHIPPING_METHOD_LOGO );
?>" alt="<?php 
esc_attr_e( 'Plugin\'s logo', 'advanced-usps-shipping-method' );
?>">
                    </div>
                    <div class="plugin-name">
                        <div class="title"><?php 
esc_html_e( ADVANCED_USPS_SHIPPING_METHOD_NAME, 'advanced-usps-shipping-method' );
?></div>
                    </div>
                    <span class="version-label"><?php 
echo esc_html( ADVANCED_USPS_SHIPPING_METHOD_VERSION_TYPE );
?></span>
                    <span class="version-number">v<?php 
echo esc_html( ADVANCED_USPS_SHIPPING_METHOD_VERSION );
?></span>
                </div>
                <div class="dots-header-right">
                        <div class="button-dots">
                            <a target="_blank" href="<?php 
echo esc_url( 'http://www.thedotstore.com/support/' );
?>">
                                <?php 
esc_html_e( 'Support', 'advanced-usps-shipping-method' );
?>
                            </a>
                        </div>
                        <div class="button-dots">
                            <a target="_blank" href="<?php 
echo esc_url( 'https://www.thedotstore.com/feature-requests/' );
?>">
                                <?php 
esc_html_e( 'Suggest', 'advanced-usps-shipping-method' );
?>
                            </a>
                        </div>
                        <div class="button-dots <?php 
echo ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ? '' : 'last-link-button' );
?>">
                            <a target="_blank" href="<?php 
echo esc_url( 'https://docs.thedotstore.com/collection/478-advanced-usps-shipping-method' );
?>">
                                <?php 
esc_html_e( 'Help', 'advanced-usps-shipping-method' );
?>
                            </a>
                        </div>

                        <?php 
if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
    ?>
                                <div class="button-dots">
                                    <a target="_blank" href="<?php 
    echo esc_url( ausm_fs()->get_account_url() );
    ?>">
                                        <?php 
    esc_html_e( 'My Account', 'advanced-usps-shipping-method' );
    ?>
                                    </a>
                                </div>
                        <?php 
} else {
    ?>
                                <div class="button-dots">
                                        <a target="_blank" class="dots-upgrade-btn" href="<?php 
    echo esc_url( ausm_fs()->get_upgrade_url() );
    ?>">
                                            <?php 
    esc_html_e( 'Upgrade', 'advanced-usps-shipping-method' );
    ?>
                                        </a>
                                </div> 
                        <?php 
}
?>
                    
                </div>
            </div>
			<?php 
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$ausm_admin_object->ausm_pro_menus( $current_page );
$menu_page = $current_page;
$dpad_getting_started = ( isset( $menu_page ) && $menu_page === 'ausm-get-started' ? 'active' : '' );
$dpad_information = ( isset( $menu_page ) && $menu_page === 'ausm-information' ? 'active' : '' );
$dpad_licenses = ( isset( $menu_page ) && $menu_page === 'ausm-get-started-account' || $menu_page === 'advanced-usps-shipping-method-account' ? 'active' : '' );
$dpad_settings_menu = ( isset( $menu_page ) && ('ausm-import-export' === $menu_page || 'ausm-get-started' === $menu_page || 'ausm-information' === $menu_page || 'wcdrfc-page-general-settings' === $menu_page) || 'ausm-get-started-account' === $menu_page || 'advanced-usps-shipping-method-account' === $menu_page ? 'active' : '' );
$dpad_display_submenu = ( !empty( $dpad_settings_menu ) && 'active' === $dpad_settings_menu ? 'display:inline-block' : 'display:none' );
if ( is_network_admin() ) {
    $admin_url = admin_url();
} else {
    $admin_url = admin_url( 'admin.php' );
}
?>
        </header>
        <div class="dots-settings-inner-main">
            <div class="ausm-section-left">
                <div class="dotstore-submenu-items" style="<?php 
echo esc_attr( $dpad_display_submenu );
?>">
                    <ul>
                        <?php 
?>
                        <li><a class="<?php 
echo esc_attr( $dpad_getting_started );
?>" href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'ausm-get-started',
), $admin_url ) );
?>"><?php 
esc_html_e( 'About', 'advanced-usps-shipping-method' );
?></a></li>
                        <li><a class="<?php 
echo esc_attr( $dpad_information );
?>" href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'ausm-information',
), $admin_url ) );
?>"><?php 
esc_html_e( 'Quick info', 'advanced-usps-shipping-method' );
?></a></li>
                        <?php 
if ( !(ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code()) ) {
    ?>
                                <li>
                                    <a class="<?php 
    echo esc_attr( $dpad_licenses );
    ?>" href="<?php 
    echo esc_url( ausm_fs()->get_account_url() );
    ?>"><?php 
    esc_html_e( 'Account', 'advanced-usps-shipping-method' );
    ?></a>
                                </li>
                                <?php 
}
?>
                        <li><a href="<?php 
echo esc_url( 'https://www.thedotstore.com/plugins/' );
?>" target="_blank"><?php 
esc_html_e( 'Shop Plugins', 'advanced-usps-shipping-method' );
?></a></li>
                    </ul>
                </div>