<?php
/**
 * Example using the CoScale AuthorizationTokenManager.
 */

/**
 * A simple hierarchy of classes based on MVC principles like in Laravel or any other framework.
 * You can also use this example within any function.
 */
class PublicPagesController extends BaseController {
    /**
     * Home page.
     */
    public function home() {
        // First we need to fetch an authorization token before we can start using the API.
        // You can define specific access token on your access token page on app.coscale.com.
        $coscaleAuthorizationToken = Coscale\AuthorizationTokenManager::getAuthorizationToken('YOUR-APP-ID', 'ACCESS-TOKEN');
        if ($coscaleAuthorizationToken != null) {
            // Start doing API calls using the authorization token and CURL.
            // See the API reference on http://docs.coscale.com/api/

            /**
             * Example getting all your alerts.
             */
            $appId = 'YOUR-APP-ID';

            // Create curl resource.
            $ch = curl_init();

            // Set url.
            curl_setopt($ch, CURLOPT_URL, "https://app.coscale.com/api/v1/app/" . $appId . "/alerts/");

            // Return the transfer as a string.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Set HTTP headers.
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'HTTPAuthorization: ' . $coscaleAuthorizationToken,
                'Accept: application/json' // We will get json returned.
            ));

            // $output contains the output string.
            $output = curl_exec($ch);

            // Close curl resource to free up system resources.
            curl_close($ch);

            // Decode output.
            // Your alerts are in this variable now.
            $alertsJson = json_decode($output);

            // Do something with alert data.
        } else {
            // If the authorization token is null then something went wrong.
            // Debug.
            // var_dump(Coscale\AuthorizationTokenManager::$lastCurlRequestInfo);
        }

        // Render page.
        return this->renderPage('home');
    }
?>
