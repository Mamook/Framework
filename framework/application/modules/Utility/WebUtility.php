<?php

namespace Mamook\WebUtility;

use Mamook\IP\IP;
use Mamook\Utility\Utility;

# Make sure the script is not accessed directly.
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}

/**
 * WebUtility
 * The WebUtility class is used to for miscellaneous utility
 * methods that must be used on scripts in a browser.
 * Most methods here are static.
 */
class WebUtility extends Utility
{
    /**
     * A wrapper method for findIP() from the IP calss.
     * Returns the IP of the visitor.
     * Throws an error if the IP address is not valid.
     *
     * @param    bool $for_insert_query Convert IP addresss to binary for database.
     *
     * @return    string
     * @throws \Mamook\ExceptionHandler\Exception
     */
    public static function findIP($for_insert_query = true)
    {
        # Create a new IP object.
        $ip_obj = IP::getInstance();
        # Set the visitor's IP addreess.
        #	Use $_SERVER over getenv() since it's more server compatible.
        #	If $_SERVER['REMOTE_ADDR'] is empty, use getenv().
        $ip = $ip_obj->findIP($for_insert_query);

        # Return the visitor's IP address.
        return $ip;
    } #==== End -- findIP

    /**
     * Removes GET query from the passed URL.
     * Must be called before removeIndex method.
     *
     * @param string $url The URL to check.
     *
     * @return mixed|string
     */
    public static function removeGetQuery($url)
    {
        # Create a variable wth the default value of the passed URL.
        $alt_url = str_ireplace(GET_QUERY, '', $url);
        # Check if the link has a page query.
        if (!empty($alt_url)) {
            $url = $alt_url;
        }

        return $url;
    } #==== End -- removeGetQuery

    /**
     * Removes "?page=#" query from the passed URL.
     *
     * @param string $url The URL to check.
     *
     * @return null|string|string[]
     */
    public static function removePageQuery($url)
    {
        # Check if the link has a page query.
        if (strpos($url, '?page=') !== false) {
            $url = preg_replace('/page\=[0-9]+\&/', '', $url);
        }

        return $url;
    } #==== End -- removePageQuery

    /**
     * Removes scheme name (ie http://) from the passed URL.
     *
     * @param string $url The URL to check.
     *
     * @return null|string|string[]
     */
    public static function removeSchemeName($url)
    {
        # Check if the link has a scheme name.
        if (preg_match('/^((https?|s?ftp)\:\/\/)|(mailto\:)/', $url) !== 0) {
            $url = preg_replace('/^((https?|s?ftp)\:\/\/)|(mailto\:)/', '', $url, 1);
        }

        return $url;
    } #==== End -- removeSchemeName
}
