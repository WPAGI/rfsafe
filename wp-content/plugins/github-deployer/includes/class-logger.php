<?php
namespace GitHub_Deployer;

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * Logger for deployments.
 */
class Logger {
/**
 * Insert log entry.
 *
 * @param string $repo   Repo full name.
 * @param string $branch Branch name.
 * @param string $folder Target folder.
 * @param bool   $success Success flag.
 * @param string $message Message.
 */
public function log( $repo, $branch, $folder, $success, $message = '' ) {
global $wpdb;
$table = $wpdb->prefix . 'gd_logs';

$wpdb->insert(
$table,
array(
'repo'    => $repo,
'branch'  => $branch,
'folder'  => $folder,
'success' => $success ? 1 : 0,
'message' => $message,
'time'    => current_time( 'mysql' ),
)
);
}

/**
 * Get last logs.
 *
 * @param int $limit Number of logs.
 *
 * @return array
 */
public function get_logs( $limit = 10 ) {
global $wpdb;
$table = $wpdb->prefix . 'gd_logs';

return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY time DESC LIMIT %d", $limit ) );
}
}
