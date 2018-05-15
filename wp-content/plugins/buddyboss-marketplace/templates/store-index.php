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
                <input type="text" name="bm_index_search" placeholder="<?php _e('Search stores', 'buddyboss-marketplace'); ?>" value="<?php echo $searchby; ?>"/>
                <input type="submit" alt="Search" value="<?php _e('Search', 'buddyboss-marketplace'); ?>" />
            </form>

            <form id="filter-shops" action="<?php echo _get_page_link(); ?>" method="GET" class="table-cell filter-dropdown">

                <label><?php _e('Sort by:', 'buddyboss-marketplace'); ?></label>
                <select name="storeorder" id="order">

                    <option value="registered" <?php selected( $vendor_controller->orderby, 'registered' ); ?>><?php _e('Most Recent', 'buddyboss-marketplace'); ?></option>
                    <option value="name" <?php selected( $vendor_controller->orderby, 'meta_value' ); ?>><?php _e('Alphabetical', 'buddyboss-marketplace'); ?></option>

                </select>

            </form>
        </div>

            <p class="store-count">
                <?php printf( __('Browsing all stores <span>(%1s of %2s)</span>', 'buddyboss-marketplace' ), $total_vendors_paged , $total_vendors ); ?>
            </p>

        <?php if ( 0 != $total_vendors ) : ?>

            <?php do_action( 'bm_before_stores_loop' ); ?>

                <?php foreach ($paged_vendors as $vendor): ?>

                    <?php
                    $vendor_id 			= $vendor;
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
                    ?>
                    <article class="table store-item">
                        <div class="table-cell store-desc">
                            <a href="<?php echo $shop_url; ?>" class="about-store">
                                <?php echo $store_icon; ?>
                                <h3><?php echo $shop_name; ?></h3>
                                    <span class="icon-wrap">
                                        <span class="bb-side-icon"></span>
                                    </span>
                            </a>

                            <div class="about-owner">
                                <div class="table">
                                    <div class="table-cell owner-avatar">
                                        <a href="<?php echo bm_get_user_domain( $vendor_id ) ?>"><?php echo get_avatar( $vendor_id, 40 ); ?></a>
                                    </div>
                                    <div class="table-cell owner-name">
                                        <span><?php _e('Shop Owner', 'buddyboss-marketplace');?></span>
                                        <a href="<?php echo bm_get_user_domain( $vendor_id ); ?>"><?php  echo bm_get_user_displayname( $vendor_id ) ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-cell store-products">
                            <?php // Products Loop
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

                            /** Get 13 products of vendors to show in block ****************/
                            $product_args = wp_parse_args( array(
                                'posts_per_page' => 13
                            ),  $args );

                            //Set number of page.
                           if ( isset( $product_paged ) ) $product_args['paged'] = $product_paged;
                           $product_posts = new WP_Query( $product_args );

                            $count = 0;
                            ?>

                            <?php if ( $product_posts->have_posts() ) : ?>

                                <?php while ( $product_posts->have_posts() && $count < 13 ) : $product_posts->the_post(); ?>

                                    <a href="<?php echo get_the_permalink() ?>">
                                        <?php
                                        global $product;

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

                            <?php for ( $i = $count; $i < 13; $i++ ) { ?>
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
                <p><?php _e( "Sorry, no stores were found.", 'buddyboss-marketplace' ); ?></p>
            </div>

        <?php endif; ?>

        </div>

    </div>

<?php get_footer();