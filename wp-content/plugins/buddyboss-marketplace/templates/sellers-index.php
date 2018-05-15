<?php
/**
 * BuddyBoss MarketPlace - Store Index
 *
 * @package WordPress
 * @subpackage BuddyBoss MarketPlace
 */
get_header(); ?>

    <div id="primary" class="site-content">

        <div id="content" role="main" class="woo-content">

        <?php

        $vendor_controller = BuddyBoss_BM_Vendors::instance();
        $current_user   = get_current_user_id();
        $all_vendors    = $vendor_controller->get_all_vendors();
        $paged_vendors  = $vendor_controller->get_paged_vendors();

        // Pagination calcs
        $total_vendors = count( $all_vendors );
        $total_vendors_paged = count($paged_vendors);
        $total_pages = ceil( $total_vendors / $vendor_controller->per_page );

        $searchby = isset( $vendor_controller->search_term ) ? $vendor_controller->search_term : '';
        ?>

        <div class="store-filters table">
            <form id="search-shops" role="search" action="" method="get" class="table-cell page-search">
                <input type="text" name="bm_index_search" placeholder="<?php _e('Search sellers', 'buddyboss-marketplace'); ?>" value="<?php echo $searchby; ?>"/>
                <input type="hidden" name="subject" value="sellers"/>
                <input type="submit" alt="Search" value="<?php _e('Search', 'buddyboss-marketplace'); ?>" />
            </form>

            <form id="filter-shops" action="" method="GET" class="table-cell filter-dropdown">

                <label><?php _e('Sort by:', 'buddyboss-marketplace'); ?></label>
                <select name="sorder" id="order">

                    <option value="registered" <?php selected( $vendor_controller->orderby, 'registered' ); ?>><?php _e('Most Recent', 'buddyboss-marketplace'); ?></option>
                    <option value="name" <?php selected( $vendor_controller->orderby, 'name' ); ?>><?php _e('Alphabetical', 'buddyboss-marketplace'); ?></option>

                </select>

            </form>
        </div>

        <p class="store-count">
            <?php printf( __('Browsing all sellers <span>(%1s of %2s)</span>', 'buddyboss-marketplace' ), $total_vendors_paged , $total_vendors ); ?>
        </p>

        <?php if ( $total_vendors != 0 ) : ?>

            <?php do_action( 'bm_before_stores_loop' ); ?>

                <?php foreach ($paged_vendors as $vendor): ?>

                    <?php
                    global $authordata;
                    $vendor_id 			= $vendor;
                    $state	 	= get_user_meta( $vendor_id, '_wcv_store_state',		true );
                    $shop_name = WCV_Vendors::is_vendor( $vendor_id )
                        ? WCV_Vendors::get_vendor_shop_name( $vendor_id )
                        : get_bloginfo( 'name' );
                    $store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 50, 50 ) );
                    $store_icon 		= '';
                    $shop_url  = WCV_Vendors::get_vendor_shop_page( $vendor_id );
                    // see if the array is valid
                    if ( is_array( $store_icon_src ) ) {
                        $store_icon 	= '<img src="'. $store_icon_src[0].'" alt="" class="store-icon" style="max-width:100%;" />';
                    }

                    $args = array(
                        'post_type'       => 'product',
                        'author'          => $vendor_id,
                        'no_found_rows'   => true,
                        'meta_query'      => array(
                            array(
                                'key'      => '_private_listing',
                                'compare'  => 'NOT EXISTS'
                            ),
                        )
                    );

                    /** Get total products of vendors ***************************/
                    $all_products_arg = wp_parse_args( array(
                        'fields'            => 'ids',
                        'posts_per_page'    => -1
                    ), $args );

                    $product_posts_all = new WP_Query( $all_products_arg );
                    $total_count = $product_posts_all->post_count;

                    /** Get max 3 products of vendors to show in block ****************/
                    $product_args = wp_parse_args( array(
                        'posts_per_page' => 3
                    ),  $args );

                    $product_posts = new WP_Query( $product_args );

                    $count = $sum = 0;

                    if(class_exists('WCVendors_Pro_Ratings_Controller')) {
                        if ($product_posts->have_posts()):

                            while ($product_posts->have_posts()) :
                                $product_posts->the_post();
                                $ratings_average = WCVendors_Pro_Ratings_Controller::get_ratings_average($authordata->ID);
                                $percent = $ratings_average/5;
                                $sum += $percent;
                            endwhile;

                        endif;
                    }
                    ?>

                    <article class="table bm-seller-item">
                        <div class="table-cell seller-desc">
                            <div class="table">
                                <div class="table-cell follow">
                                    <?php
                                    if ( $current_user && $current_user != $vendor_id ):

                                        $favorites = get_user_meta($current_user, "favorite_shops", true);

                                        $tooltip = __('Add to Favorites', 'buddyboss-marketplace');
                                        $class = '';
                                        if((is_array($favorites) && in_array($vendor_id, $favorites))) {
                                            $class = ' favorited';
                                            $tooltip = __('Remove from Favorites', 'buddyboss-marketplace');
                                        }
                                        echo '<p><a href="#" class="boss-tooltip bm-add-to-favs'.$class.'" data-tooltip="'.$tooltip.'" data-id="'. $vendor_id. '"><i class="fa fa-heart-o"></i></a></p>';
                                    endif; ?>
                                </div>
                                <div class="table-cell avatar">
                                    <a href="<?php echo bm_get_user_domain( $vendor_id ) ?>"><?php echo get_avatar( $vendor_id, 56 ); ?></a>
                                </div>
                                <div class="table-cell name">
                                    <a href="<?php echo bm_get_user_domain( $vendor_id ) ?>"><?php echo bm_get_user_displayname( $vendor_id ) ?></a>
                                    <?php BuddyBoss_BM_Templates::bm_user_social_links( $vendor_id ); ?>
                                </div>
                                <div class="table-cell details">
                                    <?php if($state != '') echo '<div>'.$state.'</div>'; ?>
                                    <?php if(class_exists('WCVendors_Pro_Ratings_Controller')): ?>
                                        <?php if(buddyboss_bm()->option('show-sold')) { ?>
                                            <?php $orders = bm_get_order_ids_by_vendor( $vendor_id ); ?>
                                            <div class="count-sale"><?php echo __('Sold: ', 'buddyboss-marketplace').count($orders); ?></div>
                                        <?php } ?>

                                        <?php if($total_count > 0): ?>
                                            <div class="percentage-feedback"><?php echo __('Feedback: ', 'buddyboss-marketplace').round(100*$sum/$total_count, 2).__('%', 'buddyboss-marketplace'); ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="table-cell seller-shop">

                          <div class="table">
                              <div class="table-cell seller-shop-desc">
                                  <div class="table">
                                      <div class="table-cell owner-avatar">
                                          <a href="<?php echo $shop_url; ?>"><?php if($store_icon) { echo $store_icon; } else { echo get_avatar( $vendor_id, 56 ); } ?></a>
                                      </div>
                                      <div class="table-cell owner-name">
                                          <span><?php _e('Shop', 'buddyboss-marketplace');?></span>
                                          <a href="<?php echo $shop_url; ?>"><?php echo $shop_name; ?></a>
                                      </div>
                                  </div>
                              </div>

                              <div class="table-cell seller-shop-products">

                                  <?php if ( $product_posts->have_posts() ) : ?>

                                      <?php while ( $product_posts->have_posts() && $count < 3 ) : $product_posts->the_post(); ?>

                                          <?php global $product;?>

                                          <a href="<?php echo get_the_permalink() ?>">
                                              <?php
                                              if ( has_post_thumbnail() ) {
                                                  echo get_the_post_thumbnail( null, 'bm-store-archive' );
                                              } elseif ( wc_placeholder_img_src() ) {
                                                  echo wc_placeholder_img( array(135, 150, 1) );
                                              }
                                              ?>
                                          </a>

                                          <?php $count++; ?>

                                      <?php endwhile; // end of the loop. ?>

                                  <?php endif; ?>

                                  <?php for ( $i = $count; $i < 3; $i++ ) { ?>
                                      <div>
                                          <img src="<?php echo buddyboss_bm()->assets_url . '/images/135x150.png' ?>">
                                      </div>
                                  <?php } ?>

                                  <div class="product-count">
                                      <img src="<?php echo buddyboss_bm()->assets_url . '/images/135x150.png' ?>">
                                      <a href="<?php echo $shop_url; ?>" class="overlay">
                                          <div class="table">
                                              <div class="table-cell">
                                                  <span class="number"><?php echo $total_count; ?></span>
                                                  <span class="text"><?php printf( _n( 'item', 'items', $total_count, 'buddyboss-marketplace' ), $total_count ); ?></span>
                                              </div>
                                          </div>
                                      </a>
                                  </div>
                              </div>
                            </div>
                        </div>
                    </article>

                <?php endforeach; ?>


            <?php do_action( 'bm_after_stores_loop' ); ?>

            <div id="pag-bottom" class="pagination">
                <ul class="pagination-links" id="member-dir-pag-bottom">

                <?php if ($total_vendors > $total_vendors_paged ): ?>

                    <?php $current_page = max( 1, get_query_var('paged') );

                        $page_links = paginate_links( 	array(
                            'base' => preg_replace('/\?.*/', '/', get_pagenum_link()) . '%_%',
                            'current' => $current_page,
                            'total' => $total_pages,
                            'prev_next'    => false,
                            'type'         => 'array',
                        ));

                        foreach ( $page_links as $current_page_link ) {
                            echo '<li>'. $current_page_link . '</li>';
                        } ?>
                <?php endif; ?>

                </ul>
            </div>

        <?php else: ?>

            <div id="message" class="info">
                <p><?php _e( "Sorry, no sellers were found.", 'buddyboss-marketplace' ); ?></p>
            </div>

        <?php endif; ?>

        </div>

    </div>

<?php get_footer();
