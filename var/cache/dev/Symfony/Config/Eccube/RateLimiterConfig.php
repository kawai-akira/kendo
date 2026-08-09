<?php

namespace Symfony\Config\Eccube;

require_once __DIR__.\DIRECTORY_SEPARATOR.'RateLimiter'.\DIRECTORY_SEPARATOR.'LimitersConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class RateLimiterConfig 
{
    private $limiters;
    private $_usedProperties = [];

    public function limiters(string $name, array $value = []): \Symfony\Config\Eccube\RateLimiter\LimitersConfig
    {
        if (!isset($this->limiters[$name])) {
            $this->_usedProperties['limiters'] = true;
            $this->limiters[$name] = new \Symfony\Config\Eccube\RateLimiter\LimitersConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "limiters()" has already been initialized. You cannot pass values the second time you call limiters().');
        }

        return $this->limiters[$name];
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('limiters', $value)) {
            $this->_usedProperties['limiters'] = true;
            $this->limiters = array_map(fn ($v) => new \Symfony\Config\Eccube\RateLimiter\LimitersConfig($v), $value['limiters']);
            unset($value['limiters']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['limiters'])) {
            $output['limiters'] = array_map(fn ($v) => $v->toArray(), $this->limiters);
        }

        return $output;
    }

}
