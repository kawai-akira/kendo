<?php

namespace Symfony\Config\LeagueOauth2Server;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ResourceServerConfig 
{
    private $publicKey;
    private $_usedProperties = [];

    /**
     * Full path to the public key file
    How to generate a public key: https://oauth2.thephpleague.com/installation/#generating-public-and-private-keys
     * @example /var/oauth/public.key
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function publicKey($value): static
    {
        $this->_usedProperties['publicKey'] = true;
        $this->publicKey = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('public_key', $value)) {
            $this->_usedProperties['publicKey'] = true;
            $this->publicKey = $value['public_key'];
            unset($value['public_key']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['publicKey'])) {
            $output['public_key'] = $this->publicKey;
        }

        return $output;
    }

}
