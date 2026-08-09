<?php

namespace Plugin\DataMigrationBackup30\Twig;

use Eccube\Common\Constant;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BootstrapHelperExtension extends AbstractExtension
{
    /**
     * @return array<int, TwigFunction>
     */
    public function getFunctions() {
        return [
            new TwigFunction('dmb_bootstrap_data_attr', [$this, 'getBootstrapDataAttrName']),
            new TwigFunction('dmb_bootstrap_data_attr_pair', [$this, 'getBootstrapDataAttrPair'], ['is_safe' => ['html']]),
        ];
    }

    public function getBootstrapDataAttrName($name) {
        $prefix = version_compare(Constant::VERSION, '4.2.0', '>=') ? 'data-bs-' : 'data-';

        return $prefix.$name;
    }

    public function getBootstrapDataAttrPair($name, $value) {
        $attr = $this->getBootstrapDataAttrName($name);

        return sprintf('%s="%s"', $attr, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }
}
