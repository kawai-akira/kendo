<?php

namespace Plugin\DataMigrationBackup30;

use Eccube\Application;
use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{
    public function install($config, $app)
    {
        $this->ensureStorageDir();
    }

    public function enable($config, $app)
    {
        $this->ensureStorageDir();
    }

    public function update($config, $app)
    {
        $this->ensureStorageDir();
    }

    public function disable($config, $app)
    {
    }

    public function uninstall($config, $app)
    {
        $this->ensureStorageDir();
    }

    private function ensureStorageDir()
    {
        $dir = __DIR__.'/storage/backups';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
}
