<?php
class Logger {
    protected $file;
    public function __construct($file) {
        $this->file = $file;
    }

    protected function read() {
        if (!file_exists($this->file)) {
            return [];
        }
        $data = json_decode(file_get_contents($this->file), true);
        return is_array($data) ? $data : [];
    }

    protected function write($logs) {
        file_put_contents($this->file, json_encode($logs, JSON_PRETTY_PRINT));
    }

    public function log($repo, $branch, $folder, $success, $message = '') {
        $logs = $this->read();
        $logs[] = [
            'time' => date('c'),
            'repo' => $repo,
            'branch' => $branch,
            'folder' => $folder,
            'success' => $success,
            'message' => $message
        ];
        $this->write($logs);
    }
}
?>
