<?php

// Prevent direct access
if (basename($_SERVER['SCRIPT_FILENAME']) === 'ComposerInfo.php') {
    header("HTTP/1.0 403 Forbidden");
    exit('Access Denied');
}

class ComposerInfo
{
    public static function getComposerInfo(XoopsTpl $xoopsTpl)
    {
        global $xoopsLogger;

        try {
            // Define the path to the composer.lock file
            $composerLockPath = XOOPS_TRUST_PATH . '/composer';
            // Get the packages data from composer.lock file
            $packages = self::readComposerLockFile($composerLockPath);
            // Extract package name and version
            $composerPackages = self::extractPackageNamesAndVersions($packages);
            // Assign the $composerPackages array to the Smarty template
            $xoopsTpl->assign('composerPackages', $composerPackages);
        } catch (Exception $e) {
            // Handle any exception and log the error using XOOPS Logger
            $xoopsLogger->handleError(E_USER_ERROR, $e->getMessage(), __FILE__, __LINE__);
            echo "An error occurred. Please try again later.";
        }
    }

    // Function to read and parse composer.lock file
    private static function readComposerLockFile(string $composerLockPath): array
    {
        $composerLockFile = $composerLockPath . '.lock';
        if (!file_exists($composerLockFile)) {
            $composerLockFile = $composerLockPath . '.dist.lock';
        }
        if (!file_exists($composerLockFile)) {
            throw new InvalidArgumentException("Failed to read the file: " . $composerLockFile);
        }

        $composerLockData = file_get_contents($composerLockFile);

        if ($composerLockData === false) {
            throw new RuntimeException("Failed to read the file: " . $composerLockFile);
        }

        $composerData = json_decode($composerLockData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException("Failed to decode JSON data: " . json_last_error_msg());
        }

        return $composerData['packages'] ?? [];
    }

    // Function to extract package name and version (using array_map for optimization)
    private static function extractPackageNamesAndVersions(array $packages): array
    {
        return array_map(
            static fn($package) => [
                'name'    => $package['name'],
                'version' => $package['version'],
            ],
            $packages,
        );
    }
}
