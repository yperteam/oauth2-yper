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
        return $this->response['result']['_id'] ?: null;
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

    /**
     * Get phones
     *
     * @return string|null
     */
    public function getPhones()
    {
        return $this->response['result']['phones'] ?: null;
    }

    /**
     * Return main email for an user (last one verified)
     *
     * @return string|null
     */
    public function getMainEmail() {
        if (isset($this->response['result']['emails'])) {
            $nb_emails = count($this->response['result']['emails']);
            if ($nb_emails == 1) {
                return $this->response['result']['emails'][0]['address'];
            } else if ($nb_emails > 1) {
                $i = $nb_emails - 1;
                while ($i >= 0) {
                    if ($this->response['result']['emails'][$i]['verified'] == true) {
                        return $this->response['result']['emails'][$i]['address'];
                    }
                    $i--;
                }
            }
        }
        return null;
    }
}
