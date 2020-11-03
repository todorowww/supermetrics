<?php

namespace Supermetrics;

use todorowww\APIException;
use todorowww\XCurl;

/**
 * Class API
 * @package Supermetrics
 *
 * Wrapper for accessing supermetrics API
 */
class API extends XCurl
{
    private $clientId;
    private $email;
    private $name;

    /**
     * @var XCurl
     */
    private $xcurl;

    /**
     * @var SlToken
     */
    private $token;

    const URL_REGISTER = "https://api.supermetrics.com/assignment/register";
    const URL_FETCH_POSTS = "https://api.supermetrics.com/assignment/posts";

    /**
     * API constructor.
     *
     * @param string $clientId Client ID used for registration
     * @param string $email Email used for registration
     * @param string $name Name used for registration
     */
    public function __construct(string $clientId, string $email, string $name)
    {
        $this->clientId = $clientId;
        $this->email = $email;
        $this->name = $name;
        $this->xcurl = new XCurl();
    }

    /**
     * Perform register API call
     *
     * @return SlToken
     * @throws APIException
     */
    public function register(): SlToken
    {
        $params = [
            'client_id' => $this->clientId,
            'email'     => $this->email,
            'name'      => $this->name,
        ];
        $response = $this->xcurl->post(self::URL_REGISTER, $params);

        if ($response) {
            $this->token = new SlToken(
                $response->data->sl_token,
                $response->data->client_id,
                $response->data->email
            );
            return $this->token;
        }
        throw new APIException("Error executing cURL request in register method.", 1025);
    }

    /**
     * Fetches all posts, paginated.
     *
     * @param int $page Optional. Page number to fetch If omitted, fetches the first page of results.
     * @return \stdClass
     * @throws APIException
     */
    public function fetchPosts(int $page = 1)
    {
        if ($this->token->isExpired()) {
            $this->register();
        }

        $params = [
            'sl_token' => $this->token->getToken(),
            'page'     => $page,
        ];
        $response = $this->xcurl->get(self::URL_FETCH_POSTS, $params);

        if ($response) {
            return $response;
        }
        throw new APIException("Error executing cURL request in fetch posts method.", 1026);
    }

    /**
     * Sets token data, to be used for API calls
     *
     * @param SlToken $token Token to use for API calls
     */
    public function setToken(SlToken $token) {
        $this->token = $token;
    }
}
