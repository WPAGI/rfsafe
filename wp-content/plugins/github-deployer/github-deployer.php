<?php
/**
 * Plugin Name: GitHub Deployer
 * Plugin URI: https://example.com/github-deployer
 * Description: Deploy GitHub repositories to your server directly from WordPress.
 * Version: 1.0.0
 * Author: OpenAI Assistant
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-github-deployer.php';

function github_deployer_init() {
\GitHub_Deployer\GitHub_Deployer::get_instance();
}
add_action( 'plugins_loaded', 'github_deployer_init' );


