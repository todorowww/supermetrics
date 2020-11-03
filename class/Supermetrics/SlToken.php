<?php

namespace Supermetrics;

/**
 * Class SlToken
 *
 * Holds short-lived token returned by the register function of the API
 *
 * @package Supermetrics
 */
class SlToken implements \JsonSerializable
{
    /**
     * Timestamp of when was this token issued
     *
     * @var int
     */
    private $issued;

    /**
     * Timestamp of when this token expires
     *
     * @var int
     */
    private $expires;

    /**
     * Token value
     *
     * @var string
     */
    private $token;

    /**
     * Client ID returned by the register function
     *
     * @var string
     */
    private $clientId;

    /**
     * Email returned by the register function
     * @var string
     */
    private $email;


    /**
     * Builds SlToken
     *
     * @param string $token
     * @param string $clientId
     * @param string $email
     */
    public function __construct(string $token, string $clientId, string $email)
    {
        $this->update($token, $clientId, $email);
    }

    /**
     * Update short-lived token
     *
     * @param string   $token    Token string
     * @param string   $clientId Client ID returned by the register function
     * @param string   $email    Email returned by the register function
     * @param int|null $issued   Optional. When was the token issued. If left out, current time is used
     * @param int|null $expires  Optional. Timestamp of token expiry. If left out, calculated from $issued value
     */
    public function update(string $token, string $clientId, string $email, int $issued = null, int $expires = null)
    {
        $this->token = $token;
        $this->clientId = $clientId;
        $this->email = $email;
        $this->issued = $issued ?? time();
        $this->expires = $expires ?? $this->issued + 3600;
    }

    public function build(string $json) {
        $obj = json_decode($json, false);
        foreach($obj as $property=>$value) {
            $this->{$property} = $value;
        }
    }

    /**
     * Return token string
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Checks whether the token has expired, or not
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires <= time();
    }

    /**
     * jsonSerializable implementation
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
