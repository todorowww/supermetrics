<?php

namespace todorowww;

/**
 * Class XCurl
 * @package todorowww
 *
 * Class that simplifies cURL calls.
 */
class XCurl
{
    const XCURL_POST = "POST";
    const XCURL_GET = "GET";

    /**
     * Executes POST request. Alias for call function
     *
     * @param string $url URL to send the request to
     * @param array  $params Parameters to be sent
     * @return \stdClass Response from the executed request.
     * @throws APIException
     */
    public function post(string $url, array $params): \stdClass
    {
        return $this->call($url, self::XCURL_POST, $params);
    }

    /**
     * Executes GET request. Alias for call function
     *
     * @param string $url URL to send the request to
     * @param array  $params Parameters to be sent
     * @return \stdClass Response from the executed request.
     * @throws APIException
     */
    public function get(string $url, array $params): \stdClass
    {
        return $this->call($url, self::XCURL_GET, $params);
    }

    /**
     * @param string $url URL to send the request to
     * @param string $method Method to be executed
     * @param array  $params Parameters to be sent
     * @return \stdClass Response from the executed request.
     * @throws APIException
     */
    private function call(string $url, string $method, array $params): \stdClass
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "utf-8",
            CURLOPT_USERAGENT      => "todorowww/XCurl",
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
        ];

        // Form a proper POST request with parameters
        if ($method === self::XCURL_POST) {
            $options[CURLOPT_POST] = 1;
            if (!empty($params)) {
                $options[CURLOPT_POSTFIELDS] = json_encode($params);
            }
        }

        // Form a proper GET request with parameters
        if (($method === self::XCURL_GET) && !empty($params)) {
            $url = sprintf("%s?%s", $url, http_build_query($params));
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $return = curl_exec($ch);
        $returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        /**
         * Check if we got 2xx, or throw and error
         */
        if ((int)($returnCode / 100) !== 2) {
            throw new APIException("Server error $returnCode executing cURL request.", $returnCode);
        }

        curl_close($ch);
        return json_decode($return, false);
    }

}
