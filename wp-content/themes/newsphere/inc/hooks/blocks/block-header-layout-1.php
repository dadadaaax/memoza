<?php
/**
 * List block part for displaying header content in page.php
 *
 * @package Newsphere
 */

?>
<?php
$class = '';
$background = '';
if (has_header_image()) {
    $class = 'data-bg';
    $background = get_header_image();
}

$show_date = newsphere_get_option('show_date_section');
$show_social_menu = newsphere_get_option('show_social_menu_section');
?>
<?php if (is_active_sidebar('off-canvas-panel') || (has_nav_menu('aft-social-nav') && $show_social_menu == true) || ($show_date == true)) : ?>
    <div class="top-header">
        <div class="container-wrapper">
          <div class="header-menu-part">
                <div id="main-navigation-bar" class="bottom-bar">
                    <div class="navigation-section-wrapper">
                        <div class="container-wrapper">
                            <div class="header-middle-part">
                                
                                <div class="navigation-container">
                                    
                                    <nav class="main-navigation clearfix" style="display:flex;">
                                        <?php
                                        $global_show_home_menu = newsphere_get_option('global_show_home_menu');
                                        if($global_show_home_menu == 'yes'):
                                        ?>
                                        
                                        <div class="logo-brand" >
                    <div class="site-branding">
                        <?php
                        the_custom_logo();
                        if (is_front_page() || is_home()) : ?>
                            <h1 class="site-title font-family-1">
                                <a href="<?php echo esc_url(home_url('/')); ?>"
                                   rel="home"><?php bloginfo('name'); ?></a>
                            </h1>
                        <?php else : ?>
                            <p class="site-title font-family-1">
                                <a href="<?php echo esc_url(home_url('/')); ?>"
                                   rel="home"><?php bloginfo('name'); ?></a>
                            </p>
                        <?php endif; ?>

                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) : ?>
                            <p class="site-description"><?php echo esc_html($description); ?></p>
                        <?php
                        endif; ?>
                    </div>
                </div>
                                        
                                        
                                        <span class="aft-home-icon">
                                        <?php $home_url = get_home_url(); ?>
                                            <a href="<?php echo esc_url($home_url); ?>">
                                                <img src="http://memoza.pl/wp-content/uploads/2020/10/kupa_logo.png" height="70px" width="70px"/>
                                        </a>
                                    </span>
                                        <?php endif; ?>
                                        <div class="aft-dynamic-navigation-elements">
                                            <button class="toggle-menu" aria-controls="primary-menu" aria-expanded="false">
                                            <span class="screen-reader-text">
                                                <?php esc_html_e('Primary Menu', 'newsphere'); ?>
                                            </span>
                                                <i class="ham"></i>
                                            </button>


                                            <?php
                                            $global_show_home_menu = newsphere_get_option('global_show_home_menu_border');

                                            wp_nav_menu(array(
                                                'theme_location' => 'aft-primary-nav',
                                                'menu_id' => 'primary-menu',
                                                'container' => 'div',
                                                'container_class' => 'menu main-menu menu-desktop '.$global_show_home_menu,
                                            ));
                                            ?>
                                        </div>

                                    </nav>
                                </div>
                            </div>
                            <div class="header-right-part">

                                <?php
                                $aft_language_switcher = newsphere_get_option('aft_language_switcher');
                                if(!empty($aft_language_switcher)):
                                ?>
                                <div class="language-icon">
                                    <?php echo do_shortcode($aft_language_switcher); ?>
                                </div>
                                <?php endif; ?>
                                <div class="af-search-wrap">
                                    <div class="search-overlay">
                                        <a href="#" title="Search" class="search-icon">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <div class="af-search-form">
                                            <?php get_search_form(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>
<div class="main-header <?php echo esc_attr($class); ?>" data-background="<?php echo esc_attr($background); ?>">
    <div class="container-wrapper">
        <div class="af-container-row af-flex-container">
            <div class="col-2 float-l pad">
              
            </div>
            <div class="col-66 float-l pad">
                <?php do_action('newsphere_action_banner_advertisement'); ?>
            </div>
        </div>
    </div>

</div>