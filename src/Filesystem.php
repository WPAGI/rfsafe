<?php
class Filesystem {
    public function ensureDir($path) {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }

    public function isEmpty($path) {
        if (!is_dir($path)) {
            return true;
        }
        $files = array_diff(scandir($path), ['.', '..']);
        return empty($files);
    }

    public function unzip($zipFile, $destination, $overwrite = false) {
        $zip = new ZipArchive();
        if ($zip->open($zipFile) === true) {
            $res = $zip->extractTo($destination);
            $zip->close();
            return $res;
        }
        return false;
    }
}
?>
