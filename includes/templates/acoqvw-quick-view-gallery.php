<?php
/**
 * Gallery content.
 *
 * @author  Acowebs
 * @package Acowebs WooCommerce Quick View
 * @version 1.0.0
 * 
 */

 
if (!defined('ABSPATH')) {
    exit;
}

global $product;

if($product){ 
    $image_urls = array();
    $image_thumbnails = array();
    $image_ids = array();
    $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
    $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
    $post_thumbnail = '';
    $product_id = $product->get_id();
    $quickview = get_option($this->token.'_quickview', false);
    $qvGT = ( isset($quickview['acoqvw_quickviewGalleryType']) ) ? $quickview['acoqvw_quickviewGalleryType'] : '';

    if($qvGT=='slider'){
        $image_ids = $product->get_gallery_image_ids();
        if(has_post_thumbnail( $product_id )){
            $image_ids[] = get_post_thumbnail_id( $product_id );
        }

        $productType = $product->get_type();
        $productVariation = new WC_Product_Variable( $product_id );
        $variations = $productVariation->get_available_variations();

        if($productType == 'variable') {
            /// add variation image
            foreach ( $variations as $variation ) {
                $image_ids[] = $variation['image_id'];
            }
        }
    } else if($qvGT=='thumb'){
        if(has_post_thumbnail( $product_id )){
            $image_ids[] = get_post_thumbnail_id( $product_id );
        }
    }

    $result = array_unique($image_ids);
    $image_ids = array_values(array_filter($result));
    
    if(count($image_ids) == 0) {
        $image_urls[] = esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );
        $image_thumbnails[] = esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );
    } else if($image_ids > 0) {
        for($i=0;$i<count($image_ids);$i++) {
            $attachment = wp_get_attachment_image_src( $image_ids[$i], $full_size );
            $attachment_thumb = wp_get_attachment_image_src( $image_ids[$i], $thumbnail_size );
            if ( !$attachment[0] ) { continue; }
            if ( !$attachment_thumb[0] ) { continue; }
            
            $image_urls[] = $attachment[0];
            $image_thumbnails[] = $attachment_thumb[0];
        }
    }

    $slider_class = 'acoqvw_sliders ';
    if(count($image_urls) == 1) {
        $slider_class .= 'acoqvw_single_image';
    }
   
    ?> 
    <?php do_action('acoqvw_before_gallery_section'); ?>
    <div class="acoqvw_gallery_inner images">
        <?php do_action('acoqvw_before_gallery_slider_outer'); ?>
        <div id="acoqvw_gallery_slider" class="acoqvw_gallery_slider">
            <?php do_action('acoqvw_before_gallery_slider_list'); ?>
            <ul class="<?php echo $slider_class; ?>">
                <?php do_action('acoqvw_before_gallery_slider_list_loop'); ?>
                <?php foreach($image_urls as $key=>$image){ ?>
                    <?php do_action('acoqvw_before_gallery_slider_list_item'); ?>
                    <li data-thumb="<?php echo $image_thumbnails[$key]; ?>" class="acoqvw_slider_image woocommerce-product-gallery__image">
                        <?php do_action('acoqvw_before_gallery_slider_image_link'); ?>
                        <a href="<?php echo $image; ?>">
                            <?php do_action('acoqvw_before_gallery_slider_image'); ?>
                            <img src="<?php echo $image; ?>" class="wp-post-image" alt="Quick View For Woocommerce">
                            <?php do_action('acoqvw_after_gallery_slider_image'); ?>
                        </a>
                        <?php do_action('acoqvw_after_gallery_slider_image_link'); ?>
                    </li>
                    <?php do_action('acoqvw_after_gallery_slider_list_item'); ?>
                <?php } ?>
                <?php do_action('acoqvw_after_gallery_slider_list_loop'); ?>
            </ul>
            <?php do_action('acoqvw_after_gallery_slider_list'); ?>
        </div>
        <?php do_action('acoqvw_after_gallery_slider_outer'); ?>
    </div>
    <?php do_action('acoqvw_after_gallery_section'); ?>
<?php
} ?>