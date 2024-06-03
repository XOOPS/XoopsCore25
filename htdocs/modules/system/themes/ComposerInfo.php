<?php
header('HTTP/1.0 404 Not Found');

class ComposerInfo
{
    public static function getComposerInfo(XoopsTpl $xoopsTpl)
    {
        global $xoopsLogger;

        try {
            // Define the path to the composer.lock file
            $composerLockPath = XOOPS_ROOT_PATH . '/class/libraries/composer';
            // Get the packages data from composer.lock file
            $packages = self::getComposerData($composerLockPath);
            // Extract package name and version
            $composerPackages = self::extractPackages($packages);
            // Assign the $composerPackages array to the Smarty template
            $xoopsTpl->assign('composerPackages', $composerPackages);
        } catch (Exception $e) {
            // Handle any exception and log the error using XOOPS Logger
            $xoopsLogger->handleError(E_USER_ERROR, $e->getMessage(), __FILE__, __LINE__);
            echo "An error occurred. Please try again later.";
        }
    }

    // Function to read and parse composer.lock file
    private static function getComposerData(string $composerLockPath): array
    {
        $composerkLock = $composerLockPath . '.lock';
        if (!file_exists($composerkLock)) {
            $composerLockPathDist = $composerLockPath . '.dist.lock';
            if (!file_exists($composerLockPathDist)) {
                throw new InvalidArgumentException("File not found at: " . $composerLockPath);
            }
            $composerLockPath = $composerLockPathDist;
        }

        $composerLockData = file_get_contents($composerLockPath);

        if ($composerLockData === false) {
            throw new RuntimeException("Failed to read the file: " . $composerLockPath);
        }

        $composerData = json_decode($composerLockData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException("Failed to decode JSON data: " . json_last_error_msg());
        }

        return $composerData['packages'] ?? [];
    }



    // Function to extract package name and version (using array_map for optimization)
    private  static function extractPackages(array $packages): array
    {
        return array_map(
            static fn($package) => [
                'name'    => $package['name'],
                'version' => $package['version']
            ], $packages
        );
    }




















}
