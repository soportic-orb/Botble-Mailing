<?php

namespace Botble\Mailing\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class UpdateService
{
    protected const CACHE_KEY = 'mailing_update_check';

    public function getRepository(): string
    {
        $repository = trim((string) setting('mailing_github_repository', 'soportic-orb/botble-mailing'));

        return $repository ?: 'soportic-orb/botble-mailing';
    }

    public function getPluginPath(string $path = ''): string
    {
        return rtrim(plugin_path('mailing'), '/') . ($path ? '/' . ltrim($path, '/') : '');
    }

    public function getCurrentVersion(): string
    {
        try {
            $file = $this->getPluginPath('plugin.json');

            if (! File::exists($file)) {
                return '0.0.0';
            }

            $data = json_decode(File::get($file), true);

            return (string) ($data['version'] ?? '0.0.0');
        } catch (Throwable) {
            return '0.0.0';
        }
    }

    /**
     * Check the latest release published on GitHub.
     */
    public function check(bool $force = false): array
    {
        $current = $this->getCurrentVersion();

        if ($force) {
            Cache::forget(self::CACHE_KEY);
        }

        try {
            $release = Cache::remember(self::CACHE_KEY, now()->addMinutes(30), function () {
                return $this->fetchLatestRelease();
            });
        } catch (Throwable $exception) {
            return [
                'current' => $current,
                'latest' => null,
                'has_update' => false,
                'release' => null,
                'error' => $exception->getMessage(),
            ];
        }

        if (! $release) {
            return [
                'current' => $current,
                'latest' => null,
                'has_update' => false,
                'release' => null,
                'error' => trans('plugins/mailing::mailing.update.no_release'),
            ];
        }

        $latest = ltrim((string) $release['tag'], 'vV');

        return [
            'current' => $current,
            'latest' => $latest,
            'has_update' => version_compare($latest, $current, '>'),
            'release' => $release,
            'error' => null,
        ];
    }

    protected function fetchLatestRelease(): ?array
    {
        $repository = $this->getRepository();

        $response = $this->github("https://api.github.com/repos/{$repository}/releases/latest");

        if ($response->successful()) {
            $data = $response->json();

            return [
                'tag' => (string) ($data['tag_name'] ?? ''),
                'name' => (string) ($data['name'] ?? $data['tag_name'] ?? ''),
                'body' => (string) ($data['body'] ?? ''),
                'published_at' => (string) ($data['published_at'] ?? ''),
                'zipball_url' => (string) ($data['zipball_url'] ?? ''),
            ];
        }

        // No releases yet: fall back to the latest tag.
        $response = $this->github("https://api.github.com/repos/{$repository}/tags?per_page=1");

        if ($response->successful() && ! empty($response->json())) {
            $tag = $response->json()[0];

            return [
                'tag' => (string) ($tag['name'] ?? ''),
                'name' => (string) ($tag['name'] ?? ''),
                'body' => '',
                'published_at' => '',
                'zipball_url' => (string) ($tag['zipball_url'] ?? ''),
            ];
        }

        return null;
    }

    /**
     * Download the latest release from GitHub and update the plugin in place (OTA).
     */
    public function update(): array
    {
        $check = $this->check(true);

        if ($check['error']) {
            return ['success' => false, 'message' => $check['error']];
        }

        if (! $check['has_update']) {
            return ['success' => false, 'message' => trans('plugins/mailing::mailing.update.already_latest')];
        }

        $release = $check['release'];
        $repository = $this->getRepository();
        $zipUrl = $release['zipball_url'] ?: "https://api.github.com/repos/{$repository}/zipball/{$release['tag']}";

        $workingDir = storage_path('app/mailing-ota-' . Str::random(8));

        try {
            File::ensureDirectoryExists($workingDir);

            $response = Http::withHeaders($this->githubHeaders())
                ->timeout(300)
                ->get($zipUrl);

            if (! $response->successful()) {
                throw new Exception(trans('plugins/mailing::mailing.update.download_failed', ['status' => $response->status()]));
            }

            $zipPath = $workingDir . '/update.zip';
            File::put($zipPath, $response->body());

            $extractPath = $workingDir . '/extracted';
            File::ensureDirectoryExists($extractPath);

            $zip = new ZipArchive();

            if ($zip->open($zipPath) !== true) {
                throw new Exception(trans('plugins/mailing::mailing.update.zip_failed'));
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $source = $this->findPluginRoot($extractPath);

            if (! $source) {
                throw new Exception(trans('plugins/mailing::mailing.update.invalid_package'));
            }

            File::copyDirectory($source, $this->getPluginPath());

            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('cache:clear');

            Cache::forget(self::CACHE_KEY);

            return [
                'success' => true,
                'message' => trans('plugins/mailing::mailing.update.updated_success', ['version' => $check['latest']]),
            ];
        } catch (Throwable $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        } finally {
            File::deleteDirectory($workingDir);
        }
    }

    /**
     * GitHub zipballs contain a single top level folder (owner-repo-hash);
     * locate the folder that actually contains plugin.json.
     */
    protected function findPluginRoot(string $extractPath): ?string
    {
        if (File::exists($extractPath . '/plugin.json')) {
            return $extractPath;
        }

        foreach (File::directories($extractPath) as $directory) {
            if (File::exists($directory . '/plugin.json')) {
                return $directory;
            }
        }

        return null;
    }

    protected function github(string $url)
    {
        return Http::withHeaders($this->githubHeaders())->timeout(20)->get($url);
    }

    protected function githubHeaders(): array
    {
        $headers = [
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => 'Botble-Mailing-Plugin',
        ];

        $token = setting('mailing_github_token');

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }
}
