<?php
require __DIR__ . '/src/Api.php';
require __DIR__ . '/src/Filesystem.php';
require __DIR__ . '/src/Logger.php';

$settingsFile = __DIR__ . '/settings.json';
$mappingFile  = __DIR__ . '/mapping.json';
$logFile      = __DIR__ . '/logs.json';

$settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];
$mappings = file_exists($mappingFile) ? json_decode(file_get_contents($mappingFile), true) : [];
$token    = isset($settings['token']) ? $settings['token'] : '';

$api   = new Api($token);
$fs    = new Filesystem();
$logger = new Logger($logFile);

$cmd = $argv[1] ?? '';

switch ($cmd) {
    case 'list':
        $repos = $api->getRepositories();
        foreach ($repos as $repo) {
            echo $repo['full_name'] . "\n";
        }
        break;

    case 'map':
        $repo = $argv[2] ?? '';
        $folder = $argv[3] ?? '';
        if (!$repo || !$folder) {
            echo "Usage: php deploy.php map <repo> <folder>\n";
            exit(1);
        }
        $mappings[$repo] = $folder;
        file_put_contents($mappingFile, json_encode($mappings, JSON_PRETTY_PRINT));
        echo "Mapping saved.\n";
        break;

    case 'deploy':
        $repo = $argv[2] ?? '';
        if (!$repo) {
            echo "Usage: php deploy.php deploy <repo>\n";
            exit(1);
        }
        if (empty($mappings[$repo])) {
            echo "No mapping found for $repo.\n";
            exit(1);
        }
        $folder = $mappings[$repo];
        echo "Deploying $repo to $folder\n";
        $zip = $api->downloadRepoZip($repo);
        if (!$zip) {
            echo "Download failed\n";
            $logger->log($repo, 'default', $folder, false, 'Download failed');
            exit(1);
        }
        $fs->ensureDir($folder);
        $success = $fs->unzip($zip, $folder, true);
        unlink($zip);
        $logger->log($repo, 'default', $folder, $success, $success ? '' : 'Unzip failed');
        echo $success ? "Deployment complete\n" : "Deployment failed\n";
        break;

    default:
        echo "Usage: php deploy.php [list|map|deploy]\n";
        exit(1);
}
?>
