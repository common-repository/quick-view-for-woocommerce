jQuery(document).ready(($) => {
	/**
	 * Global Variables
	 */
	let acoqvw_ajax = false;
	const {
		ajax_url: acoqvw_AjaxUrl,
		slider: acoqvw_Slider,
		columns: acoqvw_Columns,
	} = acoqvw_global_vars;

	/**
	 * Init Function
	 */
	const acoqvwInit = () => {
		// Variation Form Reinitialize
		var popup = jQuery('.acoqvw_quickview_container');
		var form_variation = popup.find('.variations_form');
		form_variation.each(function () {
			$(this).wc_variation_form();
		});

		var has_directionNav =
			acoqvw_Slider.enable_arrows &&
			$(
				'.acoqvw_quickview_container .acoqvw_quickview .acoqvw_imageSec .acoqvw_sliders'
			).hasClass('acoqvw_single_image')
				? false
				: acoqvw_Slider.enable_arrows;

		$('.acoqvw_gallery_slider').flexslider({
			animation: 'slider',
			selector: '.acoqvw_sliders > .acoqvw_slider_image',
			slideshowSpeed: 3000,
			controlNav: 'thumbnails',
			directionNav: has_directionNav,
			animationLoop: true,
			slideshow: false,
		});

		if (acoqvw_Slider.single_image) {
			$(
				'.acoqvw_quickview_container .acoqvw_quickview .acoqvw_imageSec .flex-direction-nav'
			).remove();
		}

		// Rest Image Position In variation
		$(document).on('woocommerce_gallery_reset_slide_position', (e) => {
			$('.acoqvw_gallery_slider').flexslider(-1);
		});
	};

	//Avoid redirection in gallery images
	$(document).on('click', '.acoqvw_gallery_inner a', (e) => {
		e.preventDefault();
	});

	/**
	 * Helpers
	 */

	const acoqvw_hideFromView = () => {
		$(
			'.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec .woocommerce-review-link'
		).remove();
		$(
			'.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_contentSec .stock'
		).remove();
	};

	/**
	 * Open Quickview Modal
	 */

	const acoqvw_openPopup = (event) => {
		let product_id;
		if (typeof event === 'object') {
			product_id = event.target.getAttribute('data-product_id')
				? event.target.getAttribute('data-product_id')
				: $(event.target)
						.parents('.acoqvw_quickview_button')
						.data('product_id');
		} else {
			product_id = event;
		}

		if (isNaN(product_id)) {
			event.preventDefault();
			$(document).trigger('acoqvw_quickview_wrong_product_id');
			return;
		}

		let popup = jQuery('#acoqvw_quickview_modal_window');

		if (popup.length) {
			popup.removeClass('hide');
			popup.html('<div class="acoqvw_preloader"></div>');
		} else {
			popupHTML =
				"<div id='acoqvw_quickview_modal_window' class='acoqvw_quickview_container'></div>";
			jQuery('body').append(popupHTML);
			popup = jQuery('#acoqvw_quickview_modal_window');
			jQuery(popup).html("<div class='acoqvw_preloader'></div>");
		}
		jQuery(popup).css('display', 'flex');
		jQuery('html').css('overflow', 'hidden');

		let quickviewOuter = jQuery('#acoqvw_quickview_outer');
		if (quickviewOuter.length) {
			jQuery(quickviewOuter).removeClass('hide');
		}

		if (product_id && acoqvw_AjaxUrl) {
			let success = false;
			$.ajax({
				type: 'POST',
				url: acoqvw_AjaxUrl,
				data: {
					action: 'acoqvw_get_quickview',
					id: product_id,
				},
				dataType: 'json',
				beforeSend: () => {
					acoqvw_ajax = true;
				},
				success: (response) => {
					jQuery(popup).html(response['result']);

                    //hide over-flowing while animating
                    jQuery(popup).find('.acoqvw_quickview').css('overflow', 'hidden');
                    setTimeout(()=>{
                        jQuery(popup).find('.acoqvw_quickview').css('overflow', 'visible');
                    },800);

					success = true;
				},
				error: (jqXHR, textStatus, errorThrown) => {
					error = jqXHR.responseJSON.data;
					jQuery(popup).html(
						'<div class="acoqvw_quickview" id="acoqvw_quickview_outer"><div class="acoqvw_inner" id="acoqvw_quickview_inner">' +
							error +
							'</div></div>'
					);
				},
				complete: () => {
					acoqvw_ajax = false;
					if (success) {
						// Re initialize Script
						acoqvwInit();
						// Hide from view
						acoqvw_hideFromView();
						// Trigger when quickview loaded
						$(document).trigger('acoqvw_quickview_loaded');
					}
				},
			});
		}
	};

	/**
	 * Open Quickview Cascading
	 */

	const acoqvw_openCascading = (event) => {
		let popupOld = jQuery('#acoqvw_quickview_cascade_window');
		let product_id = event.target.getAttribute('data-product_id')
			? event.target.getAttribute('data-product_id')
			: $(event.target)
					.parents('.acoqvw_quickview_button')
					.data('product_id');

		if (isNaN(product_id)) {
			event.preventDefault();
			$(document).trigger('acoqvw_quickview_wrong_product_id');
			return;
		}

		let appendAfter = '';
		let columns;

		if ((columns = parseInt(acoqvw_Columns))) {
			const currentItem = $(event.target).closest('li');
			let position = $('ul.products li.product').index(currentItem);
			position++;
			if ($(window).width() >= 768) {
				position = Math.ceil(position / columns) * columns;
			}
			const total_products_in_page = $('ul.products li.product').length;
			if (position > total_products_in_page) {
				position = total_products_in_page;
			}
			appendAfter =
				'ul.products > li.product:nth-of-type(' + position + ')';
		}

		if (popupOld.length) {
			popupOld.remove();
		}

		if ($(appendAfter).length) {
			popupHTML =
				"<div id='acoqvw_quickview_cascade_window' class='acoqvw_quickview_container acoqvw_cascade'></div>";
			jQuery(appendAfter).after(popupHTML);
		} else {
			acoqvw_openPopup(event);
			return;
		}

		if ($('#acoqvw_quickview_cascade_window').offset().top) {
			$('html, body').animate(
				{
					scrollTop:
						$('#acoqvw_quickview_cascade_window').offset().top -
						120,
				},
				500
			);
		}

		popup = jQuery('.acoqvw_quickview_container');
		jQuery(popup).html("<div class='acoqvw_preloader'></div>");
		jQuery(popup).css('display', 'flex');

		if (product_id && acoqvw_AjaxUrl) {
			let success = false;
			let innerHtml = '';
			$.ajax({
				type: 'POST',
				url: acoqvw_AjaxUrl,
				data: {
					action: 'acoqvw_get_quickview',
					id: product_id,
				},
				dataType: 'json',
				beforeSend: () => {
					acoqvw_ajax = true;
				},
				success: (response) => {
					innerHtml = response['result'];
					success = true;
				},
				error: (jqXHR, textStatus, errorThrown) => {
					data = jqXHR.responseJSON.data;
					innerHtml =
						'<div class="acoqvw_quickview" id="acoqvw_quickview_outer"><div class="acoqvw_inner" id="acoqvw_quickview_inner">' +
						data +
						'</div></div>';
				},
				complete: () => {
					jQuery('#acoqvw_quickview_cascade_window').html(innerHtml);
					jQuery('.acoqvw_quickview').slideDown('slow');
					acoqvw_ajax = false;
					if (success) {
						//Re initialize Script
						acoqvwInit();

						//fix height problem for Images in mobile in cascade
						if ($(window).width() < 768) {
							image_container = $(
								'.acoqvw_quickview_container .acoqvw_quickview .acoqvw_inner .acoqvw_imageSec'
							);
							if (image_container.children().length > 0) {
								width = image_container.width();
								image_container.height(width);
							}
						}

						// Hide from view
						acoqvw_hideFromView();
						// Trigger when quickview loaded

						$(document).trigger('acoqvw_quickview_loaded');
					}
				},
			});
		}
	};

	/**
	 * Quickview Open Trigger Actions
	 */

	// Open Modal window
	jQuery(document).on('click', '.acoqvw_quickview_modal', (event) => {
		event.preventDefault();
		if (!acoqvw_ajax) {
			acoqvw_openPopup(event);
		}
	});

	// Open Cascading window
	jQuery(document).on('click', '.acoqvw_quickview_cascading', (event) => {
		event.preventDefault();
		if (!acoqvw_ajax) {
			acoqvw_openCascading(event);
		}
	});

	/**
	 * Quickview Close trigger actions
	 */

	// Remove Modal window
	jQuery(document).on('click', '#acoqvw_quickview_modal_window', (event) => {
		if (!acoqvw_ajax) {
			let container = jQuery('#acoqvw_quickview_inner');
			if (
				!container.is(event.target) &&
				!container.has(event.target).length
			) {
				main_container = container.parent().parent();
				outer_container = container.parent();

				main_container.addClass('hide');
				outer_container.addClass('hide');
				setTimeout(() => {
					main_container.hide();
					jQuery('html').css('overflow', 'auto');
					$(document).trigger('acoqvw_quickview_closed');
				}, 800);
			}
		}
	});

	// Remove Cascading window
	jQuery(document).on(
		'click',
		'.acoqvw_quickview_container .acoqvw_quickview .acoqvw_close',
		() => {
			if (!acoqvw_ajax) {
				jQuery('#acoqvw_quickview_cascade_window').slideUp(
					'slow',
					() => {
						$(document).trigger('acoqvw_quickview_closed');
					}
				);
			}
		}
	);

	/**
	 * Compatibility
	 *
	 */

	$(document).on('acoqvw_quickview_loaded', function () {
		/*====== Acowebs Product Labels======== */
		var badge = jQuery('.acoqvw_quickview .acoplw-hidden-wrap');
		var acoDivClass = '.images';

		if (badge.length >= 1) {
			// Check for badges
			var badgeCont = badge.find('.acoplw-badge').clone();
			if (jQuery(acoDivClass).length) {
				var position = jQuery(acoDivClass);
				position
					.find('.acoplw-badge-icon')
					.css('visibility', 'visible');
				jQuery(badgeCont).prependTo(jQuery(position).parent());
				badge.remove();
			}
		}

		/**======= WooCommerce Composite Products ========= */
		if (jQuery.isFunction(jQuery.fn.wc_composite_form)) {
			if (
				jQuery('.acoqvw_quickview .composite_form .composite_data')
					.length
			) {
				jQuery(
					'.acoqvw_quickview .composite_form .composite_data'
				).each((i, c) => {
					jQuery(c).wc_composite_form();
				});
			}
		}
	});
});
