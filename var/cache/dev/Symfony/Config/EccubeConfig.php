<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'RateLimiterConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class EccubeConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $rateLimiter;
    private $_usedProperties = [];

    /**
     * @template TValue
     * @param TValue $value
     * @return \Symfony\Config\Eccube\RateLimiterConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\Eccube\RateLimiterConfig : static)
     */
    public function rateLimiter(mixed $value = []): \Symfony\Config\Eccube\RateLimiterConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['rateLimiter'] = true;
            $this->rateLimiter = $value;

            return $this;
        }

        if (!$this->rateLimiter instanceof \Symfony\Config\Eccube\RateLimiterConfig) {
            $this->_usedProperties['rateLimiter'] = true;
            $this->rateLimiter = new \Symfony\Config\Eccube\RateLimiterConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "rateLimiter()" has already been initialized. You cannot pass values the second time you call rateLimiter().');
        }

        return $this->rateLimiter;
    }

    public function getExtensionAlias(): string
    {
        return 'eccube';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('rate_limiter', $value)) {
            $this->_usedProperties['rateLimiter'] = true;
            $this->rateLimiter = \is_array($value['rate_limiter']) ? new \Symfony\Config\Eccube\RateLimiterConfig($value['rate_limiter']) : $value['rate_limiter'];
            unset($value['rate_limiter']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['rateLimiter'])) {
            $output['rate_limiter'] = $this->rateLimiter instanceof \Symfony\Config\Eccube\RateLimiterConfig ? $this->rateLimiter->toArray() : $this->rateLimiter;
        }

        return $output;
    }

}
