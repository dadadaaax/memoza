<?php
/**
 * Display default slider
 *
 * @since SuperNews 1.0.0
 *
 * @param int $post_id
 * @return void
 *
 */
if ( !function_exists('supernews_default_featured') ) :
    function supernews_default_featured(){
        ?>
        <div class="acme-col-2" style="background-image: url('<?php echo esc_url( get_template_directory_uri()."/assets/img/no-image-660-365.png" ); ?>')">

        </div>
        <div class="acme-col-2" style="background-image: url('<?php echo esc_url( get_template_directory_uri()."/assets/img/no-image-660-365.png" ); ?>')">

        </div>
        <div class="clearfix"></div>
        <div class="acme-col-3" style="background-image: url('<?php echo esc_url( get_template_directory_uri()."/assets/img/no-image-660-365.png" ); ?>')">

        </div>
        <div class="acme-col-3" style="background-image: url('<?php echo esc_url( get_template_directory_uri()."/assets/img/no-image-660-365.png" ); ?>')">

        </div>
        <div class="acme-col-3" style="background-image: url('<?php echo esc_url( get_template_directory_uri()."/assets/img/no-image-660-365.png" ); ?>')">

        </div>
        <?php
    }
endif;

/**
 * Featured Slider display
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return void
 */

if ( ! function_exists( 'supernews_display_feature_section' ) ) :

    function supernews_display_feature_section( ){

        global $supernews_customizer_all_values;

        $supernews_feature_cat = $supernews_customizer_all_values['supernews-feature-cat'];

	    $sticky = get_option( 'sticky_posts' );
	    $supernews_cat_post_args = array(
		    'posts_per_page'      => 5,
		    'no_found_rows'       => true,
		    'post_status'         => 'publish',
		    'ignore_sticky_posts' => true,
		    'post__not_in' => $sticky
	    );
	    if( 0 != $supernews_feature_cat ){
		    $supernews_cat_post_args['cat'] = $supernews_feature_cat;
	    }
	    $slider_query = new WP_Query($supernews_cat_post_args);
	    if ($slider_query->have_posts()):

		    $post_count = $slider_query->post_count;
		    $remaining_posts = $post_count;
		    $fixed = '';
		    if( $remaining_posts < 3 ){
			    $fixed = 'fix remain-'.$remaining_posts;
		    }

		    $i = 1;
		    $col = 'no-media acme-col-2';
		    $total_posts = $slider_query->post_count;
		    while ($slider_query->have_posts()): $slider_query->the_post();
			    if (has_post_thumbnail()) {
				    $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
			    } else {
				    $image_url[0] = get_template_directory_uri() . '/assets/img/no-image-660-365.png';
			    }
			    if( $i > 5 ){
				    $i = 1;
			    }
			    if( $i > 2 ){
				    $col = 'no-media acme-col-3';
			    }
			    if( $total_posts < 5 ){
				    if( 1 == $total_posts ){
					    $col = 'no-media acme-col-1';
				    }
                    elseif( $total_posts % 2 == 0 ){
					    $col = 'no-media acme-col-2';
				    }
                    elseif( 3 == $total_posts ){
					    $col = 'no-media acme-col-2';
					    if( $i < 2 ){
						    $col = 'no-media acme-col-1';
					    }
				    }
			    }
			    ?>
                <div class="single-unit <?php echo $col.' '.$fixed; ?>" style="background-image:url(<?php echo esc_url( $image_url[0] ); ?>);">
                    <a class="at-overlay" href="<?php the_permalink()?>">
                    </a>
                    <div class="slider-desc">
                        <div class="above-slider-details">
						    <?php
						    supernews_list_category();
						    ?>
                        </div>
                        <div class="slider-details">
                            <a href="<?php the_permalink()?>">
                                <div class="slide-title">
								    <?php the_title(); ?>
                                </div>
                            </a>
                        </div>
                        <div class="below-slider-details">
						    <?php supernews_posted_on(); ?>
						    <?php supernews_entry_footer(); ?>
                        </div>
                    </div>
                </div>
			    <?php
			    $i++;
		    endwhile;
		    wp_reset_postdata();
	    else:
		    supernews_default_featured();
	    endif;
    }
endif;

/**
 * Featured Slider display
 *
 * @since SuperNews 1.2.0
 *
 * @param null
 * @return void
 */
if ( ! function_exists( 'supernews_display_feature_slider' ) ) :

	function supernews_display_feature_slider( ){

		global $supernews_customizer_all_values;
		$supernews_feature_cat = $supernews_customizer_all_values['supernews-feature-cat'];

		$total_posts = 20;
		$sticky = get_option( 'sticky_posts' );

		$supernews_cat_post_args = array(
			'posts_per_page'      => $total_posts,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'post__not_in' => $sticky
		);
		if( 0 != $supernews_feature_cat ){
			$supernews_cat_post_args['cat'] = $supernews_feature_cat;
		}
		$slider_query = new WP_Query($supernews_cat_post_args);

		if ($slider_query->have_posts()):
			$i = 1;
			$j = 1;

			$post_count = $slider_query->post_count;
			$remaining_posts = $post_count;

			$current_slide_number = 1;
			$supernews_slider_display_options = 5;

			$total_posts = $supernews_slider_display_options;

			if( $supernews_slider_display_options > $remaining_posts ){
				$total_posts = $remaining_posts;
			}

			$fixed ='';
			if( $remaining_posts < $supernews_slider_display_options ){
				$fixed = 'fix remain-'.$remaining_posts;
			}

			echo "<div class='at-unique-slide ".$fixed."'>";
			while ($slider_query->have_posts()): $slider_query->the_post();
				if (has_post_thumbnail()) {
					$image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
				} else {
					$image_url[0] = get_template_directory_uri() . '/assets/img/no-image-660-365.png';
				}
				if( $j > $supernews_slider_display_options ){
					if( $supernews_slider_display_options == 1 || $j % $supernews_slider_display_options == 1 ){
						$current_slide_number = $current_slide_number + 1;
						$remaining_posts = $remaining_posts - $supernews_slider_display_options;
						if( $supernews_slider_display_options > $remaining_posts ){
							$total_posts = $remaining_posts;
						}
						$fixed ='';
						if( $remaining_posts < $supernews_slider_display_options ){
							$fixed = 'fix remain-'.$remaining_posts;
						}
						$i = 1;
						echo "</div><div class='at-unique-slide ".$fixed."'>";
					}
				}
				$col = 'acme-col-1';
				if( 1 == $total_posts ){
					$col = 'at-extra-height acme-col-1';
				}
                elseif( 2 == $total_posts ){
					$col = 'acme-col-2';
				}
                elseif( 3 == $total_posts ){
					$col = 'acme-col-2';
					if( $i == 3 ){
						echo "<div class='clearfix'></div>";
						$col = 'acme-col-1';
					}
				}
                elseif( 4 == $total_posts ){
					$col = 'acme-col-2';
				}
                elseif( 5 == $total_posts ){
					$col = 'acme-col-2';
					if( $i > 2 ){
						$col = 'acme-col-3';
					}
				}
                elseif( 6 == $total_posts ){
					$col = 'acme-col-3';
				}
				?>
                <div class="no-media-query at-slide-unit <?php echo $col.' atsi-'.$i; ?>" style="background-image:url(<?php echo esc_url( $image_url[0] ); ?>);">
                    <a class="at-overlay" href="<?php the_permalink()?>"></a>
                    <div class="slider-desc">
                        <div class="above-slider-details">
							<?php
							supernews_list_category();
							?>
                        </div>
                        <div class="slider-details">
                            <a href="<?php the_permalink()?>">
                                <div class="slide-title">
									<?php the_title(); ?>
                                </div>
                            </a>
                        </div>
                        <div class="below-slider-details">
							<?php supernews_posted_on(); ?>
							<?php supernews_entry_footer(); ?>
                        </div>
                    </div>

                </div>
				<?php
				$i++;
				$j++;
			endwhile;
			echo "</div>";/*at-unique-slide at-custom-slide*/
			wp_reset_postdata();
		else:
			supernews_default_featured();
		endif;
	}
endif;

/**
 * Display related posts from same category
 *
 * @since SuperNews 1.0.0
 *
 * @param int $post_id
 * @return void
 *
 */
if ( !function_exists('supernews_feature_slider') ) :
    function supernews_feature_slider() {
	    global $supernews_customizer_all_values;
	    $supernews_feature_slider_enable = $supernews_customizer_all_values['supernews-feature-slider-enable'];
	    if( 1 == $supernews_feature_slider_enable ){
	        $class = 'home-bxslider at-home-slider';
	        echo ' <div class="slider-section top-right at-slider-5">';
        }
        else{
	        $class = 'home-bxslider at-feature-section';
	        echo ' <div class="slider-section">';
        }
	    ?>
        <div class="<?php echo $class; ?>">
		    <?php
		    if( 1 == $supernews_feature_slider_enable ){
			    supernews_display_feature_slider();
		    }
		    else{
			    supernews_display_feature_section();
		    }
		    ?>
        </div>
	    <?php
	    echo '</div>';
    }
endif;
add_action( 'supernews_action_feature_slider', 'supernews_feature_slider', 0 );