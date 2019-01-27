<?php

namespace Yper\OAuth2\Client\Provider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class YperResourceOwner implements ResourceOwnerInterface {

    /**
     * Raw response
     *
     * @var
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Get resource owner id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->response['result']['id'] ?: null;
    }

    /**
     * Return all of the details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response['result'];
    }

    /**
     * Get emails
     *
     * @return string|null
     */
    public function getEmails()
    {
        return $this->response['result']['emails'] ?: null;
    }
}