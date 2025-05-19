<?php
namespace GitHub_Deployer;

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * GitHub API handler.
 */
class API {
/**
 * GitHub token.
 *
 * @var string
 */
protected $token;

/**
 * Constructor.
 *
 * @param string $token Token.
 */
public function __construct( $token = '' ) {
$this->token = $token;
}

/**
 * Set token.
 *
 * @param string $token Token.
 */
public function set_token( $token ) {
$this->token = sanitize_text_field( $token );
}

/**
 * Get headers for request.
 *
 * @return array
 */
protected function get_headers() {
$headers = array(
'Accept'        => 'application/vnd.github.v3+json',
'Authorization' => 'token ' . $this->token,
);

return $headers;
}

/**
 * Get repositories for authenticated user.
 *
 * @return array
 */
public function get_repositories() {
$response = wp_remote_get( 'https://api.github.com/user/repos', array(
'headers' => $this->get_headers(),
) );

if ( is_wp_error( $response ) ) {
return array();
}

$body = wp_remote_retrieve_body( $response );
$repos = json_decode( $body, true );

if ( ! is_array( $repos ) ) {
return array();
}

return $repos;
}

/**
 * Download repository zip.
 *
 * @param string $full_name Repo full name (owner/repo).
 *
 * @return string|false Path to downloaded zip or false.
 */
public function download_repo_zip( $full_name ) {
$url      = 'https://api.github.com/repos/' . $full_name . '/zipball';
$tmp_file = download_url( $url, 300, array(), $this->get_headers() );

if ( is_wp_error( $tmp_file ) ) {
return false;
}

return $tmp_file;
}
}
