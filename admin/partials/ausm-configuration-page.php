<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once( ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH . '/admin/partials/header/plugin-header.php' );
?>
<div class="ausm-section-left">
    <?php 
    $getPage = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $getPage = ( !empty($getPage) ? $getPage : '' );
    $getSuccess = filter_input( INPUT_GET, 'success', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $getSuccess = ( !empty($getSuccess) ? $getSuccess : '' );
    $getError = filter_input( INPUT_GET, 'error', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $getError = ( !empty($getError) ? $getError : '' );

    if( !empty($getPage) && 'advanced-usps-shipping-method' === $getPage ){
        if( !empty($getError) && $getError ){
            echo wp_kses( __( '<div class="notice notice-error is-dismissible"><p>There is some error occur. Please try again.</p></div>', 'advanced-usps-shipping-method' ), array(
            		'div' => array(
            			'class' => array()
            		),
            		'p' => array(),
            	));
        }
        if( !empty($getSuccess) && $getSuccess ){
            echo wp_kses( __( '<div class="notice notice-success is-dismissible"><p>USPS Configuration has been successfully stored.</p></div>', 'advanced-usps-shipping-method' ), array(
            		'div' => array(
            			'class' => array()
            		),
            		'p' => array(),
            	));
        }
    }
	$services 			= include ADVANCED_USPS_SHIPPING_METHOD_DIR_PATH.'includes/data-ausm-services.php' ;
	$ausm_config 		= !empty(get_option('ausm_config')) ? get_option('ausm_config') : array();
	$ausm_services		= ( isset($ausm_config['ausm_services']) && !empty($ausm_config['ausm_services']) ) ? $ausm_config['ausm_services'] : array();

	$ausm_status 		= ( isset($ausm_config['ausm_status']) && !empty($ausm_config['ausm_status']) ) ? $ausm_config['ausm_status'] : 'off';
	$ausm_debug_mode 	= ( isset($ausm_config['ausm_debug_mode']) && !empty($ausm_config['ausm_debug_mode']) ) ? $ausm_config['ausm_debug_mode'] : 'off';
	$ausm_user_id		= ( isset($ausm_config['ausm_user_id']) && !empty($ausm_config['ausm_user_id']) ) ? $ausm_config['ausm_user_id'] : '';
	$ausm_origin		= ( isset($ausm_config['ausm_origin']) && !empty($ausm_config['ausm_origin']) ) ? $ausm_config['ausm_origin'] : '';
	$ausm_service_type	= ( isset($ausm_config['ausm_service_type']) && !empty($ausm_config['ausm_service_type']) ) ? $ausm_config['ausm_service_type'] : '';
	$ausm_default_title = ( isset($ausm_config['ausm_default_title']) && !empty($ausm_config['ausm_default_title']) ) ? $ausm_config['ausm_default_title'] : '';
	$ausm_default_rate  = ( isset($ausm_config['ausm_default_rate']) && !empty($ausm_config['ausm_default_rate']) ) ? $ausm_config['ausm_default_rate'] : '';
	$ausm_cheapest_rate = ( isset($ausm_config['ausm_cheapest_rate']) && !empty($ausm_config['ausm_cheapest_rate']) ) ? $ausm_config['ausm_cheapest_rate'] : 'off';

	$pro_tag = "";
	if ( !ausm_fs()->is__premium_only() || !ausm_fs()->can_use_premium_code() ) { 
		$pro_tag = esc_html__( 'Premium', 'advanced-usps-shipping-method' ); 
	}
	$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];	
    ?>
    <div class="ausm-main-table res-cl">
        <h2><?php esc_html_e( 'USPS Shipping Method Configuration', 'advanced-usps-shipping-method' ); ?></h2>
        <form method="POST" name="usps_setting_form" action="<?php esc_url( get_admin_url() ); ?>admin-post.php" enctype="multipart/form-data" novalidate="novalidate">
            <input type='hidden' name='action' value='submit_form_ausm'/>
            <?php wp_nonce_field( 'ausm_save_action', 'ausm_configuration_save' ); ?>
            <table class="form-table table-outer usps-shipping-method-table">
                <tbody>
                    <tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_status"><?php esc_html_e( 'Status', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
							<label class="switch">
								<input type="checkbox" id="ausm_status" name="ausm_status" <?php checked( $ausm_status, 'on' ); ?> />
								<div class="slider round"></div>
							</label>
							<?php echo wp_kses( wc_help_tip( esc_html__( 'Enable or Disable the USPS shipping method using this button (USPS method will be visible to customers only if this option is enabled).', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
                    <tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_debug_mode"><?php esc_html_e( 'Debug Mode', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
							<label class="switch">
								<input type="checkbox" id="ausm_debug_mode" name="ausm_debug_mode" value="on" <?php checked( $ausm_debug_mode, 'on' ); ?> />
								<div class="slider round"></div>
							</label>
							<?php echo wp_kses( wc_help_tip( esc_html__( 'Enable debug mode to show debugging information on your cart/checkout page.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_user_id"><?php esc_html_e( 'USPS User ID', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
                            <input type="text" id="ausm_user_id" name="ausm_user_id" value="<?php echo esc_attr( $ausm_user_id ); ?>" />
							<?php echo wp_kses( wc_help_tip( esc_html__( 'User ID will get from USPS after getting an account.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label><?php esc_html_e( 'Connection Status', 'advanced-usps-shipping-method' ); ?></label>
							<?php if ( !ausm_fs()->is__premium_only() || !ausm_fs()->can_use_premium_code() ) { ?>
								<span class="ausm-pro-label"></span>
							<?php } ?>
						</th>
						<td class="forminp">
							<?php if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) { ?>
								<p class="ausm_api_status"></p>
								<a href="javascript:void(0);" class="button" id="ausm_api_key_check"><?php esc_html_e( 'Check connection','advanced-usps-shipping-method'); ?></a>
								<?php echo wp_kses( wc_help_tip( esc_html__( 'This indicate API connection with USPS.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
								<div class="ausm_api_msg"></div>
							<?php } else { ?>
								<a href="javascript:void(0);" class="button" disabled><?php esc_html_e( 'Check connection','advanced-usps-shipping-method'); ?></a>
								<?php echo wp_kses( wc_help_tip( esc_html__( 'This indicate API connection with USPS.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
							<?php } ?>
							
						</td>
					</tr>
                    <tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_origin"><?php esc_html_e( 'Origin Postcode', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
                            <input type="text" id="ausm_origin" name="ausm_origin" value="<?php echo esc_attr($ausm_origin); ?>" />
							<?php echo wp_kses( wc_help_tip( esc_html__( 'Enter the postcode for the shop shipment.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
                    <tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_service_type"><?php esc_html_e( 'Shipping rates type', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
                            <select name="ausm_service_type" id="ausm_service_type">
								<option value="<?php echo esc_attr('ONLINE'); ?>" <?php selected( $ausm_service_type, 'ONLINE' ); ?>><?php esc_html_e( 'ONLINE', 'advanced-usps-shipping-method' ); ?></option>
								<option value="<?php echo esc_attr('ALL'); ?>" <?php selected( $ausm_service_type, 'ALL' ); ?>><?php esc_html_e( 'ALL', 'advanced-usps-shipping-method' ); ?></option>
								<option value="<?php echo esc_attr('PLUS'); ?>" <?php selected( $ausm_service_type, 'PLUS' ); ?>><?php esc_html_e( 'PLUS', 'advanced-usps-shipping-method' ); ?></option>
							</select>
							<?php echo wp_kses( wc_help_tip( esc_html__( 'Choose which rates to show your customers, ONLINE rates are normally cheaper than others.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_default_title"><?php esc_html_e( 'Default shipping title', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
                            <input type="text" id="ausm_default_title" name="ausm_default_title" value="<?php echo esc_attr( $ausm_default_title ); ?>" />
							<?php echo wp_kses( wc_help_tip( esc_html__( 'Default title show after the USPS shipping method and also for the default shipping method title.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_default_rate"><?php esc_html_e( 'Default shipping rate', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
                            <input type="text" id="ausm_default_rate" name="ausm_default_rate" value="<?php echo floatval($ausm_default_rate); ?>" />
							<?php echo wp_kses( wc_help_tip( esc_html__( 'If USPS returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="ausm_cheapest_rate"><?php esc_html_e( 'Cheapest rate', 'advanced-usps-shipping-method' ); ?></label>
						</th>
						<td class="forminp">
							<label class="switch">
								<input type="checkbox" id="ausm_cheapest_rate" name="ausm_cheapest_rate" value="on" <?php checked( $ausm_cheapest_rate, 'on' ); ?> />
								<div class="slider round"></div>
							</label>
							<?php echo wp_kses( wc_help_tip( esc_html__( 'Enable this option will only show the cheapest rate from USPS shipping.', 'advanced-usps-shipping-method' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
						</td>
					</tr>
                </tbody>
            </table>
			<p class="submit">
                <input type="submit" name="submitFee" class="button button-primary button-large" value="<?php echo esc_attr( 'Save changes' ); ?>">
            </p>
			<hr/>
			<h2><?php esc_html_e( 'USPS services settings', 'advanced-usps-shipping-method' ); ?></h2>
			<table class="ausm_services widefat" style="width:100%">
				<thead>
					<?php if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) { ?>
						<th class="sort">&nbsp;</th>
					<?php } ?>
					<th class="service-column"><?php esc_html_e( 'Service(s)', 'advanced-usps-shipping-method' ); ?></th>
					<th><?php esc_html_e( 'Price Adjustment ($) ', 'advanced-usps-shipping-method' ); ?> <span class="ausm-super"><?php echo esc_html($pro_tag); ?> </span>
					<?php if ( !ausm_fs()->is__premium_only() || !ausm_fs()->can_use_premium_code() ) { ?>
						<span class="ausm-pro-label"></span>
					<?php } ?></th>
					<th><?php esc_html_e( 'Price Adjustment (%) ', 'advanced-usps-shipping-method' ); ?>  <span class="ausm-super"><?php echo esc_html($pro_tag); ?> </span>
					<?php if ( !ausm_fs()->is__premium_only() || !ausm_fs()->can_use_premium_code() ) { ?>
						<span class="ausm-pro-label"></span>
					<?php } ?></th>
				</thead>
				<tbody>
					<?php
					$sort = 0;
					$ordered_services = array();
					foreach ( $services as $code => $values ) {
						if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
							if ( isset( $ausm_services[ $code ]['order'] ) && !empty( $ausm_services[ $code ]['order'] ) ) {
								$sort = $ausm_services[ $code ]['order'];
							}
						}
						while ( isset( $ordered_services[ $sort ] ) ) {
							$sort++;
						}
						$ordered_services[ $sort ] = array( $code, $values );
						$sort++;
					}
					ksort( $ordered_services );

					foreach ( $ordered_services as $value ) {
						$code   = $value[0];
						$values = $value[1];
						
						if ( ! isset( $ausm_services[ $code ] ) ) {
							$ausm_services[ $code ] = array();
						}
						
						$ausm_service_order = isset( $ausm_services[ $code ]['order'] ) ? $ausm_services[ $code ]['order'] : '';
						?>
						<tr>
							<?php if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) { ?>
							<td class="movable">
								<span class="dashicons dashicons-move"></span>
								<input type="hidden" class="order" name="ausm_services[<?php echo esc_attr( $code ); ?>][order]" value="<?php echo esc_attr( $ausm_service_order ); ?>" />
							</td>
							<?php } ?>
							<td>
								<ul class="sub_services" style="font-size: 0.92em; color: #555">
								<?php foreach ( $values['services'] as $key => $name ) { 
									$ausm_service_enabled = ( isset( $ausm_services[ $code ][ $key ]['enabled'] ) && ! empty( $ausm_services[ $code ][ $key ]['enabled'] ) ) ? true : false;
									?>
									<li>
										<label>
											<input type="checkbox" name="ausm_services[<?php echo esc_attr( $code ); ?>][<?php echo esc_attr( $key ); ?>][enabled]" <?php checked( $ausm_service_enabled, true ); ?> />
											<?php echo esc_attr( $name ); ?>
										</label>
									</li>

									<?php } ?>
								</ul>
							</td>
							<td>
								<ul class="sub_services" style="font-size: 0.92em; color: #555">
									<?php foreach ( $values['services'] as $key => $name ) { 
										if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
										$ausm_service_price = isset( $ausm_services[ $code ][ $key ]['price'] ) ? floatval($ausm_services[ $code ][ $key ]['price']) : 0;
										$ausm_service_enabled = ( isset( $ausm_services[ $code ][ $key ]['enabled'] ) && ! empty( $ausm_services[ $code ][ $key ]['enabled'] ) ) ? 'enabled' : 'disabled';
										?>
										<li>
											<div class="input-group-prepend">
												<span class="input-group-text <?php echo esc_attr($ausm_service_enabled); ?>"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
											</div>
											<input type="number" name="ausm_services[<?php echo esc_attr( $code ); ?>][<?php echo esc_attr( $key ); ?>][price]" placeholder="N/A" size="4" value="<?php echo esc_attr( number_format_i18n( $ausm_service_price, 2) ); ?>" class="price-field" />
										</li>
										<?php } else { ?>
											<li>
												<div class="input-group-prepend">
													<span class="input-group-text disabled"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
												</div>
												<input type="number" placeholder="N/A" size="4" value="" class="price-field" disabled />
											</li>
										<?php } ?>
									<?php } ?>
								</ul>
							</td>
							<td>
								<ul class="sub_services" style="font-size: 0.92em; color: #555">
									<?php foreach ( $values['services'] as $key => $name ) { 
										if ( ausm_fs()->is__premium_only() && ausm_fs()->can_use_premium_code() ) {
										$ausm_service_percentage = isset( $ausm_services[ $code ][ $key ]['percentage'] ) ? floatval($ausm_services[ $code ][ $key ]['percentage']) : 0;
										$ausm_service_enabled = ( isset( $ausm_services[ $code ][ $key ]['enabled'] ) && ! empty( $ausm_services[ $code ][ $key ]['enabled'] ) ) ? 'enabled' : 'disabled';?>
										<li>
											<input type="number" name="ausm_services[<?php echo esc_attr( $code ); ?>][<?php echo esc_attr( $key ); ?>][percentage]" placeholder="N/A" class="percentage-field" size="4" value="<?php echo esc_attr( number_format_i18n( $ausm_service_percentage, 2 ) ); ?>" />
											<div class="input-group-append">
												<span class="input-group-text <?php echo esc_attr($ausm_service_enabled); ?>">%</span>
											</div>
										</li>
										<?php } else { ?>
											<li>
												<input type="number" placeholder="N/A" class="percentage-field" size="4" value="" disabled />
												<div class="input-group-append">
													<span class="input-group-text disabled">%</span>
												</div>
											</li>
										<?php } ?>
									<?php } ?>
								</ul>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
            <p class="submit">
                <input type="submit" name="submitFee" class="button button-primary button-large" value="<?php echo esc_attr( 'Save changes' ); ?>">
            </p>
        </form>
    </div>
	<?php if ( !ausm_fs()->is__premium_only() || !ausm_fs()->can_use_premium_code() ) { ?>
	<div class="upgrade-to-pro-modal-main">
		<div class="upgrade-to-pro-modal-outer">
			<div class="pro-modal-inner">
				<div class="pro-modal-wrapper">
					<div class="pro-modal-header">
						<span class="dashicons dashicons-no-alt modal-close-btn"></span>
						<p><span class="ausm-pro-label"></span><?php esc_html_e( 'Subscribe to use this feature', 'advanced-usps-shipping-method' ); ?></p>
					</div>
					<div class="pro-modal-body">
						<h3 class="pro-feature-title"><?php esc_html_e( 'Try USPS Shipping Method Pro for free', 'advanced-usps-shipping-method' ); ?></h3>
						<ul class="pro-feature-list">
							<li><?php esc_html_e('Check API connection by one click', 'advanced-usps-shipping-method'); ?></li>
							<li><?php esc_html_e('Adjust USPS API price by additional fix cost', 'advanced-usps-shipping-method'); ?></li>
							<li><?php esc_html_e('Adjust USPS API price by additional percentage cost', 'advanced-usps-shipping-method'); ?></li>
							<li><?php esc_html_e('Rearrage shipping methods order to show best on top', 'advanced-usps-shipping-method'); ?></li>
							<li><?php esc_html_e('Import/Export plugin setting on one click', 'advanced-usps-shipping-method'); ?></li>
						</ul>
					</div>
					<div class="pro-modal-footer">
						<a class="pro-feature-trial-btn" href="<?php echo esc_url( ausm_fs()->get_upgrade_url() ) ; ?>"><?php esc_html_e( 'Get Premium Now »', 'advanced-usps-shipping-method' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
</div>
</div>
</div>
</div>