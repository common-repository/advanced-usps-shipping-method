(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function(){
		// add currunt menu class in main manu
        jQuery('a[href="admin.php?page=advanced-usps-shipping-method"]').parent().addClass('current');
        jQuery('a[href="admin.php?page=advanced-usps-shipping-method"]').addClass('current');

		// script for the toggle sidebar
		var span_full = $('.toggleSidebar .dashicons');
        var show_sidebar = localStorage.getItem('ausm-sidebar-display');
        if( ( null !== show_sidebar || undefined !== show_sidebar ) && ( 'hide' === show_sidebar ) ) {
            $('.all-pad').addClass('hide-sidebar');
            span_full.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-left-alt2');
        } else {
            $('.all-pad').removeClass('hide-sidebar');
            span_full.removeClass('dashicons-arrow-left-alt2').addClass('dashicons-arrow-right-alt2');
        }
        $(document).on( 'click', '.toggleSidebar', function(){
            $('.all-pad').toggleClass('hide-sidebar');
            if( $('.all-pad').hasClass('hide-sidebar') ){
                localStorage.setItem('ausm-sidebar-display', 'hide');
                span_full.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-left-alt2');
                $('.all-pad .ausm-section-right').css({'-webkit-transition': '.3s ease-in width', '-o-transition': '.3s ease-in width',  'transition': '.3s ease-in width'});
                $('.all-pad .ausm-section-left').css({'-webkit-transition': '.3s ease-in width', '-o-transition': '.3s ease-in width',  'transition': '.3s ease-in width'});
                setTimeout(function() {
                    $('#dotsstoremain .dotstore_plugin_sidebar').css('display', 'none');
                }, 300);
            } else {
                localStorage.setItem('ausm-sidebar-display', 'show');
                span_full.removeClass('dashicons-arrow-left-alt2').addClass('dashicons-arrow-right-alt2');
                $('.all-pad .ausm-section-right').css({'-webkit-transition': '.3s ease-out width', '-o-transition': '.3s ease-out width',  'transition': '.3s ease-out width'});
                $('.all-pad .ausm-section-left').css({'-webkit-transition': '.3s ease-out width', '-o-transition': '.3s ease-out width',  'transition': '.3s ease-out width'});
                $('#dotsstoremain .dotstore_plugin_sidebar').css('display', 'block');
            }
        });

		/** tiptip js implementation */
		$( '.woocommerce-help-tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
			'keepAlive': true
		} );

		/** Upgrade to pro modal */
		$(document).on('click', '#dotsstoremain .ausm-pro-label', function(){
			$('body').addClass('ausm-modal-visible');
		});

		$(document).on('click', '#dotsstoremain .modal-close-btn', function(){
			$('body').removeClass('ausm-modal-visible');
		});
		// close modal on click outside at anywhere
		$(document).on('click',function(e){
			if( !(($(e.target).closest('.pro-modal-wrapper').length > 0 ) || ($(e.target).closest('.ausm-pro-label').length > 0)) ){
				$('body').removeClass('ausm-modal-visible');
			}
		});

		// script for plugin rating
		$(document).on('click', '.dotstore-sidebar-section .content_box .et-star-rating label', function(e){
			e.stopImmediatePropagation();
			var rurl = $('#et-review-url').val();
			window.open( rurl, '_blank' );
		});

		
	});

	/* <fs_premium_only> */
	function call_for_connection_check( user_id, $this ){
		
		var before_text = $this.text();
		$this.text(ausm_ajax_object.process_text);
		$this.attr('disabled', true);

		$.ajax({
			url: ausm_ajax_object.ajax_url,
			type: 'post',
			data: {
				action: 'usps_api_connection_check',
				security: ausm_ajax_object.ausm_nonce,   // pass the nonce here
				usps_user_id: user_id,
			},
			success: function( response ) {
				var ausm_span = $( '<span />' ).addClass('dashicons');
				var ausm_span_msg = $( '<span />' ).addClass('api_msg');
				if( response.success ){
					ausm_span.addClass('dashicons-rss connect');
					ausm_span_msg.addClass('success');
					ausm_span_msg.html(response.data.message);
				} else {
					ausm_span.addClass('dashicons-dismiss disconnect');
					ausm_span_msg.addClass('error');
					ausm_span_msg.html(response.data.message);
				}
				//dashicon setting
				$( '.ausm_api_status .dashicons' ).remove();
				$( '.ausm_api_status' ).append(ausm_span);
				$this.text(before_text);

				//error message setting
				$( '.ausm_api_msg span' ).remove();
				$( '.ausm_api_msg' ).append(ausm_span_msg);

				setTimeout(function(){
					$( '.ausm_api_msg span' ).remove();
				}, 5000);

				$this.attr('disabled', false);
			},
		});
	}

	function loader_show( where, message ){
		message = message ? message : null;
		$( where ).block({
			message: message,
			overlayCSS: {
			  background: 'rgb(255, 255, 255)',
			  opacity: 0.6,
			},
		});
	}

	function loader_hide( where ){
		$( where ).unblock();
	}
	/* <fs_premium_only> */

})( jQuery );
