<?php

namespace Symfony\Config\LeagueOauth2Server;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ClientConfig 
{
    private $classname;
    private $_usedProperties = [];

    /**
     * Set a custom client class. Must be a League\Bundle\OAuth2ServerBundle\Model\AbstractClient
     * @default 'League\\Bundle\\OAuth2ServerBundle\\Model\\Client'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function classname($value): static
    {
        $this->_usedProperties['classname'] = true;
        $this->classname = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('classname', $value)) {
            $this->_usedProperties['classname'] = true;
            $this->classname = $value['classname'];
            unset($value['classname']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['classname'])) {
            $output['classname'] = $this->classname;
        }

        return $output;
    }

}
