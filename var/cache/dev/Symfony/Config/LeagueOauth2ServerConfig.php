<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'LeagueOauth2Server'.\DIRECTORY_SEPARATOR.'AuthorizationServerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'LeagueOauth2Server'.\DIRECTORY_SEPARATOR.'ResourceServerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'LeagueOauth2Server'.\DIRECTORY_SEPARATOR.'ScopesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'LeagueOauth2Server'.\DIRECTORY_SEPARATOR.'PersistenceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'LeagueOauth2Server'.\DIRECTORY_SEPARATOR.'ClientConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class LeagueOauth2ServerConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $authorizationServer;
    private $resourceServer;
    private $scopes;
    private $persistence;
    private $client;
    private $rolePrefix;
    private $_usedProperties = [];

    public function authorizationServer(array $value = []): \Symfony\Config\LeagueOauth2Server\AuthorizationServerConfig
    {
        if (null === $this->authorizationServer) {
            $this->_usedProperties['authorizationServer'] = true;
            $this->authorizationServer = new \Symfony\Config\LeagueOauth2Server\AuthorizationServerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "authorizationServer()" has already been initialized. You cannot pass values the second time you call authorizationServer().');
        }

        return $this->authorizationServer;
    }

    public function resourceServer(array $value = []): \Symfony\Config\LeagueOauth2Server\ResourceServerConfig
    {
        if (null === $this->resourceServer) {
            $this->_usedProperties['resourceServer'] = true;
            $this->resourceServer = new \Symfony\Config\LeagueOauth2Server\ResourceServerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "resourceServer()" has already been initialized. You cannot pass values the second time you call resourceServer().');
        }

        return $this->resourceServer;
    }

    public function scopes(array $value = []): \Symfony\Config\LeagueOauth2Server\ScopesConfig
    {
        if (null === $this->scopes) {
            $this->_usedProperties['scopes'] = true;
            $this->scopes = new \Symfony\Config\LeagueOauth2Server\ScopesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "scopes()" has already been initialized. You cannot pass values the second time you call scopes().');
        }

        return $this->scopes;
    }

    /**
     * Configures different persistence methods that can be used by the bundle for saving client and token data.
    Only one persistence method can be configured at a time.
    */
    public function persistence(array $value = []): \Symfony\Config\LeagueOauth2Server\PersistenceConfig
    {
        if (null === $this->persistence) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\LeagueOauth2Server\PersistenceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "persistence()" has already been initialized. You cannot pass values the second time you call persistence().');
        }

        return $this->persistence;
    }

    /**
     * @default {"classname":"League\\Bundle\\OAuth2ServerBundle\\Model\\Client"}
    */
    public function client(array $value = []): \Symfony\Config\LeagueOauth2Server\ClientConfig
    {
        if (null === $this->client) {
            $this->_usedProperties['client'] = true;
            $this->client = new \Symfony\Config\LeagueOauth2Server\ClientConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "client()" has already been initialized. You cannot pass values the second time you call client().');
        }

        return $this->client;
    }

    /**
     * Set a custom prefix that replaces the default 'ROLE_OAUTH2_' role prefix
     * @default 'ROLE_OAUTH2_'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function rolePrefix($value): static
    {
        $this->_usedProperties['rolePrefix'] = true;
        $this->rolePrefix = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'league_oauth2_server';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('authorization_server', $value)) {
            $this->_usedProperties['authorizationServer'] = true;
            $this->authorizationServer = new \Symfony\Config\LeagueOauth2Server\AuthorizationServerConfig($value['authorization_server']);
            unset($value['authorization_server']);
        }

        if (array_key_exists('resource_server', $value)) {
            $this->_usedProperties['resourceServer'] = true;
            $this->resourceServer = new \Symfony\Config\LeagueOauth2Server\ResourceServerConfig($value['resource_server']);
            unset($value['resource_server']);
        }

        if (array_key_exists('scopes', $value)) {
            $this->_usedProperties['scopes'] = true;
            $this->scopes = new \Symfony\Config\LeagueOauth2Server\ScopesConfig($value['scopes']);
            unset($value['scopes']);
        }

        if (array_key_exists('persistence', $value)) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\LeagueOauth2Server\PersistenceConfig($value['persistence']);
            unset($value['persistence']);
        }

        if (array_key_exists('client', $value)) {
            $this->_usedProperties['client'] = true;
            $this->client = new \Symfony\Config\LeagueOauth2Server\ClientConfig($value['client']);
            unset($value['client']);
        }

        if (array_key_exists('role_prefix', $value)) {
            $this->_usedProperties['rolePrefix'] = true;
            $this->rolePrefix = $value['role_prefix'];
            unset($value['role_prefix']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['authorizationServer'])) {
            $output['authorization_server'] = $this->authorizationServer->toArray();
        }
        if (isset($this->_usedProperties['resourceServer'])) {
            $output['resource_server'] = $this->resourceServer->toArray();
        }
        if (isset($this->_usedProperties['scopes'])) {
            $output['scopes'] = $this->scopes->toArray();
        }
        if (isset($this->_usedProperties['persistence'])) {
            $output['persistence'] = $this->persistence->toArray();
        }
        if (isset($this->_usedProperties['client'])) {
            $output['client'] = $this->client->toArray();
        }
        if (isset($this->_usedProperties['rolePrefix'])) {
            $output['role_prefix'] = $this->rolePrefix;
        }

        return $output;
    }

}
