<?php

namespace BulkWP\BulkDelete\Core\Base;

defined('ABSPATH') || exit; // Exit if accessed directly.

/**
 * Base class for all Admin Page including Bulk Delete pages and setting pages.
 *
 * All concrete implementation of a Bulk Delete Admin page will extend this class.
 *
 * @since 6.0.0
 */
abstract class BasePage
{
    /**
     * Slug of Bulk Delete Menu.
     */
    const BULK_WP_MENU_SLUG = 'bulk-delete-posts';

    /**
     * Path to main plugin file.
     *
     * @var string
     */
    protected $plugin_file;

    /**
     * Page Slug.
     *
     * @var string
     */
    protected $page_slug;

    /**
     * Hook Suffix of the current page.
     *
     * @var string
     */
    protected $hook_suffix;

    /**
     * Current screen.
     *
     * @var \WP_Screen
     */
    protected $screen;

    /**
     * Minimum capability needed for viewing this page.
     *
     * @var string
     */
    protected $capability = 'manage_options';

    /**
     * Labels used in this page.
     *
     * @var array
     */
    protected $label = array(
        'page_title' => '',
        'menu_title' => '',
    );

    /**
     * Messages shown to the user.
     *
     * @var array
     */
    protected $messages = array(
        'warning_message' => '',
    );

    /**
     * Actions used in this page.
     *
     * @var array
     */
    protected $actions = array();

    /**
     * Should the link to this page be displayed in the plugin list. Default false.
     *
     * @var bool
     */
    protected $show_link_in_plugin_list = 0;

    /**
     * Initialize and setup variables and attributes of the page.
     *
     * @return void
     */
    abstract protected function initialize();

    /**
     * Render body content.
     *
     * @return void
     */
    abstract protected function render_body();

    /**
     * BasePage constructor.
     *
     * @param string $plugin_file Path to the main plugin file.
     */
    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
        $this->initialize();
    }

    /**
     * Register the page.
     *
     * This function will be called in the `admin_menu` hook.
     */
    public function register()
    {
        $this->register_page();
        $this->register_hooks();
    }

    /**
     * Register page as a submenu to the Bulk Delete Menu.
     */
    protected function register_page()
    {
        $hook_suffix = add_submenu_page(
            self::BULK_WP_MENU_SLUG,
            $this->label['page_title'],
            $this->label['menu_title'],
            $this->capability,
            $this->page_slug,
            array($this, 'render_page')
        );

        if (false !== $hook_suffix) {
            $this->hook_suffix = $hook_suffix;
        }
    }

    /**
     * Register hooks.
     */
    protected function register_hooks()
    {
        add_filter('bd_action_nonce_check', array($this, 'verify_nonce'), 10, 2);

        add_action("load-{$this->hook_suffix}", array($this, 'setup_contextual_help'));
        add_filter('bd_admin_help_tabs', array($this, 'render_help_tab'), 10, 2);

        add_action("bd_admin_footer_for_{$this->page_slug}", array($this, 'modify_admin_footer'));

        if ($this->show_link_in_plugin_list) {
            add_filter('bd_plugin_action_links', array($this, 'append_to_plugin_action_links'));
        }

        add_action( 'admin_print_scripts-' . $this->hook_suffix, array( $this, 'enqueue_assets' ) );

        add_action('admin_action_bulkwp_install_wp301', array($this, 'install_wp301'));
    }

    /**
	 * Enqueue Scripts and Styles.
	 */
	public function enqueue_assets() {
		/**
		 * Runs just before enqueuing scripts and styles in all Bulk Delete admin pages.
		 *
		 * This action is primarily for registering or deregistering additional scripts or styles.
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage The current page.
		 *
		 * @since 5.5.1
		 * @since 6.0.0 Added $page parameter.
		 */
		do_action( 'bd_before_admin_enqueue_scripts', $this ); //phpcs:ignore

		/**
		 * Runs just before enqueuing scripts and styles in a Bulk Delete admin pages.
		 *
		 * This action is primarily for registering or deregistering additional scripts or styles.
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage The current page.
		 *
		 * @since 6.0.1
		 */
		do_action( "bd_before_enqueue_page_assets_for_{$this->get_page_slug()}", $this ); //phpcs:ignore
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-dialog');

        /*
    wp_enqueue_style( 'jquery-ui-smoothness', $this->get_plugin_dir_url() . 'assets/css/jquery-ui-smoothness.min.css', array(), '1.12.1' );
		wp_enqueue_script(
			'jquery-ui-timepicker-addon',
			$this->get_plugin_dir_url() . 'assets/js/jquery-ui-timepicker-addon.min.js',
			array( 'jquery-ui-slider', 'jquery-ui-datepicker' ),
			'1.6.3',
			true
		);
    */

		//wp_enqueue_style( 'jquery-ui-timepicker', $this->get_plugin_dir_url() . 'assets/css/jquery-ui-timepicker-addon.min.css', array(), '1.6.3' );

    wp_enqueue_script( 'postbox');
		wp_enqueue_script( 'select2', $this->get_plugin_dir_url() . 'assets/js/select2.min.js', array( 'jquery' ), '4.0.5', true );
		wp_enqueue_style( 'select2', $this->get_plugin_dir_url() . 'assets/css/select2.min.css', array(), '4.0.5' );

        $js_localize = array(
            'bl_nonce' => wp_create_nonce('bl-nonce'),
            'wp301_install_url' => add_query_arg(array('action' => 'bulkwp_install_wp301', '_wpnonce' => wp_create_nonce('install_wp301'), 'rnd' => wp_rand()), admin_url('admin.php')),
        );

		wp_enqueue_script(
			'bulk-delete',
			$this->get_plugin_dir_url() . 'assets/js/bulk-delete.js',
			array(),
			self::plugin_version(),
			true
		);

    wp_localize_script('bulk-delete', 'bulk_delete', $js_localize);

		wp_enqueue_style(
			'bulk-delete',
			$this->get_plugin_dir_url() . 'assets/css/bulk-delete.css',
			array(),
			self::plugin_version()
		);

		/**
		 * Filter JavaScript array.
		 *
		 * This filter can be used to extend the array that is passed to JavaScript
		 *
		 * @since 5.4
		 */
		$translation_array = apply_filters( 'bd_javascript_array',  //phpcs:ignore
			array(
				'msg'              => array(),
				'validators'       => array(),
				'dt_iterators'     => array(),
				'pre_action_msg'   => array(), // deprecated since 6.0.1.
				'pre_delete_msg'   => array(),
				'pre_schedule_msg' => array(),
				'error_msg'        => array(),
				'pro_iterators'    => array(),
			)
		);
		wp_localize_script( 'bulk-delete', 'BulkWP', $translation_array ); // TODO: Change JavaScript variable to BulkWP.BulkDelete.

		/**
		 * Runs just after enqueuing scripts and styles in all Bulk Delete admin pages.
		 *
		 * This action is primarily for registering additional scripts or styles.
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage The current page.
		 *
		 * @since 5.5.1
		 * @since 6.0.0 Added $page parameter.
		 */
		do_action( 'bd_after_admin_enqueue_scripts', $this ); //phpcs:ignore

		/**
		 * Runs just after enqueuing scripts and styles in a Bulk Delete admin pages.
		 *
		 * This action is primarily for registering or deregistering additional scripts or styles.
		 *
		 * @param \BulkWP\BulkDelete\Core\Base\BaseDeletePage The current page.
		 *
		 * @since 6.0.1
		 */
		do_action( "bd_after_enqueue_page_assets_for_{$this->get_page_slug()}", $this ); //phpcs:ignore
	}

    // auto download / install / activate WP 301 Redirects plugin
    public function install_wp301()
    {
        check_ajax_referer('install_wp301');

        if (false === current_user_can('manage_options')) {
            wp_die('Sorry, you have to be an admin to run this action.');
        }

        $plugin_slug = 'eps-301-redirects/eps-301-redirects.php';
        $plugin_zip = 'https://downloads.wordpress.org/plugin/eps-301-redirects.latest-stable.zip';

        @include_once ABSPATH . 'wp-admin/includes/plugin.php';
        @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        @include_once ABSPATH . 'wp-admin/includes/file.php';
        @include_once ABSPATH . 'wp-admin/includes/misc.php';
        echo '<style>
		body{
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.5;
			color: #444;
		}
		</style>';

        echo '<div style="margin: 20px; color:#444;">';
        echo 'If things are not done in a minute <a target="_parent" href="' . esc_url(admin_url('plugin-install.php?s=301%20redirects%20webfactory&tab=search&type=term')) . '">install the plugin manually via Plugins page</a><br><br>';
        echo 'Starting ...<br><br>';

        wp_cache_flush();
        $upgrader = new \Plugin_Upgrader();
        echo 'Check if WP 301 Redirects is already installed ... <br />';
        if ($this->is_plugin_installed($plugin_slug)) {
            echo 'WP 301 Redirects is already installed! <br /><br />Making sure it\'s the latest version.<br />';
            $upgrader->upgrade($plugin_slug);
            $installed = true;
        } else {
            echo 'Installing WP 301 Redirects.<br />';
            $installed = $upgrader->install($plugin_zip);
        }
        wp_cache_flush();

        if (!is_wp_error($installed) && $installed) {
            echo 'Activating WP 301 Redirects.<br />';
            $activate = activate_plugin($plugin_slug);

            if (is_null($activate)) {
                echo 'WP 301 Redirects Activated.<br />';

                echo '<script>setTimeout(function() { top.location = "' . esc_url(admin_url('options-general.php?page=eps_redirects')) . '"; }, 1000);</script>';
                echo '<br>If you are not redirected in a few seconds - <a href="' . esc_url(admin_url('options-general.php?page=eps_redirects')) . '" target="_parent">click here</a>.';
            }
        } else {
            echo 'Could not install WP 301 Redirects. You\'ll have to <a target="_parent" href="' . esc_url(admin_url('plugin-install.php?s=301%20redirects%20webfactory&tab=search&type=term')) . '">download and install manually</a>.';
        }

        echo '</div>';
    } // install_wp301

    /**
     * Check if given plugin is installed
     *
     * @param [string] $slug Plugin slug
     * @return boolean
     */
    static function is_plugin_installed($slug)
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        if (!empty($all_plugins[$slug])) {
            return true;
        } else {
            return false;
        }
    } // is_plugin_installed

    static function plugin_version()
    {
        $plugin_data = get_file_data(BULK_DELETE_FILE, array('version' => 'Version'), 'plugin');

        return $plugin_data['version'];
    } // get_plugin_version

    /**
     * Check for nonce before executing the action.
     *
     * @param bool   $result The current result.
     * @param string $action Action name.
     *
     * @return bool True if nonce is verified, False otherwise.
     */
    public function verify_nonce($result, $action)
    {
        /**
         * List of actions for page.
         *
         * @param array    $actions Actions.
         * @param BasePage $page    Page objects.
         *
         * @since 6.0.0
         */
        $page_actions = apply_filters('bd_page_actions', $this->actions, $this); //phpcs:ignore

        if (in_array($action, $page_actions, true)) {
            if (check_admin_referer("bd-{$this->page_slug}", "bd-{$this->page_slug}-nonce")) {
                return true;
            }
        }

        return $result;
    }

    /**
     * Setup hooks for rendering contextual help.
     */
    public function setup_contextual_help()
    {
        /**
         * Add contextual help for admin screens.
         *
         * @since 5.1
         *
         * @param string Hook suffix of the current page.
         */
    }

    /**
     * Modify help tabs for the current page.
     *
     * @param array  $help_tabs   Current list of help tabs.
     * @param string $hook_suffix Hook Suffix of the page.
     *
     * @return array Modified list of help tabs.
     */
    public function render_help_tab($help_tabs, $hook_suffix)
    {
        if ($this->hook_suffix === $hook_suffix) {
            $help_tabs = $this->add_help_tab($help_tabs);
        }

        return $help_tabs;
    }

    /**
     * Add help tabs.
     *
     * Help tabs can be added by overriding this function in the child class.
     *
     * @param array $help_tabs Current list of help tabs.
     *
     * @return array List of help tabs.
     */
    protected function add_help_tab($help_tabs)
    {
        return $help_tabs;
    }

    /**
     * Render the page.
     */
    public function render_page()
    {
?>
        <div class="wrap">
            <h2><?php echo esc_html($this->label['page_title']); ?></h2>
            <?php settings_errors(); ?>

            <form method="post">
                <?php $this->render_nonce_fields(); ?>
                <div class="bulkwp-body-wrapper">
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-1">

                            <?php $this->render_header(); ?>

                            <div id="postbox-container-2" class="postbox-container">
                                <?php $this->render_body(); ?>
                            </div> <!-- #postbox-container-2 -->

                        </div> <!-- #post-body -->
                    </div><!-- #poststuff -->
                </div>
                <div class="bulkwp-sidebar-wrapper">
                    <?php bd_wp_kses_wf(\Bulk_Delete::sidebar()); ?>
                </div>
            </form>
        </div><!-- .wrap -->
    <?php
        $this->render_footer();
    }

    /**
     * Print nonce fields.
     */
    protected function render_nonce_fields()
    {
        wp_nonce_field("bd-{$this->page_slug}", "bd-{$this->page_slug}-nonce");
    }

    /**
     * Render page header.
     */
    protected function render_header()
    {
        if (empty($this->messages['warning_message'])) {
            return;
        }
    ?>
        <div class="notice notice-warning">
            <p>
                <strong>
                    <?php echo esc_html($this->messages['warning_message']); ?>
                </strong>
            </p>
        </div>
<?php
    }

    /**
     * Render page footer.
     */
    protected function render_footer()
    {
        /**
         * Runs just before displaying the footer text in the admin page.
         *
         * This action is primarily for adding extra content in the footer of admin page.
         *
         * @since 5.5.4
         */
        do_action("bd_admin_footer_for_{$this->page_slug}"); //phpcs:ignore
    }

    /**
     * Modify admin footer in Bulk Delete plugin pages.
     */
    public function modify_admin_footer()
    {
        add_filter('admin_footer_text', 'bd_add_rating_link');
    }

    /**
     * Append link to the current page in plugin list.
     *
     * @param array $links Array of links.
     *
     * @return array Modified list of links.
     */
    public function append_to_plugin_action_links($links)
    {
        $links[$this->get_page_slug()] = '<a href="admin.php?page=' . $this->get_page_slug() . '">' . $this->label['page_title'] . '</a>';

        return $links;
    }

    /**
     * Getter for screen.
     *
     * @return \WP_Screen Current screen.
     */
    public function get_screen()
    {
        return $this->screen;
    }

    /**
     * Getter for page_slug.
     *
     * @return string Slug of the page.
     */
    public function get_page_slug()
    {
        return $this->page_slug;
    }

    /**
     * Getter for Hook Suffix.
     *
     * @return string Hook Suffix of the page.
     */
    public function get_hook_suffix()
    {
        return $this->hook_suffix;
    }

    /**
     * Get the url to the plugin directory.
     *
     * @return string Url to plugin directory.
     */
    protected function get_plugin_dir_url()
    {
        return plugin_dir_url($this->plugin_file);
    }
}
