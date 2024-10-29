<?php //phpcs:ignore
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class AUSM_Shipping_Method.
 *
 * WooCommerce Advanced flat rate shipping method class.
 */
if ( class_exists( 'AUSM_Shipping_Method' ) ) {
	return; // Stop if the class already exists
}

class AUSM_Shipping_Method extends WC_Shipping_Method {

    private $endpoint 			= 'https://production.shippingapis.com/shippingapi.dll';
    //phpcs:ignore
	//private $endpoint     	= 'https://stg-production.shippingapis.com/ShippingApi.dll';
	private $default_user_id 	= '570CYDTE1766';
	private $domestic        	= array( 'US', 'PR', 'MP', 'VI', 'GU', 'AS' );
	private $found_rates;
	private $package_info;
	private $services;
	private $ausm_config;
	public $enabled;
	private $debug;
	private $origin;
	private $user_id;
	private $custom_services;
	public $title;
	private $default_rate;
	private $cheapest_rate;
	private $packing_method;
	private $disable_commercial_rates;

    /**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'dots_ausm';
		$this->method_title       = __( 'Advanced USPS Shipping Method', 'advanced-usps-shipping-method' );
		$this->method_description = __( 'The <strong>USPS</strong> extension obtains rates dynamically from the USPS API during cart/checkout.', 'advanced-usps-shipping-method' );
		$this->services           = include ADVANCED_USPS_SHIPPING_METHOD_DIR . '/includes/data-ausm-services.php';
		$this->init();
	}

	public function init(){
		$this->ausm_config 				= !empty(get_option('ausm_config')) ? get_option('ausm_config') : array();
		$this->enabled      			= isset( $this->ausm_config['ausm_status'] ) && 'on' === $this->ausm_config['ausm_status'] ? 'yes' : 'no';
		$this->debug                    = isset( $this->ausm_config['ausm_debug_mode'] ) && 'on' === $this->ausm_config['ausm_debug_mode'] ? true : false;
		$this->origin					= isset( $this->ausm_config['ausm_origin'] ) && !empty( $this->ausm_config['ausm_origin'] ) ? $this->ausm_config['ausm_origin'] : (new WC_Countries())->get_base_postcode();

		$this->user_id					= isset( $this->ausm_config['ausm_user_id'] ) && !empty( $this->ausm_config['ausm_user_id'] ) ? $this->ausm_config['ausm_user_id'] : $this->default_user_id;
		$this->custom_services			= isset( $this->ausm_config['ausm_services'] ) && !empty( $this->ausm_config['ausm_services'] ) ? $this->ausm_config['ausm_services'] : array();
		$this->title					= isset( $this->ausm_config['ausm_default_title'] ) && !empty( $this->ausm_config['ausm_default_title'] ) ? $this->ausm_config['ausm_default_title'] : '';
		$this->default_rate				= isset( $this->ausm_config['ausm_default_rate'] ) && !empty( $this->ausm_config['ausm_default_rate'] ) ? $this->ausm_config['ausm_default_rate'] : '';
		$this->cheapest_rate			= isset( $this->ausm_config['ausm_cheapest_rate'] ) && ( 'on' === $this->ausm_config['ausm_cheapest_rate'] ) ? true : false;
		$this->packing_method			= 'per_item';
		$this->disable_commercial_rates = false;
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @accesss public
	 * @param mixed $package
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		global $woocommerce;
		
		$domestic = in_array( $package['destination']['country'], $this->domestic, true ) ? true : false;
		
		$this->debug( __( 'Advanced USPS debug mode is on - to hide these messages, turn debug mode off in the settings.', 'advanced-usps-shipping-method' ) );
		
		$package_requests = $this->get_package_requests($package);
		$api              = $domestic ? 'RateV4' : 'IntlRateV2';
		libxml_use_internal_errors( true );

		if( $package_requests ) {

			$request  = '<' . $api . 'Request USERID="' . $this->user_id . '">' . "\n";
			$request .= '<Revision>2</Revision>' . "\n";

			foreach ( $package_requests as $key => $package_request ) {
				$request .= $package_request['request_data'];
			}

			$request .= '</' . $api . 'Request>' . "\n";
			$request  = 'API=' . $api . '&XML=' . str_replace( array( "\n", "\r" ), '', $request );

            // phpcs:disable
			// $transient       = 'ausm_quote_' . md5( $request );
			// $cached_response = get_transient( $transient );
            
			$this->debug( 'Advanced USPS REQUEST: <pre>' . print_r( htmlspecialchars( $request ), true ) . '</pre>' );
            // phpcs:enable
			
			$response = wp_remote_post(
				$this->endpoint,
				array(
					'timeout' => 3,
					'sslverify' => 0,
					'body' => $request,
				)
			);

			if ( is_wp_error( $response ) ) {
				$error_string = $response->get_error_message();
				$this->debug( 'Advanced USPS REQUEST FAILED' . $error_string );
				if ( $this->default_rate && ! empty( $this->custom_services ) ) {
					$default_title = !empty($this->title) ? $this->title : $this->method_title;
					$this->add_rate(
						array(
							'id' => $this->id . '_default_rate',
							'label' => $default_title,
							'cost' => $this->default_rate,
							'sort' => 0,
						)
					);
				}
				$response = false;
			} else {
				$response = $response['body'];

                // phpcs:ignore
				$this->debug( 'Advanced USPS RESPONSE: <pre style="height: 200px; overflow:auto;">' . print_r( htmlspecialchars( $response ), true ) . '</pre>' );

                // phpcs:ignore
				// set_transient( $transient, $response, DAY_IN_SECONDS * 30 );
			}

			if ( $response ) {
				$usps_packages = simplexml_load_string( $response );

				if ( ! ( $usps_packages ) ) {
					$this->debug( 'Advanced Failed loading XML', 'error' );
				}

				if ( ! is_object( $usps_packages ) && ! is_a( $usps_packages, 'SimpleXMLElement' ) ) {
					$this->debug( 'Advanced Invalid XML response format', 'error' );
				}

				if ( ! empty( $usps_packages ) ) {
					foreach ( $usps_packages as $usps_package ) {

						if ( ! $usps_package || ! is_object( $usps_package ) ) {
							continue;
						}

						// Get package data
						$data_parts = explode( ':', $usps_package->attributes()->ID );
						if ( count( $data_parts ) < 6 ) {
							$valid_response = false; // when the request has invalid ID or no valid address was found.
							continue;
						}

						list( $package_item_id, $cart_item_qty, $package_length, $package_width, $package_height, $package_weight ) = $data_parts;

						$quotes = $usps_package->children();

						if ( $this->debug ) {
							$found_quotes = array();

							foreach ( $quotes as $quote ) {
                                // phpcs:disable
								if ( $domestic ) {
									$code = strval( $quote->attributes()->CLASSID );
									$name = strip_tags( htmlspecialchars_decode( (string) $quote->{'MailService'} ) );
								} else {
									$code = strval( $quote->attributes()->ID );
									$name = strip_tags( htmlspecialchars_decode( (string) $quote->{'SvcDescription'} ) );
								}
                                // phpcs:enable

								if ( $name && $code ) {
									$found_quotes[ $code ] = $name;
								} elseif ( $name ) {
									$found_quotes[ $code . '-' . sanitize_title( $name ) ] = $name;
								}
							}

							if ( $found_quotes ) {
								ksort( $found_quotes );
								$found_quotes_html = '';
								foreach ( $found_quotes as $code => $name ) {
									if ( ! strstr( $name, 'Flat Rate' ) ) {
										$found_quotes_html .= '<li>' . $code . ' - ' . $name . '</li>';
									}
								}
								$this->debug( 'Advanced The following quotes were returned by USPS: <ul>' . $found_quotes_html . '</ul> If any of these do not display, they may not be enabled in USPS settings.', 'success' );
							}
						}
						
						// Loop our known services
						foreach ( $this->services as $service => $values ) {
							
							if ( $domestic && strpos( $service, 'D_' ) !== 0 ) {
								continue;
							}

							if ( ! $domestic && strpos( $service, 'I_' ) !== 0 ) {
								continue;
							}

							$default_title = ( !empty($this->title) ) ? ' (' . $this->title . ')' : '';

							$rate_code      = (string) $service;
							$rate_id        = $this->id . ':' . $rate_code;
							$rate_name      = (string) $values['name'] . $default_title;
							$rate_cost      = null;
							$svc_commitment = null;

							foreach ( $quotes as $quote ) {
								if ( $domestic ) {
									$code = strval( $quote->attributes()->CLASSID );
								} else {
									$code = strval( $quote->attributes()->ID );
								}

								if ( '' !== $code && in_array( $code, array_keys( $values['services'] ), true ) ) {
									if ( $domestic ) {
										if ( $this->disable_commercial_rates ) {
											if ( ( (float) $quote->{'Rate'} ) > 0.0 ) {
												$cost = (float) $quote->{'Rate'} * $cart_item_qty;
											} else {
												continue;
											}
										} else {
											if ( ! empty( $quote->{'CommercialRate'} ) ) {
												$cost = (float) $quote->{'CommercialRate'} * $cart_item_qty;
											} else {
												$cost = (float) $quote->{'Rate'} * $cart_item_qty;
											}
										}
									} else {
	
										if ( ! empty( $quote->{'CommercialPostage'} ) ) {
											$cost = (float) $quote->{'CommercialPostage'} * $cart_item_qty;
										} else {
											$cost = (float) $quote->{'Postage'} * $cart_item_qty;
										}
									}
	
									// Cost percentage adjustment
									if ( ! empty( $this->custom_services[ $rate_code ][ $code ]['percentage'] ) ) {
										$cost = round( $cost + ( $cost * ( floatval( $this->custom_services[ $rate_code ][ $code ]['percentage'] ) / 100 ) ), wc_get_price_decimals() );
									}
	
									// Cost price adjustment
									if ( ! empty( $this->custom_services[ $rate_code ][ $code ]['price'] ) ) {
										$cost = round( $cost + floatval( $this->custom_services[ $rate_code ][ $code ]['price'] ), wc_get_price_decimals() );
									}
	
									// Enabled check
									if ( ! isset( $this->custom_services[ $rate_code ][ $code ] ) || empty( $this->custom_services[ $rate_code ][ $code ]['enabled'] ) ) {
										continue;
									}
									
									if ( is_null( $rate_cost ) ) {
										$rate_cost      = $cost;
										$svc_commitment = $quote->SvcCommitments;
									} elseif ( $cost < $rate_cost ) {
										$rate_cost      = $cost;
										$svc_commitment = $quote->SvcCommitments;
									}
								}
							}

							if ( $rate_cost ) {
								if ( ! empty( $svc_commitment ) && strstr( $svc_commitment, 'days' ) ) {
									$rate_name .= ' (' . current( explode( 'days', $svc_commitment ) ) . ' days)';
								}
								$this->prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost );
							}
						}
					}
				} else {
					// No rates
					$this->debug( 'Invalid request; no rates returned', 'error' );
				}
			}
		}

		// Ensure rates were found for all packages
		if ( $this->found_rates ) {
			foreach ( $this->found_rates as $key => $value ) {
				if ( $value['packages'] < count( $package_requests ) ) {
					$this->debug( "Unsetting {$key} - too few packages.", 'error' );
					unset( $this->found_rates[ $key ] );
				}
			}
		}

		// Add rates
		if ( $this->found_rates ) {
			if ( $this->cheapest_rate ) {

				$cheapest_rate = '';

				foreach ( $this->found_rates as $key => $rate ) {
					if ( ! $cheapest_rate || $cheapest_rate['cost'] > $rate['cost'] ) {
						$cheapest_rate = $rate;
					}
				}

                // phpcs:ignore
				// $cheapest_rate['label'] = $this->method_title;
				$this->add_rate( $cheapest_rate );
			} else {
				
				uasort( $this->found_rates, array( $this, 'sort_rates' ) );

				foreach ( $this->found_rates as $key => $rate ) {
					$this->add_rate( $rate );
				}
			}  
		}
	}

	/**
	 * Prepare_rate function.
	 *
	 * @accesss private
	 * @param mixed $rate_code
	 * @param mixed $rate_id
	 * @param mixed $rate_name
	 * @param mixed $rate_cost
	 * @return void
	 */
	private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost ) {

		// Name adjustment
		if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) ) {
			$rate_name = $this->custom_services[ $rate_code ]['name'];
		}

		// Merging
		if ( isset( $this->found_rates[ $rate_id ] ) ) {
			$rate_cost = $rate_cost + $this->found_rates[ $rate_id ]['cost'];
			$packages  = 1 + $this->found_rates[ $rate_id ]['packages'];
		} else {
			$packages = 1;
		}

		// Sort
		if ( isset( $this->custom_services[ $rate_code ]['order'] ) ) {
			$sort = $this->custom_services[ $rate_code ]['order'];
		} else {
			$sort = 999;
		}

		$this->found_rates[ $rate_id ] = array(
			'id' => $rate_id,
			'label' => $rate_name,
			'cost' => $rate_cost,
			'sort' => $sort,
			'packages' => $packages,
		);
	}

	/**
	 * Sort_rates function.
	 *
	 * @accesss public
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	public function sort_rates( $a, $b ) {
		if ( $a['sort'] === $b['sort'] ) {
			return 0;
		}
		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}

	public function debug( $message, $type = 'notice' ) {
		if ( $this->debug && ! is_admin() ) { 
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
				wc_add_notice( $message, $type );
			} else {
				global $woocommerce;
				$woocommerce->add_message( $message );
			}
		}
	}

	public function get_package_requests( $package ) {

		// Choose selected packing
		switch ( $this->packing_method ) {
			case 'box_packing': //premium
				$requests = $this->box_shipping__premium_only( $package );
				break;
			case 'weight_based': //premium
				$requests = $this->weight_based_shipping__premium_only( $package );
				break;
			case 'per_item':
			default:
				$requests = $this->per_item_shipping( $package );
				break;
		}

		return $requests;
	}

	/**
	 * Per_item_shipping function.
	 *
	 * @accesss private
	 * @param mixed $package
	 * @return void
	 */
	private function per_item_shipping( $package ) {
		global $woocommerce;
		
		$requests = array();
		$domestic = in_array( $package['destination']['country'], $this->domestic, true ) ? true : false;

		// Get weight of order
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( wp_kses_post( '<strong>(#%s - %s)</strong> is virtual. Skipping...', 'advanced-usps-shipping-method' ), $values['data']->get_id(), $values['data']->get_title() ) );
				continue;
			}

			$item_weight = !empty($values['data']->get_weight()) && $values['data']->get_weight() > 0 ? $values['data']->get_weight() : '';
			if ( ! $item_weight ) {
				$this->debug( sprintf( wp_kses_post( '<strong>(#%s - %s)</strong> is missing weight. We are assuming 1lb.', 'advanced-usps-shipping-method' ), $values['data']->get_id(), $values['data']->get_title() ) );

				$weight = 1;
			} else {
				
				$weight = wc_get_weight( $item_weight, 'lbs' );
			}
			
			//Default we will use regular size
			$size = 'REGULAR';
			$item_length = $values['data']->get_length() ? $values['data']->get_length() : 0;
			$item_width = $values['data']->get_width() ? $values['data']->get_width() : 0;
			$item_height = $values['data']->get_height() ? $values['data']->get_height() : 0;
			
			if( $item_length <= 0 ) {
				$this->debug( sprintf( wp_kses_post( '<strong>(#%s - %s)</strong> is missing length. We are assuming 0.', 'advanced-usps-shipping-method' ), $values['data']->get_id(), $values['data']->get_title() ) );
			}
			if( $item_width <= 0 ) {
				$this->debug( sprintf( wp_kses_post( '<strong>(#%s - %s)</strong> is missing width. We are assuming 0.', 'advanced-usps-shipping-method' ), $values['data']->get_id(), $values['data']->get_title() ) );
			}
			if( $item_height <= 0 ) {
				$this->debug( sprintf( wp_kses_post( '<strong>(#%s - %s)</strong> is missing height. We are assuming 0.', 'advanced-usps-shipping-method' ), $values['data']->get_id(), $values['data']->get_title() ) );
			}

			if( $item_length > 0 || $item_width > 0 || $item_height > 0 ){

				$dimensions = array( wc_get_dimension( $item_length, 'in' ), wc_get_dimension( $item_width, 'in' ), wc_get_dimension( $item_height, 'in' ) ); //we have converted these data from cm to inches

				if ( max( $dimensions ) > 12 ) {
					$size = 'LARGE';
				} 
			} else {
				$dimensions = array( 0, 0, 0 );
			}

			if ( $domestic ) {

				$request  = '<Package ID="' . $this->generate_package_id( $item_id, $values['quantity'], $dimensions[0], $dimensions[1], $dimensions[2], $weight ) . '">' . "\n";
				$request .= '	<Service>' . ( $this->ausm_config['ausm_service_type'] ? $this->ausm_config['ausm_service_type'] : 'ONLINE' ) . '</Service>' . "\n";
				$request .= '	<ZipOrigination>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</ZipOrigination>' . "\n";
				$request .= '	<ZipDestination>' . strtoupper( substr( $package['destination']['postcode'], 0, 5 ) ) . '</ZipDestination>' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( wc_get_weight( $weight - floor( $weight ), 'oz', 'lbs'), 2 ) . '</Ounces>' . "\n";

				if ( 'LARGE' === $size ) {
					$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				} else {
					$request .= '	<Container />' . "\n";
				}

				$request .= '	<Size>' . $size . '</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[0] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[2] . '</Height>' . "\n";
				$request .= '	<Girth></Girth>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<ShipDate>' . gmdate( 'd-M-Y', ( current_time( 'timestamp' ) + ( 60 * 60 * 24 ) ) ) . '</ShipDate>' . "\n";
				$request .= '</Package>' . "\n";
			} else {

				$request  = '<Package ID="' . $this->generate_package_id( $item_id, $values['quantity'], $dimensions[0], $dimensions[1], $dimensions[2], $weight ) . '">' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( wc_get_weight( $weight - floor( $weight ), 'oz', 'lbs'), 2 ) . '</Ounces>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<MailType>Package</MailType>' . "\n";
				$request .= '	<ValueOfContents>' . $values['data']->get_price() . '</ValueOfContents>' . "\n";
				$request .= '	<Country>' . strtoupper( WC()->countries->countries[ $package['destination']['country'] ] ) . '</Country>' . "\n";

				$request .= '	<Container>RECTANGULAR</Container>' . "\n";

				$request .= '	<Size>' . $size . '</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[0] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[2] . '</Height>' . "\n";
				$request .= '	<Girth></Girth>' . "\n";
				$request .= '	<OriginZip>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</OriginZip>' . "\n";
				$request .= '	<CommercialFlag>' . ( 'ONLINE' === $this->ausm_config['ausm_service_type'] ? 'Y' : 'N' ) . '</CommercialFlag>' . "\n";
				$request .= '</Package>' . "\n";
			}

			$item_data = $values['data'] ? $values['data'] : array();
            $packed_items = array();
			if ( $item_data ) {// Front-end price call doesn't need this data
				$item_id                = $item_data->get_id();
				$packed_items[ $item_id ] = array(
					'product_name' => $item_data->get_title(),
					'qty' => 1,
				);
				if ( 'simple' !== $item_data->get_type() ) {
					$packed_items[ $item_id ]['variation_text'] = $this->ausm_get_variation_data_from_variation_id( $item_data->get_id() );
				}
			}
			$package_info = array(
				'items' => $packed_items,
				'dimension' => array(
					'length' => $dimensions[0],
					'width' => $dimensions[1],
					'height' => $dimensions[2],
					'weight' => $weight,
				),
				'units' => array(
					'dimension' => 'in',
					'weight' => 'lbs',
				),
			);
			$requests[]   = array(
				'request_data' => $request,
				'package_info' => $package_info,
			);
		}
		return $requests;
	}

	/**
	 * Generate a package ID for the request
	 *
	 * Contains qty and dimension info so we can look at it again later when it comes back from USPS if needed
	 *
	 * @return string
	 */
	public function generate_package_id( $id, $qty, $length, $width, $height, $weight ) {
		return implode( ':', array( $id, $qty, $length, $width, $height, $weight ) );
	}

	/**
	 * Return product variation details from variation ID
	 *
	 * Contains qty and dimension info so we can look at it again later when it comes back from USPS if needed
	 *
	 * @return string
	 */
	public function ausm_get_variation_data_from_variation_id( $item_id ) {
		$_product         = new WC_Product_Variation( $item_id );
		$variation_data   = $_product->get_variation_attributes();
		$variation_detail = wc_get_formatted_variation( $variation_data, true );  // this will give all variation detail in one line
		// $variation_detail = woocommerce_get_formatted_variation( $variation_data, false);  // this will give all variation detail one by one
		return $variation_detail; // $variation_detail will return string containing variation detail which can be used to print on website
		// return $variation_data; // $variation_data will return only the data which can be used to store variation data
	}
}