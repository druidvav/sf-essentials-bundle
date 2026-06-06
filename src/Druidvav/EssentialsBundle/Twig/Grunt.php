<?php

namespace Druidvav\EssentialsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Grunt extends AbstractExtension
{
    protected string $gruntAssetManifestPath = '';
    protected string $projectDir = '';
    protected bool $debug = false;

    public function setGruntAssetManifestPath(string $gruntAssetManifestPath): void
    {
        $this->gruntAssetManifestPath = $gruntAssetManifestPath;
    }

    public function setProjectDir(string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('grunt_asset', array($this, 'gruntAsset')),
        );
    }

    public function gruntAsset($string): string
    {
        $assetsFilename = $this->gruntAssetManifestPath;
        if (file_exists($assetsFilename)) {
            $data = file_get_contents($assetsFilename);
            if (empty($data)) {
                return $this->appendDebugVersion('/'.$string);
            }
            $assets = json_decode($data, true);
            if (empty($assets)) {
                return $this->appendDebugVersion('/'.$string);
            }
            foreach ($assets as $asset) {
                if ($asset['originalPath'] == $string) {
                    return $this->appendDebugVersion('/'.$asset['versionedPath']);
                }
            }
        }

        return $this->appendDebugVersion('/'.$string);
    }

    private function appendDebugVersion(string $path): string
    {
        if (!$this->debug || '' === $this->projectDir) {
            return $path;
        }

        $pathOnly = parse_url($path, PHP_URL_PATH);
        if (!is_string($pathOnly) || '' === $pathOnly) {
            return $path;
        }

        $filePath = rtrim($this->projectDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.ltrim($pathOnly, '/');
        if (!is_file($filePath)) {
            return $path;
        }

        $hash = hash_file('sha1', $filePath);
        if (false === $hash) {
            return $path;
        }

        $separator = false === strpos($path, '?') ? '?' : '&';

        return $path.$separator.'v='.$hash;
    }
}
