<?php

namespace Symfony\Config\LeagueOauth2Server;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'DoctrineConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class PersistenceConfig 
{
    private $doctrine;
    private $inMemory;
    private $_usedProperties = [];

    public function doctrine(array $value = []): \Symfony\Config\LeagueOauth2Server\Persistence\DoctrineConfig
    {
        if (null === $this->doctrine) {
            $this->_usedProperties['doctrine'] = true;
            $this->doctrine = new \Symfony\Config\LeagueOauth2Server\Persistence\DoctrineConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "doctrine()" has already been initialized. You cannot pass values the second time you call doctrine().');
        }

        return $this->doctrine;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function inMemory($value): static
    {
        $this->_usedProperties['inMemory'] = true;
        $this->inMemory = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('doctrine', $value)) {
            $this->_usedProperties['doctrine'] = true;
            $this->doctrine = new \Symfony\Config\LeagueOauth2Server\Persistence\DoctrineConfig($value['doctrine']);
            unset($value['doctrine']);
        }

        if (array_key_exists('in_memory', $value)) {
            $this->_usedProperties['inMemory'] = true;
            $this->inMemory = $value['in_memory'];
            unset($value['in_memory']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['doctrine'])) {
            $output['doctrine'] = $this->doctrine->toArray();
        }
        if (isset($this->_usedProperties['inMemory'])) {
            $output['in_memory'] = $this->inMemory;
        }

        return $output;
    }

}
