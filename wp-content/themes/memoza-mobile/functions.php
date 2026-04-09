<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function memoza_mobile_enqueue_scripts() {
    $theme_dir_path = get_stylesheet_directory();
    $theme_dir_url = get_stylesheet_directory_uri();
    
    $manifest_path = $theme_dir_path . '/dist/.vite/manifest.json';
    
    if ( file_exists( $manifest_path ) ) {
        $manifest = json_decode( file_get_contents( $manifest_path ), true );
        
        if ( isset( $manifest['src/main.tsx'] ) ) {
            $main_js = $manifest['src/main.tsx']['file'];
            wp_enqueue_script( 'memoza-mobile-main', $theme_dir_url . '/dist/' . $main_js, array(), null, true );
            
            if ( isset( $manifest['src/main.tsx']['css'] ) ) {
                foreach ( $manifest['src/main.tsx']['css'] as $css_file ) {
                    wp_enqueue_style( 'memoza-mobile-style-' . md5($css_file), $theme_dir_url . '/dist/' . $css_file, array(), null );
                }
            }
        }
    }

    // Enqueue Memozor dependencies
    wp_enqueue_script('fabric-js', 'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js', array(), '5.3.1', true);
    wp_enqueue_style('memozor-fonts', 'https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Oswald:wght@700&family=Creepster&family=Press+Start+2P&display=swap', array(), null);
    wp_enqueue_style('memozor-css', plugins_url('memozor/css/memozor.css'), array(), '1.0.0');
    wp_enqueue_script('memozor-js', plugins_url('memozor/js/memozor.js'), array('fabric-js'), '1.0.3', true);
    wp_localize_script('memozor-js', 'memozorSettings', array(
        'restUrl' => esc_url_raw(rest_url('memozor/v1/save')),
        'nonce'   => wp_create_nonce('wp_rest')
    ));

    // Pass data to React
    wp_localize_script( 'memoza-mobile-main', 'memozaData', array(
        'apiUrl' => esc_url_raw( rest_url( 'wp/v2/posts' ) ),
        'nonce'  => wp_create_nonce( 'wp_rest' ),
        'siteUrl' => site_url(),
        'themeUrl' => get_stylesheet_directory_uri(),
        'isLoggedIn' => is_user_logged_in(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'memoza_mobile_enqueue_scripts' );

// Add module type to the script tag for Vite build
add_filter( 'script_loader_tag', 'memoza_mobile_script_type', 10, 3 );
function memoza_mobile_script_type( $tag, $handle, $src ) {
    if ( 'memoza-mobile-main' === $handle ) {
        return '<script type="module" src="' . esc_url( $src ) . '" id="' . esc_attr( $handle ) . '-js"></script>';
    }
    return $tag;
}
