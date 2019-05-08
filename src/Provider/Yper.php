<?php

namespace Yper\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Yper extends AbstractProvider
{
    use BearerAuthorizationTrait;

    private $baseURI = [
        'production' => 'https://api.yper.io',
        'rc' => 'https://io.rc.yper.org',
        'beta' => 'https://io.beta.yper.org',
        'alpha' => 'https://io.alpha.yper.org',
        'development' => 'http://localhost:5000'
    ];

    private $env = null;
    private $uri = null;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        $this->env = 'production';
        if (isset($options['environment'])) {
            $this->env = $options['environment'];
        }

        if (isset($this->baseURI[$this->env])) {
            $this->uri = $this->baseURI[$this->env];
        }
    }

    public function getBaseAuthorizationUrl()
    {
        if (!$this->env) {
            throw new \Exception('Please provide environment for yper.');
        }
        return $this->uri . '/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->uri . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->uri . '/user/me';
    }

    protected function getScopeSeparator()
    {
        return ',';
    }

    protected function getDefaultHeaders()
    {
        return [
            'X-Request-Timestamp' => time()
        ];
    }

    protected function prepareAccessTokenResponse(array $result)
    {
        $result = parent::prepareAccessTokenResponse($result);
        return $result['result'];
    }

    protected function getDefaultScopes()
    {
        return ['global'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            if (isset($data['error_code']) && isset($data['error_message'])) {
                $error = $data['error_code'];
                $errorDescription = $data['error_message'];

                throw new IdentityProviderException(
                    $statusCode . ' - ' . $errorDescription . ': ' . $error,
                    $response->getStatusCode(),
                    $response
                );
            } else {
                throw new IdentityProviderException(
                    'Unrecognized response',
                    $response->getStatusCode(),
                    $response
                );
            }
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new YperResourceOwner($response);
    }
}