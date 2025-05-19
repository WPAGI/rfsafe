<?php
namespace GitHub_Deployer;

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * Main plugin class.
 */
class GitHub_Deployer {
/**
 * Plugin version.
 *
 * @var string
 */
const VERSION = '1.0.0';

/**
 * Single instance.
 *
 * @var GitHub_Deployer
 */
protected static $instance = null;

/**
 * API handler.
 *
 * @var API
 */
public $api;

/**
 * UI handler.
 *
 * @var UI
 */
public $ui;

/**
 * Filesystem handler.
 *
 * @var Filesystem
 */
public $filesystem;

/**
 * Logger handler.
 *
 * @var Logger
 */
public $logger;

/**
 * Get singleton instance.
 *
 * @return GitHub_Deployer
 */
public static function get_instance() {
if ( null === self::$instance ) {
self::$instance = new self();
}

return self::$instance;
}

/**
 * Constructor.
 */
private function __construct() {
$this->includes();
$this->init_classes();
$this->hooks();
}

/**
 * Include required files.
 */
private function includes() {
require_once __DIR__ . '/class-api.php';
require_once __DIR__ . '/class-filesystem.php';
require_once __DIR__ . '/class-logger.php';
require_once __DIR__ . '/class-ui.php';
}

/**
 * Initialize class instances.
 */
private function init_classes() {
$this->filesystem = new Filesystem();
$this->logger     = new Logger();
$this->api        = new API( $this->get_token() );
$this->ui         = new UI( $this );
}

/**
 * Register hooks.
 */
private function hooks() {
register_activation_hook( plugin_dir_path( __DIR__ ) . 'github-deployer.php', array( $this, 'activate' ) );
add_action( 'admin_init', array( $this, 'admin_init' ) );
}

/**
 * Plugin activation handler.
 */
public function activate() {
global $wpdb;
$table_name      = $wpdb->prefix . 'gd_logs';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
id bigint(20) unsigned NOT NULL auto_increment,
repo varchar(200) NOT NULL,
branch varchar(200) NOT NULL,
folder varchar(255) NOT NULL,
success tinyint(1) NOT NULL,
message text NULL,
time datetime NOT NULL,
PRIMARY KEY  (id)
) {$charset_collate};";

require_once ABSPATH . 'wp-admin/includes/upgrade.php';
dbDelta( $sql );
}

/**
 * Register settings.
 */
public function admin_init() {
register_setting( 'github_deployer', 'gd_github_token', array( $this, 'sanitize_token' ) );
}

/**
 * Sanitize GitHub token.
 *
 * @param string $token Token to sanitize.
 *
 * @return string
 */
public function sanitize_token( $token ) {
return sanitize_text_field( $token );
}

/**
 * Get saved GitHub token.
 *
 * @return string
 */
public function get_token() {
return get_option( 'gd_github_token', '' );
}

/**
 * Set GitHub token.
 *
 * @param string $token Token to save.
 */
public function set_token( $token ) {
update_option( 'gd_github_token', sanitize_text_field( $token ) );
$this->api->set_token( $token );
}
}
