<?php
namespace GitHub_Deployer;

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * Admin UI handler.
 */
class UI {
/**
 * Plugin instance.
 *
 * @var GitHub_Deployer
 */
protected $plugin;

/**
 * Constructor.
 *
 * @param GitHub_Deployer $plugin Plugin instance.
 */
public function __construct( $plugin ) {
$this->plugin = $plugin;
add_action( 'admin_menu', array( $this, 'admin_menu' ) );
add_action( 'admin_post_gd_save_token', array( $this, 'save_token' ) );
        add_action( 'admin_post_gd_save_mapping', array( $this, 'save_mapping' ) );
        add_action( 'admin_post_gd_deploy_repo', array( $this, 'deploy_repo' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_gd_browse_dir', array( $this, 'ajax_browse_dir' ) );
    }

/**
 * Enqueue admin assets.
 */
public function enqueue_assets() {
wp_enqueue_style( 'gd-admin', plugins_url( '../assets/css/admin.css', __FILE__ ), array(), GitHub_Deployer::VERSION );
    wp_enqueue_script( 'gd-admin', plugins_url( '../assets/js/admin.js', __FILE__ ), array( 'jquery' ), GitHub_Deployer::VERSION, true );
    wp_localize_script( 'gd-admin', 'gd_vars', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ) );
}

/**
 * Add admin menu.
 */
public function admin_menu() {
add_submenu_page(
'tools.php',
'GitHub Deployer',
'GitHub Deployer',
'manage_options',
'github-deployer',
array( $this, 'settings_page' )
);
}

/**
 * Render settings page.
 */
public function settings_page() {
if ( ! current_user_can( 'manage_options' ) ) {
return;
}

$token     = $this->plugin->get_token();
$mappings  = get_option( 'gd_repo_mappings', array() );
$repos     = array();
$error     = '';

if ( $token ) {
$repos = $this->plugin->api->get_repositories();
if ( empty( $repos ) ) {
$error = 'Unable to fetch repositories. Check your token.';
}
}

$logs = $this->plugin->logger->get_logs( 10 );

include plugin_dir_path( __FILE__ ) . '../views/settings-page.php';
}

/**
 * Save GitHub token.
 */
public function save_token() {
check_admin_referer( 'gd_save_token' );

if ( ! current_user_can( 'manage_options' ) ) {
wp_die( 'Unauthorized.' );
}

$this->plugin->set_token( $_POST['github_token'] );

wp_redirect( admin_url( 'tools.php?page=github-deployer' ) );
exit;
}

/**
 * Save mapping for repo.
 */
public function save_mapping() {
check_admin_referer( 'gd_save_mapping' );

if ( ! current_user_can( 'manage_options' ) ) {
wp_die( 'Unauthorized.' );
}

$repo     = sanitize_text_field( $_POST['repo'] );
$folder   = sanitize_text_field( $_POST['folder'] );
$mappings = get_option( 'gd_repo_mappings', array() );
$mappings[ $repo ] = $folder;
update_option( 'gd_repo_mappings', $mappings );

wp_redirect( admin_url( 'tools.php?page=github-deployer' ) );
exit;
}

/**
 * Deploy repository.
 */
    public function deploy_repo() {
        $repo   = sanitize_text_field( $_GET['repo'] );
$mappings = get_option( 'gd_repo_mappings', array() );

if ( empty( $mappings[ $repo ] ) ) {
wp_die( 'No folder mapping for this repository.' );
}

$folder = $mappings[ $repo ];
$confirm = isset( $_GET['confirm'] );

if ( ! $confirm && ! $this->plugin->filesystem->is_empty( $folder ) ) {
wp_die( 'Folder not empty. <a href="' . esc_url( add_query_arg( 'confirm', '1' ) ) . '">Confirm overwrite</a>' );
}

$zip = $this->plugin->api->download_repo_zip( $repo );
$success = false;
$message = '';

if ( $zip ) {
$this->plugin->filesystem->ensure_dir( $folder );
$success = $this->plugin->filesystem->unzip( $zip, $folder, true );
if ( ! $success ) {
$message = 'Unzip failed.';
}
@unlink( $zip );
} else {
$message = 'Download failed.';
}

$this->plugin->logger->log( $repo, 'default', $folder, $success, $message );

        wp_redirect( admin_url( 'tools.php?page=github-deployer' ) );
        exit;
    }

    /**
     * AJAX handler to browse directories.
     */
    public function ajax_browse_dir() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized', 403 );
        }

        $path = isset( $_GET['path'] ) ? sanitize_text_field( wp_unslash( $_GET['path'] ) ) : '/';
        if ( '' === $path ) {
            $path = '/';
        }

        $folders = $this->plugin->filesystem->list_folders( $path );

        wp_send_json_success( array(
            'path'    => $path,
            'folders' => $folders,
        ) );
    }
}
