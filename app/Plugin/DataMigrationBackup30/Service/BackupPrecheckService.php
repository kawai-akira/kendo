<?php

namespace Plugin\DataMigrationBackup30\Service;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\Constant;

class BackupPrecheckService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $projectDir;

    public function __construct(EntityManagerInterface $entityManager, $projectDir)
    {
        $this->entityManager = $entityManager;
        $this->projectDir = rtrim($projectDir, '/');
    }

    public function listTables()
    {
        $conn = $this->entityManager->getConnection();
        $schemaManager = method_exists($conn, 'createSchemaManager') ? $conn->createSchemaManager() : $conn->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $rows = array();

        foreach ($tables as $name) {
            $rows[] = (string) $name;
        }

        sort($rows);

        return $rows;
    }

    public function collectChecks($options)
    {
        $selectedTables = $this->resolveSelectedTables($options);
        $estimateBytes = $this->estimateBackupBytes($options, $selectedTables);
        $requiredBytes = (int) max(209715200, $estimateBytes * 1.5);

        $backupDir = $this->getBackupDir();
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0777, true);
        }

        $freeBytes = @disk_free_space($backupDir);
        if (!is_numeric($freeBytes)) {
            $freeBytes = 0;
        }

        $themeCode = $this->optionValue($options, 'theme_code', 'default');
        $themeAppDir = $this->getProjectDir().'/app/template/'.$themeCode;
        $themeCoreDefaultDir = $this->getProjectDir().'/src/Eccube/Resource/template/default';
        $themeHtmlDir = $this->getProjectDir().'/html/template/'.$themeCode;
        $themeExists = is_dir($themeAppDir)
            || is_dir($themeHtmlDir)
            || (strtolower($themeCode) === 'default' && is_dir($themeCoreDefaultDir));

        $rows = array();
        $rows[] = array(
            'label' => 'EC-CUBE バージョン',
            'value' => Constant::VERSION,
            'ok' => true,
            'note' => '確認済み',
        );
        $rows[] = array(
            'label' => '空きディスク容量',
            'value' => $this->formatBytes((int) $freeBytes),
            'ok' => ((int) $freeBytes >= $requiredBytes),
            'note' => '必要目安 '.$this->formatBytes($requiredBytes),
        );

        if (!empty($options['backup_theme'])) {
            $rows[] = array(
                'label' => 'テーマディレクトリ',
                'value' => $themeCode,
                'ok' => $themeExists,
                'note' => $themeExists ? '確認済み' : '対象テーマが見つかりません',
            );
        }

        $hasWarning = false;
        foreach ($rows as $row) {
            if (empty($row['ok'])) {
                $hasWarning = true;
                break;
            }
        }

        $facts = array(
            'eccube_version' => Constant::VERSION,
            'php_version' => PHP_VERSION,
            'database_platform' => $this->getDatabasePlatformName(),
            'memory_limit' => (string) ini_get('memory_limit'),
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'backup_dir' => $backupDir,
            'backup_dir_writable' => is_writable($backupDir),
            'disk_free_bytes' => (int) $freeBytes,
            'estimated_backup_bytes' => (int) $estimateBytes,
            'required_free_bytes' => (int) $requiredBytes,
            'db_scope' => $this->optionValue($options, 'db_scope', 'all'),
            'db_tables_count' => count($selectedTables),
            'db_tables' => $selectedTables,
            'theme_code' => $themeCode,
            'theme_exists' => $themeExists,
        );

        return array(
            'can_run' => true,
            'has_warning' => $hasWarning,
            'estimated_bytes' => (int) $estimateBytes,
            'required_bytes' => (int) $requiredBytes,
            'free_bytes' => (int) $freeBytes,
            'rows' => $rows,
            'facts' => $facts,
        );
    }

    public function resolveSelectedTables($options)
    {
        if (empty($options['backup_db'])) {
            return array();
        }

        $allTables = $this->listTables();
        $scope = $this->optionValue($options, 'db_scope', 'all');
        if ($scope !== 'selected') {
            return $allTables;
        }

        $selected = isset($options['db_tables']) && is_array($options['db_tables']) ? $options['db_tables'] : array();
        $selected = array_values(array_unique(array_map('strval', $selected)));

        return array_values(array_filter($allTables, function ($table) use ($selected) {
            return in_array($table, $selected, true);
        }));
    }

    public function getBackupDir()
    {
        return $this->getProjectDir().'/app/Plugin/DataMigrationBackup30/storage/backups';
    }

    private function estimateBackupBytes($options, $selectedTables)
    {
        $estimate = 0;

        if (!empty($options['backup_db']) && !empty($selectedTables)) {
            $estimate += $this->estimateDatabaseBytes($selectedTables);
        }

        if (!empty($options['backup_plugin'])) {
            $estimate += $this->directorySize($this->getProjectDir().'/app/Plugin');
        }

        if (!empty($options['backup_theme'])) {
            $themeCode = $this->optionValue($options, 'theme_code', 'default');
            if (strtolower($themeCode) === 'default') {
                $estimate += $this->directorySize($this->getProjectDir().'/src/Eccube/Resource/template/default');
            }
            $estimate += $this->directorySize($this->getProjectDir().'/app/template/'.$themeCode);
            $estimate += $this->directorySize($this->getProjectDir().'/html/template/'.$themeCode);
        }

        if (!empty($options['backup_assets'])) {
            $estimate += $this->directorySize($this->getProjectDir().'/html/upload');
            $estimate += $this->directorySize($this->getProjectDir().'/html/user_data');
        }

        if (!empty($options['backup_app_assets'])) {
            $estimate += $this->directorySize($this->getProjectDir().'/app/template/user_data');
        }

        return max(10485760, (int) $estimate);
    }

    private function estimateDatabaseBytes($tables)
    {
        $conn = $this->entityManager->getConnection();
        $platform = $this->getDatabasePlatformName();
        $sum = 0;

        foreach ($tables as $table) {
            try {
                if ($platform === 'mysql') {
                    $bytes = $this->fetchValue(
                        $conn,
                        'SELECT COALESCE(DATA_LENGTH + INDEX_LENGTH, 0) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
                        array($table)
                    );
                    $sum += (int) $bytes;
                    continue;
                }

                if ($platform === 'postgresql') {
                    $bytes = $this->fetchValue($conn, 'SELECT COALESCE(pg_total_relation_size(?), 0)', array($table));
                    $sum += (int) $bytes;
                    continue;
                }

                $count = (int) $this->fetchValue($conn, 'SELECT COUNT(*) FROM '.$this->quoteIdentifier($table));
                $sum += max(1024, $count * 512);
            } catch (\Exception $e) {
                $sum += 1048576;
            }
        }

        return $sum;
    }

    private function fetchValue($conn, $sql, $params = array())
    {
        $stmt = $conn->executeQuery($sql, $params);
        if (method_exists($stmt, 'fetchColumn')) {
            return $stmt->fetchColumn(0);
        }
        if (method_exists($stmt, 'fetch')) {
            $row = $stmt->fetch();
            if (is_array($row)) {
                return reset($row);
            }
        }

        return null;
    }

    private function quoteIdentifier($name)
    {
        return $this->entityManager->getConnection()->getDatabasePlatform()->quoteIdentifier($name);
    }

    private function directorySize($dir)
    {
        if (!is_dir($dir)) {
            return 0;
        }

        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo && $file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    private function formatBytes($bytes)
    {
        if ($bytes <= 0) {
            return '0 MB';
        }

        $mb = $bytes / 1024 / 1024;
        if ($mb > 1000) {
            return sprintf('%d GB', (int) ceil($mb / 1024));
        }

        return sprintf('%d MB', (int) ceil($mb));
    }

    private function getProjectDir()
    {
        return $this->projectDir;
    }

    private function getDatabasePlatformName()
    {
        try {
            return (string) $this->entityManager->getConnection()->getDatabasePlatform()->getName();
        } catch (\Exception $e) {
            return '';
        }
    }

    private function optionValue($options, $key, $default)
    {
        if (is_array($options) && array_key_exists($key, $options) && $options[$key] !== null && $options[$key] !== '') {
            return $options[$key];
        }

        return $default;
    }
}
