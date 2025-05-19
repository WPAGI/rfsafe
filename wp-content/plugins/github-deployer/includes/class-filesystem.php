<?php
namespace GitHub_Deployer;

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * Filesystem handler.
 */
class Filesystem {
/**
 * WP_Filesystem_Base instance.
 *
 * @var \WP_Filesystem_Base
 */
protected $fs;

/**
 * Constructor.
 */
public function __construct() {
require_once ABSPATH . 'wp-admin/includes/file.php';
WP_Filesystem();
global $wp_filesystem;
$this->fs = $wp_filesystem;
}

/**
 * Ensure directory exists.
 *
 * @param string $path Path.
 *
 * @return bool
 */
public function ensure_dir( $path ) {
if ( ! $this->fs->is_dir( $path ) ) {
return $this->fs->mkdir( $path, FS_CHMOD_DIR, true );
}

return true;
}

/**
 * Check if directory is empty.
 *
 * @param string $path Path.
 *
 * @return bool
 */
public function is_empty( $path ) {
$files = $this->fs->dirlist( $path );

return empty( $files );
}

/**
 * Unzip file into destination.
 *
 * @param string $zip_file Zip file path.
 * @param string $destination Destination folder.
 * @param bool   $overwrite   Overwrite existing files.
 *
 * @return bool
 */
public function unzip( $zip_file, $destination, $overwrite = false ) {
$unzip_result = unzip_file( $zip_file, $destination, $overwrite );

if ( is_wp_error( $unzip_result ) ) {
return false;
}

return true;
}
}
