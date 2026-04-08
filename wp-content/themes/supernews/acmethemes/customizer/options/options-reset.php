<?php
/**
 * Reset options
 * Its outside options panel
 *
 * @param  array $reset_options
 * @return void
 *
 * @since SuperNews 1.0.0
 */
if ( ! function_exists( 'supernews_reset_db_options' ) ) :
    function supernews_reset_db_options( $reset_options ) {
        set_theme_mod( 'supernews_theme_options', $reset_options );
    }
endif;

function supernews_reset_db_setting( ){
	$supernews_customizer_all_values = supernews_get_theme_options();
	$input = $supernews_customizer_all_values['supernews-reset-options'];
	if( '0' == $input ){
		return;
	}
    $supernews_default_theme_options = supernews_get_default_theme_options();
    $supernews_get_theme_options = get_theme_mod( 'supernews_theme_options');

    switch ( $input ) {
        case "reset-color-options":
            $supernews_get_theme_options['supernews-primary-color'] = $supernews_default_theme_options['supernews-primary-color'];
            supernews_reset_db_options($supernews_get_theme_options);
            break;

        case "reset-all":
            supernews_reset_db_options($supernews_default_theme_options);
            break;

        default:
            break;
    }
}
add_action( 'customize_save_after','supernews_reset_db_setting' );

/*adding sections for Reset Options*/
$wp_customize->add_section( 'supernews-reset-options', array(
    'priority'       => 220,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Reset Options', 'supernews' )
) );

/*Reset Options*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-reset-options]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-reset-options'],
    'sanitize_callback' => 'supernews_sanitize_select',
    'transport'			=> 'postMessage'
) );

$choices = supernews_reset_options();
$wp_customize->add_control( 'supernews_theme_options[supernews-reset-options]', array(
    'choices'  	=> $choices,
    'label'		=> __( 'Reset Options', 'supernews' ),
    'description'=> __( 'Caution: Reset theme settings according to the given options. Refresh the page after saving to view the effects. ', 'supernews' ),
    'section'   => 'supernews-reset-options',
    'settings'  => 'supernews_theme_options[supernews-reset-options]',
    'type'	  	=> 'select'
) );