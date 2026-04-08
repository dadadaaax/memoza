<?php

/**
 * Post Addons related functions.
 *
 * @since      5.5
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Addon
 */
defined('ABSPATH') || exit; // Exit if accessed directly

/**
 * Register post related addons.
 *
 * @since 5.5
 */
function bd_register_post_features(){ //phpcs:ignore
    $bd = BULK_DELETE();

    add_meta_box(Bulk_Delete::BOX_CUSTOM_FIELD, esc_html__('By Custom Field', 'bulk-delete'), 'bd_render_delete_posts_by_custom_field_box', $bd->posts_page, 'advanced');
    add_meta_box(Bulk_Delete::BOX_TITLE, esc_html__('By Title', 'bulk-delete'), 'bd_render_delete_posts_by_title_box', $bd->posts_page, 'advanced');
    add_meta_box(Bulk_Delete::BOX_DUPLICATE_TITLE, esc_html__('By Duplicate Title', 'bulk-delete'), 'bd_render_delete_posts_by_duplicate_title_box', $bd->posts_page, 'advanced');
    add_meta_box(Bulk_Delete::BOX_POST_BY_ROLE, esc_html__('By User Role', 'bulk-delete'), 'bd_render_delete_posts_by_user_role_box', $bd->posts_page, 'advanced');
}
add_action('bd_add_meta_box_for_posts', 'bd_register_post_features');

/**
 * Render delete posts by custom field box.
 *
 * @since 5.5
 */
function bd_render_delete_posts_by_custom_field_box(){ //phpcs:ignore
    if (BD_Util::is_posts_box_hidden(Bulk_Delete::BOX_CUSTOM_FIELD)) {
        /* translators: %1$s is the URL to refresh the page */
        bd_wp_kses_wf(sprintf(__('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete'), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG));

        return;
    }

    if (! class_exists('Bulk_Delete_Posts_By_Custom_Field')) {
?>
        <!-- Custom Field box start-->
        <p>
            <span class="bd-post-custom-field-pro" style="color:red">
                <?php esc_html_e('Deleting posts by custom field is ', 'bulk-delete') . '<span class="open-upsell pro-feature-inline">Available in PRO</span>'; ?>
            </span>
        </p>
        <!-- Custom Field box end-->
    <?php
    } else {
        Bulk_Delete_Posts_By_Custom_Field::render_delete_posts_by_custom_field_box();
    }
}

/**
 * Render posts by title box.
 *
 * @since 5.5
 */
function bd_render_delete_posts_by_title_box(){ //phpcs:ignore
    if (BD_Util::is_posts_box_hidden(Bulk_Delete::BOX_TITLE)) {
        /* translators: %1$s is the URL to refresh the page */
        bd_wp_kses_wf(sprintf(__('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete'), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG));

        return;
    }

    if (! class_exists('Bulk_Delete_Posts_By_Title')) {
    ?>
        <!-- Title box start-->
        <p>
            <span class="bd-post-title-pro" style="color:red">
                <?php esc_html_e('Bulk Delete Posts by Title is ', 'bulk-delete') . '<span class="open-upsell pro-feature-inline">Available in PRO</span>'; ?>
            </span>
        </p>
        <!-- Title box end-->
    <?php
    } else {
        Bulk_Delete_Posts_By_Title::render_delete_posts_by_title_box();
    }
}

/**
 * Render delete posts by duplicate title box.
 *
 * @since 5.5
 */
function bd_render_delete_posts_by_duplicate_title_box(){ //phpcs:ignore
    if (BD_Util::is_posts_box_hidden(Bulk_Delete::BOX_DUPLICATE_TITLE)) {
        /* translators: %1$s is the URL to refresh the page */
        bd_wp_kses_wf(sprintf(__('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete'), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG));

        return;
    }

    if (! class_exists('Bulk_Delete_Posts_By_Duplicate_Title')) {
    ?>
        <!-- Duplicate Title box start-->
        <p>
            <span class="bd-post-title-pro" style="color:red">
                <?php esc_html_e('Bulk Delete Posts by Duplicate Title is ', 'bulk-delete') . '<span class="open-upsell pro-feature-inline">Available in PRO</span>'; ?>
            </span>
        </p>
        <!-- Duplicate Title box end-->
    <?php
    } else {
        Bulk_Delete_Posts_By_Duplicate_Title::render_delete_posts_by_duplicate_title_box();
    }
}

/**
 * Delete posts by user role.
 *
 * @since 5.5
 */
function bd_render_delete_posts_by_user_role_box(){ //phpcs:ignore
    if (BD_Util::is_posts_box_hidden(Bulk_Delete::BOX_POST_BY_ROLE)) {
        /* translators: %1$s is the URL to refresh the page */
        bd_wp_kses_wf(sprintf(__('This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-delete'), 'admin.php?page=' . Bulk_Delete::POSTS_PAGE_SLUG));

        return;
    }
    if (! class_exists('Bulk_Delete_Posts_By_User_Role')) {
    ?>
        <!-- Posts by user role start-->
        <p>
            <span class="bd-post-by-role-pro" style="color:red">
                <?php esc_html_e('Bulk Delete Posts by User Role is ', 'bulk-delete') . '<span class="open-upsell pro-feature-inline">Available in PRO</span>'; ?>
            </span>
        </p>
        <!-- Posts by user role end-->
    <?php
    } else {
        Bulk_Delete_Posts_By_User_Role::render_delete_posts_by_user_role_box();
    }
}
?>