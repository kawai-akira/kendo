<?php

namespace Symfony\Config\LeagueOauth2Server;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AuthorizationServerConfig 
{
    private $privateKey;
    private $privateKeyPassphrase;
    private $encryptionKey;
    private $encryptionKeyType;
    private $accessTokenTtl;
    private $refreshTokenTtl;
    private $authCodeTtl;
    private $enableClientCredentialsGrant;
    private $enablePasswordGrant;
    private $enableRefreshTokenGrant;
    private $enableAuthCodeGrant;
    private $requireCodeChallengeForPublicClients;
    private $enableImplicitGrant;
    private $persistAccessToken;
    private $_usedProperties = [];

    /**
     * Full path to the private key file.
    How to generate a private key: https://oauth2.thephpleague.com/installation/#generating-public-and-private-keys
     * @example /var/oauth/private.key
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function privateKey($value): static
    {
        $this->_usedProperties['privateKey'] = true;
        $this->privateKey = $value;

        return $this;
    }

    /**
     * Passphrase of the private key, if any
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function privateKeyPassphrase($value): static
    {
        $this->_usedProperties['privateKeyPassphrase'] = true;
        $this->privateKeyPassphrase = $value;

        return $this;
    }

    /**
     * The plain string or the ascii safe string used to create a Defuse\Crypto\Key to be used as an encryption key.
    How to generate an encryption key: https://oauth2.thephpleague.com/installation/#string-password
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function encryptionKey($value): static
    {
        $this->_usedProperties['encryptionKey'] = true;
        $this->encryptionKey = $value;

        return $this;
    }

    /**
     * The type of value of 'encryption_key'
    Should be either 'plain' or 'defuse'
     * @default 'plain'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function encryptionKeyType($value): static
    {
        $this->_usedProperties['encryptionKeyType'] = true;
        $this->encryptionKeyType = $value;

        return $this;
    }

    /**
     * How long the issued access token should be valid for.
    The value should be a valid interval: http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
     * @default 'PT1H'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function accessTokenTtl($value): static
    {
        $this->_usedProperties['accessTokenTtl'] = true;
        $this->accessTokenTtl = $value;

        return $this;
    }

    /**
     * How long the issued refresh token should be valid for.
    The value should be a valid interval: http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
     * @default 'P1M'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function refreshTokenTtl($value): static
    {
        $this->_usedProperties['refreshTokenTtl'] = true;
        $this->refreshTokenTtl = $value;

        return $this;
    }

    /**
     * How long the issued auth code should be valid for.
    The value should be a valid interval: http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
     * @default 'PT10M'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function authCodeTtl($value): static
    {
        $this->_usedProperties['authCodeTtl'] = true;
        $this->authCodeTtl = $value;

        return $this;
    }

    /**
     * Whether to enable the client credentials grant
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableClientCredentialsGrant($value): static
    {
        $this->_usedProperties['enableClientCredentialsGrant'] = true;
        $this->enableClientCredentialsGrant = $value;

        return $this;
    }

    /**
     * Whether to enable the password grant
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enablePasswordGrant($value): static
    {
        $this->_usedProperties['enablePasswordGrant'] = true;
        $this->enablePasswordGrant = $value;

        return $this;
    }

    /**
     * Whether to enable the refresh token grant
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableRefreshTokenGrant($value): static
    {
        $this->_usedProperties['enableRefreshTokenGrant'] = true;
        $this->enableRefreshTokenGrant = $value;

        return $this;
    }

    /**
     * Whether to enable the authorization code grant
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableAuthCodeGrant($value): static
    {
        $this->_usedProperties['enableAuthCodeGrant'] = true;
        $this->enableAuthCodeGrant = $value;

        return $this;
    }

    /**
     * Whether to require code challenge for public clients for the auth code grant
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function requireCodeChallengeForPublicClients($value): static
    {
        $this->_usedProperties['requireCodeChallengeForPublicClients'] = true;
        $this->requireCodeChallengeForPublicClients = $value;

        return $this;
    }

    /**
     * Whether to enable the implicit grant
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableImplicitGrant($value): static
    {
        $this->_usedProperties['enableImplicitGrant'] = true;
        $this->enableImplicitGrant = $value;

        return $this;
    }

    /**
     * Whether to enable access token saving to persistence layer
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function persistAccessToken($value): static
    {
        $this->_usedProperties['persistAccessToken'] = true;
        $this->persistAccessToken = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('private_key', $value)) {
            $this->_usedProperties['privateKey'] = true;
            $this->privateKey = $value['private_key'];
            unset($value['private_key']);
        }

        if (array_key_exists('private_key_passphrase', $value)) {
            $this->_usedProperties['privateKeyPassphrase'] = true;
            $this->privateKeyPassphrase = $value['private_key_passphrase'];
            unset($value['private_key_passphrase']);
        }

        if (array_key_exists('encryption_key', $value)) {
            $this->_usedProperties['encryptionKey'] = true;
            $this->encryptionKey = $value['encryption_key'];
            unset($value['encryption_key']);
        }

        if (array_key_exists('encryption_key_type', $value)) {
            $this->_usedProperties['encryptionKeyType'] = true;
            $this->encryptionKeyType = $value['encryption_key_type'];
            unset($value['encryption_key_type']);
        }

        if (array_key_exists('access_token_ttl', $value)) {
            $this->_usedProperties['accessTokenTtl'] = true;
            $this->accessTokenTtl = $value['access_token_ttl'];
            unset($value['access_token_ttl']);
        }

        if (array_key_exists('refresh_token_ttl', $value)) {
            $this->_usedProperties['refreshTokenTtl'] = true;
            $this->refreshTokenTtl = $value['refresh_token_ttl'];
            unset($value['refresh_token_ttl']);
        }

        if (array_key_exists('auth_code_ttl', $value)) {
            $this->_usedProperties['authCodeTtl'] = true;
            $this->authCodeTtl = $value['auth_code_ttl'];
            unset($value['auth_code_ttl']);
        }

        if (array_key_exists('enable_client_credentials_grant', $value)) {
            $this->_usedProperties['enableClientCredentialsGrant'] = true;
            $this->enableClientCredentialsGrant = $value['enable_client_credentials_grant'];
            unset($value['enable_client_credentials_grant']);
        }

        if (array_key_exists('enable_password_grant', $value)) {
            $this->_usedProperties['enablePasswordGrant'] = true;
            $this->enablePasswordGrant = $value['enable_password_grant'];
            unset($value['enable_password_grant']);
        }

        if (array_key_exists('enable_refresh_token_grant', $value)) {
            $this->_usedProperties['enableRefreshTokenGrant'] = true;
            $this->enableRefreshTokenGrant = $value['enable_refresh_token_grant'];
            unset($value['enable_refresh_token_grant']);
        }

        if (array_key_exists('enable_auth_code_grant', $value)) {
            $this->_usedProperties['enableAuthCodeGrant'] = true;
            $this->enableAuthCodeGrant = $value['enable_auth_code_grant'];
            unset($value['enable_auth_code_grant']);
        }

        if (array_key_exists('require_code_challenge_for_public_clients', $value)) {
            $this->_usedProperties['requireCodeChallengeForPublicClients'] = true;
            $this->requireCodeChallengeForPublicClients = $value['require_code_challenge_for_public_clients'];
            unset($value['require_code_challenge_for_public_clients']);
        }

        if (array_key_exists('enable_implicit_grant', $value)) {
            $this->_usedProperties['enableImplicitGrant'] = true;
            $this->enableImplicitGrant = $value['enable_implicit_grant'];
            unset($value['enable_implicit_grant']);
        }

        if (array_key_exists('persist_access_token', $value)) {
            $this->_usedProperties['persistAccessToken'] = true;
            $this->persistAccessToken = $value['persist_access_token'];
            unset($value['persist_access_token']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['privateKey'])) {
            $output['private_key'] = $this->privateKey;
        }
        if (isset($this->_usedProperties['privateKeyPassphrase'])) {
            $output['private_key_passphrase'] = $this->privateKeyPassphrase;
        }
        if (isset($this->_usedProperties['encryptionKey'])) {
            $output['encryption_key'] = $this->encryptionKey;
        }
        if (isset($this->_usedProperties['encryptionKeyType'])) {
            $output['encryption_key_type'] = $this->encryptionKeyType;
        }
        if (isset($this->_usedProperties['accessTokenTtl'])) {
            $output['access_token_ttl'] = $this->accessTokenTtl;
        }
        if (isset($this->_usedProperties['refreshTokenTtl'])) {
            $output['refresh_token_ttl'] = $this->refreshTokenTtl;
        }
        if (isset($this->_usedProperties['authCodeTtl'])) {
            $output['auth_code_ttl'] = $this->authCodeTtl;
        }
        if (isset($this->_usedProperties['enableClientCredentialsGrant'])) {
            $output['enable_client_credentials_grant'] = $this->enableClientCredentialsGrant;
        }
        if (isset($this->_usedProperties['enablePasswordGrant'])) {
            $output['enable_password_grant'] = $this->enablePasswordGrant;
        }
        if (isset($this->_usedProperties['enableRefreshTokenGrant'])) {
            $output['enable_refresh_token_grant'] = $this->enableRefreshTokenGrant;
        }
        if (isset($this->_usedProperties['enableAuthCodeGrant'])) {
            $output['enable_auth_code_grant'] = $this->enableAuthCodeGrant;
        }
        if (isset($this->_usedProperties['requireCodeChallengeForPublicClients'])) {
            $output['require_code_challenge_for_public_clients'] = $this->requireCodeChallengeForPublicClients;
        }
        if (isset($this->_usedProperties['enableImplicitGrant'])) {
            $output['enable_implicit_grant'] = $this->enableImplicitGrant;
        }
        if (isset($this->_usedProperties['persistAccessToken'])) {
            $output['persist_access_token'] = $this->persistAccessToken;
        }

        return $output;
    }

}
