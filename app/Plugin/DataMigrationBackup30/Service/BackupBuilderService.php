<?php

namespace Plugin\DataMigrationBackup30\Service;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\Constant;
use Symfony\Component\Yaml\Yaml;

class BackupBuilderService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $projectDir;

    /** @var BackupPrecheckService */
    private $precheckService;

    public function __construct(EntityManagerInterface $entityManager, $projectDir, BackupPrecheckService $precheckService)
    {
        $this->entityManager = $entityManager;
        $this->projectDir = rtrim($projectDir, '/');
        $this->precheckService = $precheckService;
    }

    public function createBackup($options)
    {
        $this->extendExecutionTime();

        $projectDir = $this->getProjectDir();
        $backupDir = $this->precheckService->getBackupDir();
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0777, true);
        }

        $tmpRoot = sys_get_temp_dir().'/dmbk_'.sha1(microtime(true).mt_rand(1000, 999999));
        $payloadDir = $tmpRoot.'/payload';
        @mkdir($payloadDir, 0777, true);
        $timestamp = date('YmdHis');
        $fixedBackupName = $this->sanitizeFixedBackupName($this->optionValue($options, 'fixed_backup_name', ''));
        $components = array();
        $assetDownloads = array();
        $profile = $this->normalizeProfile($this->optionValue($options, 'backup_profile', 'custom'));
        $themeCode = $this->optionValue($options, 'theme_code', 'default');
        $themeDependencyScan = array();
        $preparedThemeSources = array();

        try {
            if (!empty($options['backup_db'])) {
                $selectedTables = $this->precheckService->resolveSelectedTables($options);
                $this->dumpDatabaseSql($payloadDir.'/database.sql', $selectedTables);
                $this->writeDatabaseSchemaJson($payloadDir.'/databases.json', $selectedTables);
                $components[] = 'database.sql';
                $components[] = 'databases.json';
            }

            if (!empty($options['backup_plugin'])) {
                $this->createPluginZip($projectDir.'/app/Plugin', $payloadDir.'/plugins.zip');
                $components[] = 'plugins.zip';
            }

            if (!empty($options['backup_theme'])) {
                $preparedThemeSources = $this->prepareThemeBackupSources($themeCode, $tmpRoot.'/theme_sources');
                $themeArchiveName = 'theme_'.$themeCode.'.tar.gz';
                $this->createArchiveFromMap(
                    isset($preparedThemeSources['archive_map']) && is_array($preparedThemeSources['archive_map']) ? $preparedThemeSources['archive_map'] : array(),
                    $payloadDir.'/'.$themeArchiveName,
                    $tmpRoot.'/theme_work'
                );
                if (is_file($payloadDir.'/'.$themeArchiveName)) {
                    $components[] = $themeArchiveName;
                }
            }

            if (!empty($options['backup_assets'])) {
                $assetsHtmlName = 'assets_html_'.$timestamp.'.tar.gz';
                $assetsHtmlPath = $backupDir.'/'.$assetsHtmlName;
                $this->createArchiveFromMap(
                    array(
                        'html/upload' => $projectDir.'/html/upload',
                        'html/user_data' => $projectDir.'/html/user_data',
                    ),
                    $assetsHtmlPath,
                    $tmpRoot.'/assets_html_work'
                );
                if (is_file($assetsHtmlPath)) {
                    $this->writeBackupMeta($assetsHtmlPath, array(
                        'backup_profile' => $profile,
                        'generated_at' => date('c'),
                        'components' => array(),
                        'options' => array(),
                        'file_kind' => 'assets_html',
                        'file_label' => '公開ディレクトリのファイル',
                    ));
                    $assetDownloads[] = array(
                        'name' => $assetsHtmlName,
                        'size' => (int) filesize($assetsHtmlPath),
                        'kind' => 'assets_html',
                        'destination' => 'html/upload, html/user_data',
                    );
                }
            }

            if (!empty($options['backup_app_assets'])) {
                $assetsAppName = 'assets_app_'.$timestamp.'.tar.gz';
                $assetsAppPath = $backupDir.'/'.$assetsAppName;
                $this->createArchiveFromMap(
                    array(
                        'app/template/user_data' => $projectDir.'/app/template/user_data',
                    ),
                    $assetsAppPath,
                    $tmpRoot.'/assets_app_work'
                );
                if (is_file($assetsAppPath)) {
                    $this->writeBackupMeta($assetsAppPath, array(
                        'backup_profile' => $profile,
                        'generated_at' => date('c'),
                        'components' => array(),
                        'options' => array(),
                        'file_kind' => 'assets_app',
                        'file_label' => 'テンプレート側のファイル',
                    ));
                    $assetDownloads[] = array(
                        'name' => $assetsAppName,
                        'size' => (int) filesize($assetsAppPath),
                        'kind' => 'assets_app',
                        'destination' => 'app/template/user_data',
                    );
                }
            }

            $sourcePlugins = $this->buildSourcePluginsSnapshot();
            $activePlugins = array_values(array_filter($sourcePlugins, function ($plugin) {
                return !empty($plugin['enabled']);
            }));
            $sourcePluginTables = $this->buildSourcePluginTableMap($activePlugins);
            $sourceAuthProfile = $this->resolveSourceAuthProfile();
            $themeName = $this->resolveThemeName($themeCode);
            if (!empty($options['backup_theme'])) {
                $themeDependencyScan = $this->collectThemeDependencyScan(
                    $themeCode,
                    $sourcePlugins,
                    isset($preparedThemeSources['scan_roots']) && is_array($preparedThemeSources['scan_roots']) ? $preparedThemeSources['scan_roots'] : array()
                );
            }
            $precheck = $this->precheckService->collectChecks($options);
            if (!isset($precheck['facts']) || !is_array($precheck['facts'])) {
                $precheck['facts'] = array();
            }
            if (empty($precheck['facts']['eccube_version'])) {
                $precheck['facts']['eccube_version'] = Constant::VERSION;
            }
            $precheck['facts']['database_tables'] = !empty($selectedTables) ? array_values($selectedTables) : array();
            $precheck['facts']['active_plugins'] = $activePlugins;
            $precheck['facts']['all_plugins'] = $sourcePlugins;
            $precheck['facts']['theme_name'] = $themeName;
            $precheck['facts']['source_plugin_tables'] = $sourcePluginTables;
            if (!empty($options['backup_theme'])) {
                $precheck['facts']['theme_dependency_scan'] = $themeDependencyScan;
            }
            $this->writePrecheckJson($payloadDir.'/precheck.json', $precheck);
            $components[] = 'precheck.json';

            $manifest = array(
                'generated_at' => date('c'),
                'source' => 'DataMigrationBackup30',
                'components' => $components,
                'precheck_has_warning' => !empty($precheck['has_warning']),
                'backup_profile' => $profile,
                'source_plugins' => $sourcePlugins,
                'source_plugin_tables' => $sourcePluginTables,
                'source_auth_profile' => $sourceAuthProfile,
                'theme_dependency_scan' => !empty($options['backup_theme']) ? $themeDependencyScan : array(),
                'options' => array(
                    'backup_db' => !empty($options['backup_db']),
                    'db_scope' => $this->optionValue($options, 'db_scope', 'all'),
                    'backup_plugin' => !empty($options['backup_plugin']),
                    'backup_assets' => !empty($options['backup_assets']),
                    'backup_app_assets' => !empty($options['backup_app_assets']),
                    'backup_theme' => !empty($options['backup_theme']),
                    'theme_code' => $themeCode,
                    'theme_name' => $themeName,
                ),
                'asset_files' => $assetDownloads,
            );
            file_put_contents($payloadDir.'/manifest.json', json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            $baseName = $fixedBackupName !== '' ? preg_replace('/\.tar\.gz$/i', '', $fixedBackupName) : 'data_migration_backup_'.$timestamp;
            if (!is_string($baseName) || $baseName === '') {
                $baseName = 'data_migration_backup_'.$timestamp;
            }
            $tarPath = $tmpRoot.'/'.$baseName.'.tar';
            $gzPath = $tarPath.'.gz';
            if (is_file($tarPath)) {
                @unlink($tarPath);
            }
            if (is_file($gzPath)) {
                @unlink($gzPath);
            }

            $phar = new \PharData($tarPath);
            $phar->buildFromDirectory($payloadDir);
            $phar->compress(\Phar::GZ);
            if (is_file($tarPath)) {
                @unlink($tarPath);
            }

            $finalName = $fixedBackupName !== '' ? $fixedBackupName : $baseName.'.tar.gz';
            $finalPath = $backupDir.'/'.$finalName;
            if (is_file($finalPath)) {
                @unlink($finalPath);
            }
            if (is_file($finalPath.'.meta.json')) {
                @unlink($finalPath.'.meta.json');
            }
            if (!@rename($gzPath, $finalPath)) {
                if (!@copy($gzPath, $finalPath)) {
                    throw new \RuntimeException('バックアップファイルの保存に失敗しました。');
                }
                @unlink($gzPath);
            }
            $this->writeBackupMeta($finalPath, $manifest);

            $mainDownload = array(
                'name' => basename($finalPath),
                'size' => is_file($finalPath) ? (int) filesize($finalPath) : 0,
                'kind' => 'backup',
                'destination' => 'データ移行アシスタントへアップロード',
            );

            return array(
                'path' => $finalPath,
                'name' => basename($finalPath),
                'size' => is_file($finalPath) ? (int) filesize($finalPath) : 0,
                'components' => $components,
                'backup_profile' => $profile,
                'download_files' => array_merge(array($mainDownload), $assetDownloads),
            );
        } finally {
            $this->removePath($tmpRoot);
        }
    }

    private function sanitizeFixedBackupName($name)
    {
        $value = trim((string) $name);
        if ($value === '') {
            return '';
        }

        $value = basename($value);
        if ($value === '' || !$this->endsWith(strtolower($value), '.tar.gz')) {
            return '';
        }

        return $value;
    }

    public function listBackupFiles()
    {
        $dir = $this->precheckService->getBackupDir();
        if (!is_dir($dir)) {
            return array();
        }

        $files = array();
        $items = scandir($dir);
        if (!is_array($items)) {
            return array();
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (strpos($item, '.') === 0) {
                continue;
            }
            $path = $dir.'/'.$item;
            $lower = strtolower($item);
            if (!is_file($path) || $this->endsWith($item, '.meta.json')) {
                continue;
            }
            if (!$this->endsWith($lower, '.tar.gz') && !$this->endsWith($lower, '.zip')) {
                continue;
            }

            $meta = $this->readBackupMeta($path);
            $generatedAt = null;
            if (!empty($meta['generated_at'])) {
                try {
                    $generatedAt = new \DateTime((string) $meta['generated_at']);
                } catch (\Exception $e) {
                    $generatedAt = null;
                }
            }
            $typeLabel = '';
            if (!empty($meta['file_kind']) && $meta['file_kind'] === 'backup') {
                $typeLabel = $this->getProfileLabel($this->normalizeProfile($this->optionValue($meta, 'backup_profile', 'custom')));
            } elseif (!empty($meta['file_kind']) && $meta['file_kind'] === 'assets_html') {
                $typeLabel = '公開ディレクトリのファイル';
            } elseif (!empty($meta['file_kind']) && $meta['file_kind'] === 'assets_app') {
                $typeLabel = 'テンプレート側のファイル';
            } elseif (!empty($meta['file_label'])) {
                $typeLabel = (string) $meta['file_label'];
            } elseif ($this->endsWith(strtolower($item), '.zip')) {
                $typeLabel = '関連ファイル';
            } else {
                $typeLabel = $this->getProfileLabel($this->normalizeProfile($this->optionValue($meta, 'backup_profile', 'custom')));
            }

            $files[] = array(
                'name' => $item,
                'size' => (int) filesize($path),
                'modified_at' => $generatedAt ?: new \DateTime('@'.(string) filemtime($path)),
                'path' => $path,
                'type_label' => $typeLabel,
                'file_kind' => $this->optionValue($meta, 'file_kind', 'backup'),
                'backup_profile' => $this->normalizeProfile($this->optionValue($meta, 'backup_profile', 'custom')),
                'restore_available' => $this->optionValue($meta, 'file_kind', 'backup') === 'backup'
                    && $this->normalizeProfile($this->optionValue($meta, 'backup_profile', 'custom')) === 'migrate_default'
                    && in_array('database.sql', isset($meta['components']) && is_array($meta['components']) ? $meta['components'] : array(), true),
            );
        }

        usort($files, function ($a, $b) {
            $aTime = $a['modified_at'] instanceof \DateTime ? $a['modified_at']->getTimestamp() : 0;
            $bTime = $b['modified_at'] instanceof \DateTime ? $b['modified_at']->getTimestamp() : 0;
            if ($aTime === $bTime) {
                return strcmp((string) $b['name'], (string) $a['name']);
            }

            return ($aTime < $bTime) ? 1 : -1;
        });

        return $files;
    }

    public function resolveBackupFile($name)
    {
        $safe = basename($name);
        $lower = strtolower($safe);
        if ($safe === '') {
            return null;
        }
        if (!$this->endsWith($lower, '.tar.gz') && !$this->endsWith($lower, '.zip')) {
            return null;
        }

        $path = $this->precheckService->getBackupDir().'/'.$safe;

        return is_file($path) ? $path : null;
    }

    public function deleteBackupFile($name)
    {
        $path = $this->resolveBackupFile($name);
        if ($path === null) {
            return false;
        }

        $result = @unlink($path);
        $metaPath = $path.'.meta.json';
        if (is_file($metaPath)) {
            @unlink($metaPath);
        }

        return $result;
    }

    public function deleteAllBackupFiles()
    {
        $deleted = 0;
        foreach ($this->listBackupFiles() as $file) {
            if (is_array($file) && !empty($file['name']) && $this->deleteBackupFile($file['name'])) {
                ++$deleted;
            }
        }

        return $deleted;
    }

    public function getSourcePluginsSnapshot($enabledOnly = false)
    {
        $plugins = $this->buildSourcePluginsSnapshot();
        if (!$enabledOnly) {
            return $plugins;
        }

        return array_values(array_filter($plugins, function ($plugin) {
            return !empty($plugin['enabled']);
        }));
    }

    public function getCurrentDatabasePlatformName()
    {
        try {
            return (string) $this->entityManager->getConnection()->getDatabasePlatform()->getName();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getSourcePluginTableMap($pluginCodes = array())
    {
        $plugins = $this->getSourcePluginsSnapshot(true);
        if (!empty($pluginCodes) && is_array($pluginCodes)) {
            $selected = array();
            foreach ($pluginCodes as $code) {
                $trimmed = trim((string) $code);
                if ($trimmed !== '') {
                    $selected[$trimmed] = true;
                }
            }
            $plugins = array_values(array_filter($plugins, function ($plugin) use ($selected) {
                $code = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));

                return $code !== '' && isset($selected[$code]);
            }));
        }

        return $this->buildSourcePluginTableMap($plugins);
    }

    private function dumpDatabaseSql($sqlPath, $tables)
    {
        $conn = $this->entityManager->getConnection();
        $fp = fopen($sqlPath, 'wb');
        if ($fp === false) {
            throw new \RuntimeException('SQLファイルを作成できません。');
        }

        fwrite($fp, "-- DataMigrationBackup30 SQL Dump\n");
        fwrite($fp, '-- Generated at: '.date('Y-m-d H:i:s')."\n\n");

        foreach ($tables as $table) {
            $quotedTable = $conn->getDatabasePlatform()->quoteIdentifier($table);
            fwrite($fp, '-- Table: '.$table."\n");
            $stmt = $conn->executeQuery('SELECT * FROM '.$quotedTable);
            while (($row = $this->fetchAssocRow($stmt)) !== false) {
                $columns = array_keys($row);
                if (empty($columns)) {
                    continue;
                }

                $columnSql = implode(', ', array_map(array($this, 'normalizeSqlIdentifier'), $columns));
                $values = array();
                foreach ($columns as $column) {
                    $values[] = $this->toSqlValue($conn, array_key_exists($column, $row) ? $row[$column] : null);
                }

                fwrite(
                    $fp,
                    sprintf(
                        "INSERT INTO %s (%s) VALUES (%s);\n",
                        $this->normalizeSqlIdentifier($table),
                        $columnSql,
                        implode(', ', $values)
                    )
                );
            }
            fwrite($fp, "\n");
        }

        fclose($fp);
    }

    private function fetchAssocRow($stmt)
    {
        if (method_exists($stmt, 'fetchAssociative')) {
            return $stmt->fetchAssociative();
        }

        if (method_exists($stmt, 'fetch')) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        return false;
    }

    private function toSqlValue($conn, $value)
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $conn->quote((string) $value);
    }

    private function normalizeSqlIdentifier($name)
    {
        return $this->entityManager->getConnection()->getDatabasePlatform()->quoteIdentifier($name);
    }

    private function writeDatabaseSchemaJson($path, $tables)
    {
        $conn = $this->entityManager->getConnection();
        $schemaManager = method_exists($conn, 'createSchemaManager') ? $conn->createSchemaManager() : $conn->getSchemaManager();
        $payload = array(
            'generated_at' => date('c'),
            'database_platform' => $conn->getDatabasePlatform()->getName(),
            'tables' => array(),
        );

        foreach ($tables as $table) {
            try {
                $columns = $schemaManager->listTableColumns($table);
                $indexes = $schemaManager->listTableIndexes($table);
            } catch (\Exception $e) {
                continue;
            }

            $columnRows = array();
            foreach ($columns as $name => $column) {
                if (!is_string($name)) {
                    continue;
                }
                $type = '';
                if (is_object($column) && method_exists($column, 'getType')) {
                    $typeObj = $column->getType();
                    if (is_object($typeObj) && method_exists($typeObj, 'getName')) {
                        $type = (string) $typeObj->getName();
                    } else {
                        $type = (string) $typeObj;
                    }
                }
                $columnRows[$name] = array(
                    'type' => $type,
                    'length' => method_exists($column, 'getLength') ? $column->getLength() : null,
                    'precision' => method_exists($column, 'getPrecision') ? $column->getPrecision() : null,
                    'scale' => method_exists($column, 'getScale') ? $column->getScale() : null,
                    'notnull' => method_exists($column, 'getNotnull') ? $column->getNotnull() : null,
                    'default' => method_exists($column, 'getDefault') ? $column->getDefault() : null,
                );
            }

            $primaryKeys = array();
            foreach ($indexes as $index) {
                if (is_object($index) && method_exists($index, 'isPrimary') && $index->isPrimary()) {
                    $primaryKeys = array_values(array_map('strval', $index->getColumns()));
                    break;
                }
            }

            $payload['tables'][$table] = array(
                'columns' => $columnRows,
                'primary_keys' => $primaryKeys,
            );
        }

        file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    private function createPluginZip($sourceDir, $zipPath)
    {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException('ZipArchive を利用できません。');
        }
        if (!is_dir($sourceDir)) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('plugins.zip を作成できません。');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }
            $filePath = $file->getPathname();
            $localName = substr($filePath, strlen(rtrim($sourceDir, '/')) + 1);
            $zip->addFile($filePath, str_replace('\\', '/', $localName));
        }

        $zip->close();
    }

    private function createArchiveFromMap($map, $archivePath, $workDir)
    {
        $this->removePath($workDir);
        @mkdir($workDir, 0777, true);

        $hasEntries = false;
        $emptyDirs = array();
        foreach ($map as $target => $source) {
            if (!is_string($target) || !is_string($source)) {
                continue;
            }
            $normalizedTarget = ltrim(trim($target), '/');
            if ($normalizedTarget === '') {
                continue;
            }
            $destination = rtrim($workDir, '/').'/'.$normalizedTarget;
            if (file_exists($source)) {
                $this->copyPath($source, $destination);
                $hasEntries = true;
            } else {
                @mkdir($destination, 0777, true);
                $hasEntries = true;
            }
            $emptyDirs[] = $normalizedTarget;
        }

        if (!$hasEntries) {
            return;
        }

        $tarPath = substr($archivePath, -3) === '.gz' ? substr($archivePath, 0, -3) : $archivePath.'.tar';
        if (is_file($tarPath)) {
            @unlink($tarPath);
        }
        if (is_file($archivePath)) {
            @unlink($archivePath);
        }

        $phar = new \PharData($tarPath);
        $built = $phar->buildFromDirectory($workDir);
        if (!is_array($built) || count($built) === 0) {
            foreach ($emptyDirs as $dir) {
                $dir = trim((string) $dir, '/');
                if ($dir === '') {
                    continue;
                }
                if (!isset($phar[$dir])) {
                    $phar->addEmptyDir($dir);
                }
            }
        }
        $phar->compress(\Phar::GZ);
        if (is_file($tarPath)) {
            @unlink($tarPath);
        }
    }

    private function copyPath($source, $destination)
    {
        if (is_file($source)) {
            @mkdir(dirname($destination), 0777, true);
            @copy($source, $destination);
            return;
        }

        if (!is_dir($source)) {
            return;
        }

        @mkdir($destination, 0777, true);
        $items = scandir($source);
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $this->copyPath($source.'/'.$item, $destination.'/'.$item);
        }
    }

    private function buildSourcePluginsSnapshot()
    {
        try {
            $plugins = $this->entityManager->getRepository('Eccube\\Entity\\Plugin')->findBy(array(), array('code' => 'ASC'));
        } catch (\Exception $e) {
            return array();
        }

        $rows = array();
        foreach ($plugins as $plugin) {
            if (!is_object($plugin) || !method_exists($plugin, 'getCode')) {
                continue;
            }
            $rows[] = array(
                'code' => (string) $plugin->getCode(),
                'name' => method_exists($plugin, 'getName') ? (string) $plugin->getName() : (string) $plugin->getCode(),
                'version' => method_exists($plugin, 'getVersion') ? (string) $plugin->getVersion() : '',
                'enabled' => method_exists($plugin, 'getEnable') ? ((int) $plugin->getEnable() === 1) : false,
            );
        }

        return $rows;
    }

    private function buildSourcePluginTableMap($plugins)
    {
        try {
            $conn = $this->entityManager->getConnection();
            $schemaManager = method_exists($conn, 'createSchemaManager') ? $conn->createSchemaManager() : $conn->getSchemaManager();
        } catch (\Exception $e) {
            return array();
        }

        $rows = array();
        foreach ($plugins as $plugin) {
            $code = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));
            if ($code === '' || empty($plugin['enabled'])) {
                continue;
            }

            $tables = $this->collectPluginTablesFromEntity($code, $schemaManager);
            if (!empty($tables)) {
                $rows[$code] = array('tables' => $tables);
            }
        }

        return $rows;
    }

    private function collectPluginTablesFromEntity($pluginCode, $schemaManager)
    {
        $entityDir = $this->getProjectDir().'/app/Plugin/'.$pluginCode.'/Entity';
        if (!is_dir($entityDir)) {
            return array();
        }

        $tables = array();
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($entityDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }
            if (strtolower((string) $file->getExtension()) !== 'php') {
                continue;
            }

            $filePath = $file->getPathname();
            $tableNames = $this->extractTableNamesFromEntityFile($filePath);
            if (empty($tableNames)) {
                $className = $this->resolvePluginEntityClassName($pluginCode, $entityDir, $filePath);
                if ($className !== null && class_exists($className)) {
                    try {
                        $classMetadata = $this->entityManager->getClassMetadata($className);
                        if (method_exists($classMetadata, 'getTableName') && empty($classMetadata->isMappedSuperclass)) {
                            $tableNames[] = strtolower((string) $classMetadata->getTableName());
                        }
                    } catch (\Exception $e) {
                    }
                }
            }

            foreach ($tableNames as $tableName) {
                $table = strtolower(trim((string) $tableName));
                if ($table === '' || !$this->startsWith($table, 'plg_')) {
                    continue;
                }
                $tables[$table] = true;
            }
        }

        $rows = array();
        foreach (array_keys($tables) as $tableName) {
            try {
                if (method_exists($schemaManager, 'tablesExist') && !$schemaManager->tablesExist(array($tableName))) {
                    continue;
                }
                $columns = $schemaManager->listTableColumns($tableName);
            } catch (\Exception $e) {
                continue;
            }
            if (empty($columns)) {
                continue;
            }

            $columnRows = array();
            foreach ($columns as $column) {
                if (!is_object($column) || !method_exists($column, 'getName')) {
                    continue;
                }
                $columnName = strtolower((string) $column->getName());
                if ($columnName === '') {
                    continue;
                }
                $type = '';
                if (method_exists($column, 'getType')) {
                    $typeObject = $column->getType();
                    if (is_object($typeObject) && method_exists($typeObject, 'getName')) {
                        $type = (string) $typeObject->getName();
                    } else {
                        $type = (string) $typeObject;
                    }
                }
                $columnRows[$columnName] = array('type' => $type);
            }
            if (empty($columnRows)) {
                continue;
            }

            $rows[$tableName] = array('columns' => $columnRows);
        }

        if (!empty($rows)) {
            ksort($rows);
        }

        return $rows;
    }

    private function extractTableNamesFromEntityFile($filePath)
    {
        $raw = @file_get_contents($filePath);
        if ($raw === false || trim($raw) === '') {
            return array();
        }

        $rows = array();
        preg_match_all('/@ORM\\\\Table\\(\\s*name\\s*=\\s*[\'"]([^\'"]+)[\'"]\\s*\\)/i', $raw, $matches);
        if (isset($matches[1]) && is_array($matches[1])) {
            foreach ($matches[1] as $name) {
                $table = strtolower(trim((string) $name));
                if ($table !== '') {
                    $rows[] = $table;
                }
            }
        }
        preg_match_all('/#\\[\\s*ORM\\\\Table\\(\\s*name\\s*:\\s*[\'"]([^\'"]+)[\'"]\\s*\\)\\s*\\]/i', $raw, $attributeMatches);
        if (isset($attributeMatches[1]) && is_array($attributeMatches[1])) {
            foreach ($attributeMatches[1] as $name) {
                $table = strtolower(trim((string) $name));
                if ($table !== '') {
                    $rows[] = $table;
                }
            }
        }

        $rows = array_values(array_unique($rows));
        sort($rows);

        return $rows;
    }

    private function resolvePluginEntityClassName($pluginCode, $entityDir, $filePath)
    {
        $relativePath = ltrim(str_replace($entityDir, '', $filePath), '/');
        if ($relativePath === '' || !$this->endsWith(strtolower($relativePath), '.php')) {
            return null;
        }
        $relativeClass = substr($relativePath, 0, -4);
        $relativeClass = str_replace('/', '\\', $relativeClass);

        return 'Plugin\\'.$pluginCode.'\\Entity\\'.$relativeClass;
    }

    private function resolveThemeName($themeCode)
    {
        try {
            $template = $this->entityManager->getRepository('Eccube\\Entity\\Template')->findOneBy(array('code' => $themeCode));
            if (is_object($template) && method_exists($template, 'getName')) {
                $name = trim((string) $template->getName());
                if ($name !== '') {
                    return $name;
                }
            }
        } catch (\Exception $e) {
        }

        return $themeCode;
    }

    private function prepareThemeBackupSources($themeCode, $workRoot)
    {
        $code = trim((string) $themeCode);
        if ($code === '') {
            $code = 'default';
        }

        $projectDir = $this->getProjectDir();
        $archiveMap = array();
        $scanRoots = array();
        $normalizedWorkRoot = rtrim((string) $workRoot, '/');

        $appThemePath = $projectDir.'/app/template/'.$code;
        if (strtolower($code) === 'default') {
            $coreDefaultPath = $projectDir.'/src/Eccube/Resource/template/default';
            $effectiveAppPath = $normalizedWorkRoot.'/app/template/default';
            $this->removePath($effectiveAppPath);

            if (is_dir($coreDefaultPath)) {
                $this->copyPath($coreDefaultPath, $effectiveAppPath);
            }
            if (is_dir($appThemePath)) {
                $this->copyPath($appThemePath, $effectiveAppPath);
            }

            $archiveMap['app/template/default'] = is_dir($effectiveAppPath) ? $effectiveAppPath : $appThemePath;
            $scanRoots['app/template/default'] = is_dir($effectiveAppPath) ? $effectiveAppPath : $appThemePath;
        } else {
            $archiveMap['app/template/'.$code] = $appThemePath;
            $scanRoots['app/template/'.$code] = $appThemePath;
        }

        $htmlThemePath = $projectDir.'/html/template/'.$code;
        $archiveMap['html/template/'.$code] = $htmlThemePath;
        $scanRoots['html/template/'.$code] = $htmlThemePath;

        return array(
            'archive_map' => $archiveMap,
            'scan_roots' => $scanRoots,
        );
    }

    private function collectThemeDependencyScan($themeCode, array $sourcePlugins = [], array $scanRoots = array())
    {
        $code = trim((string) $themeCode);
        if ($code === '') {
            $code = 'default';
        }

        $temporaryRoot = '';
        if (empty($scanRoots)) {
            $temporaryRoot = sys_get_temp_dir().'/dmbk_theme_scan_'.sha1($code.'|'.microtime(true));
            $prepared = $this->prepareThemeBackupSources($code, $temporaryRoot);
            $roots = isset($prepared['scan_roots']) && is_array($prepared['scan_roots']) ? $prepared['scan_roots'] : array();
        } else {
            $roots = $scanRoots;
        }
        $references = array();
        $dependencies = array();
        $themeExists = false;
        $pluginNameMap = $this->buildPluginCodeNameMap($sourcePlugins);

        try {
            foreach ($roots as $prefix => $directory) {
                if (!is_dir($directory)) {
                    continue;
                }

                $themeExists = true;
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $File) {
                    if (!$File instanceof \SplFileInfo || !$File->isFile()) {
                        continue;
                    }

                    $filePath = str_replace('\\', '/', (string) $File->getPathname());
                    if (strtolower(substr($filePath, -5)) !== '.twig') {
                        continue;
                    }

                    $raw = @file_get_contents($filePath);
                    if (!is_string($raw) || trim($raw) === '') {
                        continue;
                    }

                    $relative = ltrim(str_replace(str_replace('\\', '/', $directory), '', $filePath), '/');
                    foreach ($this->extractThemePluginReferencesFromTwigContent($raw) as $reference) {
                        $pluginCode = trim((string) (isset($reference['plugin_code']) ? $reference['plugin_code'] : ''));
                        if ($pluginCode === '') {
                            continue;
                        }
                        $dependencies[$pluginCode] = true;
                        $references[] = array(
                            'file' => $prefix.'/'.$relative,
                            'plugin_code' => $pluginCode,
                            'plugin_name' => isset($pluginNameMap[strtolower($pluginCode)]) ? $pluginNameMap[strtolower($pluginCode)] : '',
                            'line' => (int) (isset($reference['line']) ? $reference['line'] : 0),
                            'snippet' => isset($reference['snippet']) ? (string) $reference['snippet'] : '',
                        );
                    }
                }
            }
        } finally {
            if ($temporaryRoot !== '') {
                $this->removePath($temporaryRoot);
            }
        }

        $dependencyList = array_values(array_keys($dependencies));
        sort($dependencyList);

        return array(
            'scanned' => $themeExists,
            'dependencies' => $dependencyList,
            'references' => $references,
            'note' => $themeExists ? '' : 'テーマディレクトリが見つかりません。',
        );
    }

    private function extractThemePluginReferencesFromTwigContent($content)
    {
        $raw = (string) $content;
        if (trim($raw) === '') {
            return array();
        }

        $rows = array();
        $lines = preg_split('/\R/u', $raw);
        if (!is_array($lines)) {
            $lines = array($raw);
        }
        foreach ($lines as $index => $line) {
            $lineText = (string) $line;
            if ($lineText === '') {
                continue;
            }
            if (preg_match_all('/Plugin(?:\\\\+|\/+)([A-Za-z0-9_]+)/u', $lineText, $matches) > 0 && isset($matches[1]) && is_array($matches[1])) {
                foreach ($matches[1] as $pluginCode) {
                    $code = trim((string) $pluginCode);
                    if ($code === '' || $this->isInternalMigrationPluginCode($code)) {
                        continue;
                    }
                    $lineNumber = (int) $index + 1;
                    $snippet = trim(preg_replace('/\s+/u', ' ', $lineText));
                    $key = strtolower($code.'|'.$lineNumber.'|'.$snippet);
                    $rows[$key] = array(
                        'plugin_code' => $code,
                        'line' => $lineNumber,
                        'snippet' => $snippet,
                    );
                }
            }
        }

        $result = array_values($rows);
        usort($result, static function ($left, $right) {
            $leftCode = isset($left['plugin_code']) ? (string) $left['plugin_code'] : '';
            $rightCode = isset($right['plugin_code']) ? (string) $right['plugin_code'] : '';
            if ($leftCode === $rightCode) {
                $leftLine = (int) (isset($left['line']) ? $left['line'] : 0);
                $rightLine = (int) (isset($right['line']) ? $right['line'] : 0);
                if ($leftLine === $rightLine) {
                    return 0;
                }

                return ($leftLine < $rightLine) ? -1 : 1;
            }

            return strcmp($leftCode, $rightCode);
        });

        return $result;
    }

    private function buildPluginCodeNameMap(array $plugins)
    {
        $rows = array();
        foreach ($plugins as $plugin) {
            if (!is_array($plugin)) {
                continue;
            }
            $code = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));
            $name = trim((string) (
                isset($plugin['name']) ? $plugin['name']
                    : (isset($plugin['plugin_name']) ? $plugin['plugin_name']
                    : (isset($plugin['pluginName']) ? $plugin['pluginName']
                    : (isset($plugin['title']) ? $plugin['title'] : '')))
            ));
            if ($code === '' || $name === '') {
                continue;
            }
            $rows[strtolower($code)] = $name;
        }

        return $rows;
    }

    private function isInternalMigrationPluginCode($code)
    {
        $normalized = strtolower(trim((string) $code));
        $normalized = preg_replace('/[^a-z0-9]/', '', $normalized) ?: $normalized;

        return strpos($normalized, 'datamigrationassistant') === 0
            || strpos($normalized, 'datamigrationbackup') === 0;
    }

    private function writePrecheckJson($path, $precheck)
    {
        $checks = array();
        $rows = isset($precheck['rows']) && is_array($precheck['rows']) ? $precheck['rows'] : array();
        $facts = isset($precheck['facts']) && is_array($precheck['facts']) ? $precheck['facts'] : array();
        $eccubeVersion = trim((string) $this->optionValue($facts, 'eccube_version', ''));
        if ($eccubeVersion === '') {
            $eccubeVersion = Constant::VERSION;
        }
        $databaseTables = array();
        if (isset($facts['database_tables']) && is_array($facts['database_tables'])) {
            $databaseTables = array_values(array_unique(array_map('strval', $facts['database_tables'])));
        } elseif (isset($facts['db_tables']) && is_array($facts['db_tables'])) {
            $databaseTables = array_values(array_unique(array_map('strval', $facts['db_tables'])));
        }
        $facts['eccube_version'] = $eccubeVersion;
        $facts['database_tables'] = $databaseTables;
        foreach ($rows as $row) {
            $checks[] = array(
                'label' => $this->optionValue($row, 'label', ''),
                'value' => $this->optionValue($row, 'value', ''),
                'ok' => !empty($row['ok']),
                'note' => $this->optionValue($row, 'note', ''),
            );
        }

        $payload = array(
            'generated_at' => date('c'),
            'has_warning' => !empty($precheck['has_warning']),
            'estimated_bytes' => (int) $this->optionValue($precheck, 'estimated_bytes', 0),
            'required_bytes' => (int) $this->optionValue($precheck, 'required_bytes', 0),
            'free_bytes' => (int) $this->optionValue($precheck, 'free_bytes', 0),
            'eccube_version' => $eccubeVersion,
            'database_tables' => $databaseTables,
            'facts' => $facts,
            'checks' => $checks,
        );

        file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    private function writeBackupMeta($backupPath, $manifest)
    {
        $meta = array(
            'backup_profile' => $this->normalizeProfile($this->optionValue($manifest, 'backup_profile', 'custom')),
            'generated_at' => $this->optionValue($manifest, 'generated_at', ''),
            'components' => isset($manifest['components']) && is_array($manifest['components']) ? $manifest['components'] : array(),
            'options' => isset($manifest['options']) && is_array($manifest['options']) ? $manifest['options'] : array(),
            'file_kind' => $this->optionValue($manifest, 'file_kind', 'backup'),
            'file_label' => $this->optionValue($manifest, 'file_label', ''),
        );

        file_put_contents($backupPath.'.meta.json', json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    private function readBackupMeta($backupPath)
    {
        $metaPath = $backupPath.'.meta.json';
        if (!is_file($metaPath)) {
            return array();
        }

        $raw = @file_get_contents($metaPath);
        if ($raw === false || trim($raw) === '') {
            return array();
        }

        $json = json_decode($raw, true);

        return is_array($json) ? $json : array();
    }

    private function resolveSourceAuthProfile()
    {
        $configPath = $this->getProjectDir().'/app/config/eccube/config.yml';
        if (!is_file($configPath)) {
            return array();
        }

        try {
            $config = Yaml::parse(file_get_contents($configPath));
        } catch (\Throwable $e) {
            return array();
        }

        if (!is_array($config)) {
            return array();
        }

        $authMagic = trim((string) $this->optionValue($config, 'auth_magic', ''));
        if ($authMagic === '') {
            return array();
        }

        $authType = strtoupper(trim((string) $this->optionValue($config, 'auth_type', 'HMAC')));
        if ($authType === '') {
            $authType = 'HMAC';
        }

        $hashAlgo = strtoupper(trim((string) $this->optionValue($config, 'password_hash_algos', 'SHA256')));
        if ($hashAlgo === '') {
            $hashAlgo = 'SHA256';
        }

        return array(
            'auth_magic' => $authMagic,
            'auth_type' => $authType,
            'password_hash_algos' => $hashAlgo,
        );
    }

    private function normalizeProfile($profile)
    {
        return in_array($profile, array('migrate_default', 'custom'), true) ? $profile : 'custom';
    }

    private function getProfileLabel($profile)
    {
        return $profile === 'migrate_default' ? '移行用デフォルト' : 'カスタム';
    }

    private function removePath($path)
    {
        if (is_file($path) || is_link($path)) {
            @unlink($path);
            return;
        }
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if (is_array($items)) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $this->removePath($path.'/'.$item);
            }
        }

        @rmdir($path);
    }

    private function extendExecutionTime()
    {
        @set_time_limit(0);
    }

    private function getProjectDir()
    {
        return $this->projectDir;
    }

    private function optionValue($array, $key, $default)
    {
        if (is_array($array) && array_key_exists($key, $array) && $array[$key] !== null) {
            return $array[$key];
        }

        return $default;
    }

    private function startsWith($value, $prefix)
    {
        if ($prefix === '') {
            return true;
        }

        return strpos($value, $prefix) === 0;
    }

    private function endsWith($value, $suffix)
    {
        if ($suffix === '') {
            return true;
        }

        $suffixLen = strlen($suffix);
        if ($suffixLen > strlen($value)) {
            return false;
        }

        return substr($value, -$suffixLen) === $suffix;
    }
}
