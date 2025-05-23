<?php
class Api {
    protected $token;
    public function __construct($token = '') {
        $this->token = $token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    protected function request($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github.v3+json',
            'Authorization: token ' . $this->token,
            'User-Agent: Standalone-Deployer'
        ]);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status >= 200 && $status < 300) {
            return $data;
        }
        return false;
    }

    public function getRepositories() {
        $resp = $this->request('https://api.github.com/user/repos');
        if (!$resp) {
            return [];
        }
        $data = json_decode($resp, true);
        return is_array($data) ? $data : [];
    }

    public function downloadRepoZip($fullName) {
        $url = 'https://api.github.com/repos/' . $fullName . '/zipball';
        $resp = $this->request($url);
        if (!$resp) {
            return false;
        }
        $tmp = tempnam(sys_get_temp_dir(), 'repo_') . '.zip';
        file_put_contents($tmp, $resp);
        return $tmp;
    }
}
?>
