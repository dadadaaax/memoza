<?php
/**
 * The left sidebar containing the main widget area.
 */
if ( ! is_active_sidebar( 'supernews-sidebar-left' ) ) {
	return;
}
$sidebar_layout = supernews_sidebar_selection();
?>
<?php if( $sidebar_layout == "left-sidebar" || $sidebar_layout == "both-sidebar"  ) : ?>
    <div id="secondary-left" class="widget-area sidebar secondary-sidebar float-right" role="complementary">
        <div id="sidebar-section-top" class="widget-area sidebar clearfix">
			<?php dynamic_sidebar( 'supernews-sidebar-left' ); ?>
        </div>
    </div>
<?php endif;