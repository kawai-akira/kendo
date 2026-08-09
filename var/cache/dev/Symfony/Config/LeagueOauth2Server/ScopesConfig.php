<?php

namespace Symfony\Config\LeagueOauth2Server;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ScopesConfig 
{
    private $available;
    private $default;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function available(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['available'] = true;
        $this->available = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function default(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['default'] = true;
        $this->default = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('available', $value)) {
            $this->_usedProperties['available'] = true;
            $this->available = $value['available'];
            unset($value['available']);
        }

        if (array_key_exists('default', $value)) {
            $this->_usedProperties['default'] = true;
            $this->default = $value['default'];
            unset($value['default']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['available'])) {
            $output['available'] = $this->available;
        }
        if (isset($this->_usedProperties['default'])) {
            $output['default'] = $this->default;
        }

        return $output;
    }

}
