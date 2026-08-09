<?php

namespace Plugin\DataMigrationBackup30\ServiceProvider;

use Eccube\Common\Constant;
use Plugin\DataMigrationBackup30\Service\BackupBuilderService;
use Plugin\DataMigrationBackup30\Service\BackupPrecheckService;
use Silex\Application as BaseApplication;
#use Silex\ServiceProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

use Symfony\Component\Translation\Loader\YamlFileLoader;

class DataMigrationBackup30ServiceProvider implements ServiceProviderInterface
{

    public function get(string $id): mixed
    {
        // 必要ならサービスを返す。不要なら例外でもOK
        throw new \RuntimeException("Service not provided: " . $id);
    }

    public function has(string $id): bool
    {
        // 提供するサービスがないなら false
        return false;
    }

    public function getProvidedServices(): array
    {
        // 提供するサービスがないなら空配列
        return [];
    }

    public function register(BaseApplication $app)
    {
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $file = __DIR__.'/../Resource/locale/messages.'.$app['locale'].'.yaml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));

        $app['eccube.plugin.data_migration_backup.service.precheck'] = $app->share(function () use ($app) {
            return new BackupPrecheckService($app['orm.em'], $app['config']['root_dir']);
        });

        $app['eccube.plugin.data_migration_backup.service.builder'] = $app->share(function () use ($app) {
            return new BackupBuilderService(
                $app['orm.em'],
                $app['config']['root_dir'],
                $app['eccube.plugin.data_migration_backup.service.precheck']
            );
        });

        $admin = $app['controllers_factory'];
        if ((int) $app['config']['force_ssl'] === Constant::ENABLED) {
            $admin->requireHttps();
        }

        $admin->match('/data-migration-backup', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::index')
            ->method('GET|POST')
            ->bind('data_migration_backup_admin_index');
        $admin->match('/data-migration-backup/files', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::files')
            ->method('GET')
            ->bind('data_migration_backup_admin_files');
        $admin->match('/data-migration-backup/guide', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::guide')
            ->method('GET')
            ->bind('data_migration_backup_admin_guide');
        $admin->match('/data-migration-backup/scanner', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scanner')
            ->method('GET|POST')
            ->bind('data_migration_backup_admin_scanner');
        $admin->match('/data-migration-backup/scanner/chunk/init', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scannerChunkInit')
            ->method('POST')
            ->bind('data_migration_backup_admin_scanner_chunk_init');
        $admin->match('/data-migration-backup/scanner/chunk/{uploadId}/append', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scannerChunkAppend')
            ->method('POST')
            ->bind('data_migration_backup_admin_scanner_chunk_append');
        $admin->match('/data-migration-backup/scanner/chunk/{uploadId}/complete', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scannerChunkComplete')
            ->method('POST')
            ->bind('data_migration_backup_admin_scanner_chunk_complete');
        $admin->match('/data-migration-backup/scanner/chunk/{uploadId}/cancel', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scannerChunkCancel')
            ->method('POST')
            ->bind('data_migration_backup_admin_scanner_chunk_cancel');
        $admin->match('/data-migration-backup/scanner/scan-connectors', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scannerScanConnectors')
            ->method('POST')
            ->bind('data_migration_backup_admin_scanner_scan_connectors');
        $admin->match('/data-migration-backup/scanner/scan-theme', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::scannerScanTheme')
            ->method('POST')
            ->bind('data_migration_backup_admin_scanner_scan_theme');
        $admin->match('/data-migration-backup/run', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::run')
            ->method('POST')
            ->bind('data_migration_backup_admin_run');
        $admin->match('/data-migration-backup/precheck', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::precheck')
            ->method('POST')
            ->bind('data_migration_backup_admin_precheck');
        $admin->match('/data-migration-backup/download/{name}', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::downloadByName')
            ->method('GET')
            ->assert('name', '.+')
            ->bind('data_migration_backup_admin_download');
        $admin->match('/data-migration-backup/download', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::downloadQuery')
            ->method('GET')
            ->bind('data_migration_backup_admin_download_query');
        $admin->match('/data-migration-backup/delete/{name}', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::delete')
            ->method('POST')
            ->assert('name', '.+')
            ->bind('data_migration_backup_admin_delete');
        $admin->match('/data-migration-backup/restore/{name}', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::restore')
            ->method('POST')
            ->assert('name', '.+')
            ->bind('data_migration_backup_admin_restore');
        $admin->match('/data-migration-backup/delete-all', '\\Plugin\\DataMigrationBackup30\\Controller\\Admin\\BackupController::deleteAll')
            ->method('POST')
            ->bind('data_migration_backup_admin_delete_all');

        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);

        $app['config'] = $app->share($app->extend('config', function ($config) use ($app) {
            if (!isset($config['nav']) || !is_array($config['nav'])) {
                return $config;
            }

            $children = array(
                array(
                    'id' => 'data_migration_backup_index',
                    'name' => $app['translator']->trans('data_migration_backup.admin.nav.index'),
                    'url' => 'data_migration_backup_admin_index',
                ),
                array(
                    'id' => 'data_migration_backup_files',
                    'name' => $app['translator']->trans('data_migration_backup.admin.nav.files'),
                    'url' => 'data_migration_backup_admin_files',
                ),
                array(
                    'id' => 'data_migration_backup_scanner',
                    'name' => $app['translator']->trans('data_migration_backup.admin.nav.scanner'),
                    'url' => 'data_migration_backup_admin_scanner',
                ),
                array(
                    'id' => 'data_migration_backup_guide',
                    'name' => $app['translator']->trans('data_migration_backup.admin.nav.guide'),
                    'url' => 'data_migration_backup_admin_guide',
                ),
            );

            $filteredNav = array();
            foreach ($config['nav'] as $level1) {
                if (!is_array($level1)) {
                    $filteredNav[] = $level1;

                    continue;
                }

                if (isset($level1['id']) && $level1['id'] === 'data_migration_backup') {
                    continue;
                }

                $filteredNav[] = $level1;
            }

            $filteredNav[] = array(
                'id' => 'data_migration_backup',
                'name' => $app['translator']->trans('data_migration_backup.admin.nav.title'),
                'has_child' => true,
                'icon' => 'cb-folder',
                'child' => $children,
            );

            $config['nav'] = $filteredNav;

            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }


}
