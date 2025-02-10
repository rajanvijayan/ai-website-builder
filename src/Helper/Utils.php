<?php

namespace AIWebsiteBuilder\Helper;

class Utils {

    /**
     * Get a transient value.
     *
     * @param string $key The transient key.
     * @return mixed The transient value or false if not found.
     */
    public static function getTransient($key) {
        return get_transient($key);
    }

    /**
     * Set a transient value.
     *
     * @param string $key The transient key.
     * @param mixed $value The value to store.
     * @param int $expiration Expiration time in seconds.
     * @return bool True on success, false on failure.
     */
    public static function setTransient($key, $value, $expiration = 3600) {
        return set_transient($key, $value, $expiration);
    }

    /**
     * Delete a transient value.
     *
     * @param string $key The transient key.
     * @return bool True on success, false on failure.
     */
    public static function deleteTransient($key) {
        return delete_transient($key);
    }

    /**
     * Sanitize a string to be used in URLs or filenames.
     *
     * @param string $string The input string.
     * @return string The sanitized string.
     */
    public static function sanitizeString($string) {
        return sanitize_title($string);
    }

    /**
     * Log a message for debugging.
     *
     * @param string $message The message to log.
     */
    public static function log($message) {
        if (WP_DEBUG === true) {
            error_log('[AIWebsiteBuilder] ' . $message);
        }
    }

    public static function cleanJSON($json) {
        $json = preg_replace('/^```(json)?\s*|\s*```$/i', '', trim($json));

        // Decode to check if it's valid JSON
        $decoded = json_decode($json, true);
        
        // If JSON is valid, return as a properly formatted JSON string
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return false; // Return false if JSON is invalid
    }

    public static function cleanHTML($html) {
        $html = preg_replace('/^```(html)?\s*|\s*```$/i', '', trim($html));
        return $html;
    }

}