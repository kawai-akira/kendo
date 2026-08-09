<?php

namespace Plugin\DataMigrationBackup30;

class DataMigrationBackupNav
{
    public static function getNav()
    {
        return [
            'data_migration_backup' => [
                'name' => 'data_migration_backup.admin.nav.title',
                'icon' => 'cb-folder',
                'children' => [
                    'data_migration_backup_index' => [
                        'name' => 'data_migration_backup.admin.nav.index',
                        'url' => 'data_migration_backup_admin_index',
                    ],
                    'data_migration_backup_files' => [
                        'name' => 'data_migration_backup.admin.nav.files',
                        'url' => 'data_migration_backup_admin_files',
                    ],
                    'data_migration_backup_scanner' => [
                        'name' => 'data_migration_backup.admin.nav.scanner',
                        'url' => 'data_migration_backup_admin_scanner',
                    ],
                    'data_migration_backup_guide' => [
                        'name' => 'data_migration_backup.admin.nav.guide',
                        'url' => 'data_migration_backup_admin_guide',
                    ],
                ],
            ],
        ];
    }
}
