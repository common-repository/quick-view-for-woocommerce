<?php
/**
 * Quick view content.
 *
 * @author  Acowebs
 * @package Acowebs WooCommerce Quick View
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

while ( have_posts() ) :
	the_post(); 
    global $product;
    $product_type = $product->get_type();
    
    ?> 
    <div class="acoqvw_quickview product  <?php echo 'acoqvw_'.$product_type; ?>" id="acoqvw_quickview_outer"> 
        <span class="acoqvw_close"></span>
        <?php do_action( 'acoqvw_before_quickview_inner_content' );?>
        <div class="acoqvw_inner" id="acoqvw_quickview_inner">
            <?php do_action('acoqvw_before_product_image'); ?>
            <div class="acoqvw_imageSec">
                <?php 
                do_action( 'acoqvw_before_quickview_sale_label' );
                do_action( 'acoqvw_quickview_sale_label' ); 
                do_action( 'acoqvw_after_quickview_sale_label' );
                do_action( 'acoqvw_quickview_before_product_image' ); 
                do_action( 'acoqvw_quickview_product_image' ); 
                do_action( 'acoqvw_quickview_after_product_image' ); 
                ?>
            </div>
            <?php 
            do_action('acoqvw_after_product_image'); 
            do_action('acoqvw_before_product_content');
            ?>
            <div class="acoqvw_contentSec">
                <?php do_action('acoqvw_before_product_content_inner'); ?>
                <div class="acoqvw_contentInner">
                <?php 
                    do_action( 'acoqvw_quickview_before_product_summary' );
                    do_action( 'acoqvw_quickview_product_summary' );
                    do_action( 'acoqvw_quickview_after_product_summary' );

                    do_action( 'acoqvw_quickview_before_product_add_to_cart' );
                    do_action( 'acoqvw_quickview_product_add_to_cart' );
                    do_action( 'acoqvw_quickview_after_product_add_to_cart' );
                    ?>
                </div>
                <?php do_action('acoqvw_after_product_content_inner'); ?>   
            </div>
            <?php 
            do_action('acoqvw_after_product_content');
            ?>
        </div>
        <?php do_action( 'acoqvw_after_quickview_inner_content' );?>
    </div>
	<?php
endwhile; // end of the loop.
