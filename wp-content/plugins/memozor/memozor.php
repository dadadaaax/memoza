<?php
/**
 * Plugin Name: Memozor
 * Description: A modern front-end meme editor with text formatting capabilities.
 * Version: 1.0.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// 1. Enqueue Scripts & Register Shortcode
function memozor_enqueue_scripts() {
    // Check if we are on a post/page and it has the shortcode
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'memozor_editor')) {
        // Fabric.js (Canvas library)
        wp_enqueue_script('fabric-js', 'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js', array(), '5.3.1', true);
        
        // Google Fonts for Memes
        wp_enqueue_style('memozor-fonts', 'https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Oswald:wght@700&family=Creepster&family=Press+Start+2P&display=swap', array(), null);

        // Plugin Scripts & Styles
        wp_enqueue_style('memozor-css', plugin_dir_url(__FILE__) . 'css/memozor.css', array(), '1.0.0');
        wp_enqueue_script('memozor-js', plugin_dir_url(__FILE__) . 'js/memozor.js', array('fabric-js'), '1.0.3', true);

        // Localize script to pass REST API details
        wp_localize_script('memozor-js', 'memozorSettings', array(
            'restUrl' => esc_url_raw(rest_url('memozor/v1/save')),
            'nonce'   => wp_create_nonce('wp_rest')
        ));
    }
}
add_action('wp_enqueue_scripts', 'memozor_enqueue_scripts');

function memozor_editor_shortcode() {
    ob_start();
    ?>
    <div id="memozor-container">
        <!-- Honeypot field for bot protection -->
        <input type="text" id="memozor-website-url" name="website_url" style="display:none" tabindex="-1" autocomplete="off">
        <div id="memozor-toolbar">
            <input type="file" id="memozor-upload" accept="image/png, image/jpeg, image/webp" title="Upload Image" />
            <button type="button" id="memozor-undo" disabled title="Undo">↶ Undo</button>
            <button type="button" id="memozor-redo" disabled title="Redo">↷ Redo</button>
            <button type="button" id="memozor-add-text">Add Text</button>
            <label>Font: 
                <select id="memozor-font-family">
                    <option value="Impact, sans-serif">Impact</option>
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="'Comic Sans MS', cursive">Comic Sans</option>
                    <option value="'Oswald', sans-serif">Oswald</option>
                    <option value="'Anton', sans-serif">Anton</option>
                    <option value="'Bebas Neue', sans-serif">Bebas Neue</option>
                    <option value="'Creepster', cursive">Creepster (Spooky!)</option>
                    <option value="'Press Start 2P', cursive">Press Start 2P (Retro!)</option>
                </select>
            </label>
            <label>Color: <input type="color" id="memozor-text-color" value="#ffffff"></label>
            <label>Outline: <input type="color" id="memozor-stroke-color" value="#000000"></label>
            <label>Size: <input type="range" id="memozor-text-size" min="10" max="150" value="40"></label>
            <button type="button" id="memozor-save">Save Meme</button>
        </div>
        <div id="memozor-canvas-container">
            <canvas id="memozor-canvas" width="600" height="400"></canvas>
        </div>
        <div id="memozor-message"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('memozor_editor', 'memozor_editor_shortcode');

// 2. Register REST API endpoint for saving
add_action('rest_api_init', function () {
    register_rest_route('memozor/v1', '/save', array(
        'methods'             => 'POST',
        'callback'            => 'memozor_save_image_endpoint',
        'permission_callback' => '__return_true'
    ));
});

function memozor_save_image_endpoint(WP_REST_Request $request) {
    $params = $request->get_json_params();

    // Honeypot check
    if (!empty($params['website_url'])) {
        // Silently reject
        return rest_ensure_response(array(
            'success'       => true,
            'attachment_id' => 0,
            'post_id'       => 0,
            'url'           => ''
        ));
    }

    // Rate limiting (max 5 per hour per IP)
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'memozor_rate_' . md5($ip);
    $attempts = get_transient($transient_key);
    if ($attempts === false) {
        $attempts = 0;
    }
    
    if ($attempts >= 5) {
        return new WP_Error('too_many_requests', 'You have reached the submission limit. Please try again later.', array('status' => 429));
    }
    
    set_transient($transient_key, $attempts + 1, HOUR_IN_SECONDS);

    $base64_img = isset($params['image_data']) ? $params['image_data'] : '';

    if (empty($base64_img)) {
        return new WP_Error('missing_data', 'No image data provided', array('status' => 400));
    }

    // Strip the "data:image/png;base64," prefix
    $img_parts = explode(";base64,", $base64_img);
    if (count($img_parts) !== 2) {
        return new WP_Error('invalid_data', 'Invalid image data format', array('status' => 400));
    }

    $img_type_aux = explode("image/", $img_parts[0]);
    if (!isset($img_type_aux[1])) {
        return new WP_Error('invalid_mime', 'Invalid MIME type', array('status' => 400));
    }
    
    $img_type = $img_type_aux[1];
    $img_base64 = base64_decode($img_parts[1]);

    if (!$img_base64) {
        return new WP_Error('decode_failed', 'Failed to decode base64', array('status' => 400));
    }

    $filename = 'meme_' . time() . '.' . $img_type;
    $upload = wp_upload_bits($filename, null, $img_base64);

    if ($upload['error']) {
        return new WP_Error('upload_error', $upload['error'], array('status' => 500));
    }

    // Create a new post
    $post_data = array(
        'post_title'    => 'Meme ' . date('Y-m-d H:i:s'),
        'post_content'  => '',
        'post_status'   => 'pending',
        'post_type'     => 'post'
    );
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        return new WP_Error('post_error', 'Could not create post', array('status' => 500));
    }

    // Add meta for identification in admin notice
    update_post_meta($post_id, '_is_memozor_meme', 1);

    // Insert into Media Library
    $attachment = array(
        'post_mime_type' => 'image/' . $img_type,
        'post_title'     => sanitize_file_name($filename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
    
    if (!is_wp_error($attach_id)) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        // Apply watermark using Image_Watermark plugin if available
        if (function_exists('Image_Watermark')) {
            $image_watermark = Image_Watermark();
            $attach_data = $image_watermark->apply_watermark($attach_data, $attach_id, 'manual');
            wp_update_attachment_metadata($attach_id, $attach_data);
        }

        // Set the meme as the featured image (thumbnail) of the post
        set_post_thumbnail($post_id, $attach_id);

        return rest_ensure_response(array(
            'success'       => true,
            'attachment_id' => $attach_id,
            'post_id'       => $post_id,
            'url'           => get_permalink($post_id)
        ));
    }

    return new WP_Error('attachment_error', 'Could not create attachment', array('status' => 500));
}

/**
 * 3. Admin Notification for Pending Memes
 */
function memozor_admin_pending_notice() {
    if (!current_user_can('edit_others_posts')) {
        return;
    }

    $pending_posts = get_posts(array(
        'post_type'   => 'post',
        'post_status' => 'pending',
        'meta_key'    => '_is_memozor_meme',
        'meta_value'  => '1',
        'numberposts' => -1,
    ));

    $pending_count = count($pending_posts);

    if ($pending_count > 0) {
        $url = admin_url('edit.php?post_status=pending&post_type=post');
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <?php printf(
                    _n(
                        'There is %d pending meme awaiting review.',
                        'There are %d pending memes awaiting review.',
                        $pending_count,
                        'memozor'
                    ),
                    number_format_i18n($pending_count)
                ); ?>
                <a href="<?php echo esc_url($url); ?>"><?php _e('Review Memes', 'memozor'); ?></a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'memozor_admin_pending_notice');
