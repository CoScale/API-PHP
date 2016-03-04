<?php
namespace Coscale;
/**
 * In order for this class to function properly you will need to have CURL installed.
 */

/**
 * Helper class to easily get an access token via PHP.
 */
class AuthorizationTokenManager {
    /**
     * The url to the CoScale API.
     *
     * @type {string}
     */
    private static $apiURL = 'https://app.coscale.com/api/v1/';

    /**
     * Array containing all the info of the last Curl request.
     *
     * Useful for debugging.
     * Please note that this will only store the info of the last request that was done.
     *
     * @type {Array}
     */
    public static $lastCurlRequestInfo;

    /**
     * The status code for the last Curl request.
     *
     * Useful for debugging.
     * Please note that this will only store the info of the last request that was done.
     *
     * @type {number}
     */
    public static $lastCurlRequestStatusCode;

    /**
     * Get an authorization token using your application id and an access token.
     *
     * How to get an access token?
     * ---------------------------
     * You can create access tokens on app.coscale.com. Once logged in use the menu and go to Users > Access tokens.
     *
     * In Case of problems:
     * --------------------
     * If this function returns NULL then it means something went wrong with the request.
     * In this scenario you can get the static property $lastCurlRequestInfo and see what the problem is.
     *
     * Generally if this function fails to return an authorization token it will mean 1 of 3 things:
     * - The appId you provided is wrong.
     * - The accessToken you provided is wrong.
     * - Network problems with CoScale or some service in between.
     *
     * @param  {string} $appId       The appid.
     * @param  {string} $accessToken Access token.
     * @return {string}
     */
    public static function getAuthorizationToken($appId, $accessToken) {
        // Create curl resource.
        $ch = curl_init();

        // Set url.
        curl_setopt($ch, CURLOPT_URL, static::$apiURL . "app/" . $appId . "/login/");

        // Return the transfer as a string.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Turn on POST.
        curl_setopt($ch, CURLOPT_POST, true);

        // Set the post fields.
        $postFields = array('accessToken' => $accessToken);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));

        // Set HTTP headers.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded', // Otherwise the post parameters will not be accepted.
            'Accept: application/json' // We will get json returned.
        ));

        // $output contains the output string.
        $output = curl_exec($ch);

        // Set the curl info into the static properties.
        static::$lastCurlRequestInfo = curl_getinfo($ch);
        static::$lastCurlRequestStatusCode = static::$lastCurlRequestInfo['http_code'];

        // Close curl resource to free up system resources.
        curl_close($ch);

        // Decode output.
        $jsonDecoded = json_decode($output);

        // If the token is found we will return it.
        // Otherwise the function will finish returning NULL.
        if (isset($jsonDecoded->token)) {
            return $jsonDecoded->token;
        }

        return null;
    }
}
?>
