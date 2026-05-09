<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Utility for probing audio/video media files for metadata such as duration.
 *
 * Uses `ffprobe` when available. All methods return null when the binary is
 * missing, the file does not exist, or the path falls outside of an allowed
 * directory. The class never throws on probing failures so that callers can
 * gracefully degrade.
 */
final class MediaProbe
{
    /** Cached path to the ffprobe binary, or false when unavailable. */
    private static string|false|null $ffprobeBinary = null;

    /**
     * Probe the duration (in whole seconds) of a media file given an absolute path.
     */
    public static function durationFromAbsolutePath(string $absolutePath): ?int
    {
        if ($absolutePath === '' || !is_file($absolutePath) || !is_readable($absolutePath)) {
            return null;
        }

        $binary = self::resolveFfprobeBinary();
        if ($binary === false) {
            return null;
        }

        $command = sprintf(
            '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>/dev/null',
            escapeshellcmd($binary),
            escapeshellarg($absolutePath)
        );

        $output = @shell_exec($command);
        if ($output === null || $output === false) {
            return null;
        }

        $output = trim((string) $output);
        if ($output === '' || !is_numeric($output)) {
            return null;
        }

        $seconds = (int) round((float) $output);
        return $seconds > 0 ? $seconds : null;
    }

    /**
     * Probe duration starting from a public web path (e.g. "/media/foo.mp4").
     * The web path is constrained to the provided public directory to avoid
     * escaping out of the media folder.
     */
    public static function durationFromWebPath(string $webPath, string $publicDir): ?int
    {
        $absolute = self::resolveSafeAbsolutePath($webPath, $publicDir);
        if ($absolute === null) {
            return null;
        }
        return self::durationFromAbsolutePath($absolute);
    }

    /**
     * Convert a `/foo/bar.mp4` web path to an absolute path under $publicDir,
     * rejecting traversal attempts. Returns null if the resolved path falls
     * outside of $publicDir.
     */
    public static function resolveSafeAbsolutePath(string $webPath, string $publicDir): ?string
    {
        if ($webPath === '' || $publicDir === '') {
            return null;
        }
        $publicReal = realpath($publicDir);
        if ($publicReal === false) {
            return null;
        }

        $relative = ltrim($webPath, '/');
        $candidate = $publicReal . DIRECTORY_SEPARATOR . $relative;
        $candidateReal = realpath($candidate);
        if ($candidateReal === false) {
            return null;
        }

        $publicWithSep = rtrim($publicReal, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($candidateReal, $publicWithSep)) {
            return null;
        }
        return $candidateReal;
    }

    /**
     * Reset the cached ffprobe lookup. Intended for tests.
     */
    public static function resetCache(): void
    {
        self::$ffprobeBinary = null;
    }

    private static function resolveFfprobeBinary(): string|false
    {
        if (self::$ffprobeBinary !== null) {
            return self::$ffprobeBinary;
        }
        $candidate = @shell_exec('command -v ffprobe 2>/dev/null');
        if (is_string($candidate)) {
            $candidate = trim($candidate);
            if ($candidate !== '' && is_executable($candidate)) {
                return self::$ffprobeBinary = $candidate;
            }
        }
        return self::$ffprobeBinary = false;
    }
}
