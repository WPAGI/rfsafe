# GitHub Deployer (Standalone)

This repository includes a simple script for deploying GitHub repositories to
folders on your server.

## Configuration

1. Create a `settings.json` file in the project root (one is included) and set
your GitHub personal access token:

```json
{
  "token": "your-github-token"
}
```

2. Repository folder mappings are stored in `mapping.json` as
`{"owner/repo": "/path/to/folder"}`.

3. Deployment logs are written to `logs.json`.

## Usage

Run the script from the command line:

- `php deploy.php list` – list repositories available on GitHub.
- `php deploy.php map <repo> <folder>` – map a repository to a folder.
- `php deploy.php deploy <repo>` – download and extract the mapped repository.

The script uses `curl` and `ZipArchive` and requires PHP with those extensions
enabled.
