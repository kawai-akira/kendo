<?php

namespace Plugin\DataMigrationBackup30\Controller\Admin;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BackupController extends AbstractController
{
    const SCANNER_SOURCE_MODE_CURRENT = 'current';
    const SCANNER_SOURCE_MODE_SAVED = 'saved';
    const SCANNER_SOURCE_MODE_UPLOAD = 'upload';
    const SCANNER_DEFAULT_SOURCE_BACKUP_NAME = 'data_migration_backup_default.tar.gz';

    /** @var \Eccube\Application */
    private $app;

    /** @var object */
    private $precheckService;

    /** @var object */
    private $backupBuilderService;

    public function index(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        $tableOptions = $this->precheckService->listTables();
        $themeCode = isset($app['config']['template_code']) ? (string) $app['config']['template_code'] : 'default';
        $formData = $this->buildFormData($request, $tableOptions, $themeCode);
        $checks = $this->precheckService->collectChecks($formData);

        return $app->render('DataMigrationBackup30/Resource/template/admin/index.twig', $this->withToken(array(
            'formData' => $formData,
            'tableOptions' => $tableOptions,
            'precheck' => $checks,
        )));
    }

    public function files(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        $perPage = 20;
        $page = max(1, (int) $request->get('page', 1));
        $allFiles = $this->backupBuilderService->listBackupFiles();
        $total = count($allFiles);
        $pages = max(1, (int) ceil($total / $perPage));
        if ($page > $pages) {
            $page = $pages;
        }

        $offset = ($page - 1) * $perPage;
        $backupFiles = array_slice($allFiles, $offset, $perPage);

        return $app->render('DataMigrationBackup30/Resource/template/admin/files.twig', $this->withToken(array(
            'backupFiles' => $backupFiles,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
            'perPage' => $perPage,
        )));
    }

    public function guide(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        return $app->render('DataMigrationBackup30/Resource/template/admin/guide.twig', $this->withToken(array()));
    }

    public function scanner(Application $app, Request $request)
    {
        $this->bootstrapContext($app);
        $sourceInfo = $this->buildScannerSourceInfo();
        $sourceFileOptions = $this->buildSourceFileOptions();
        $targetVersionOptions = $this->getTargetVersionOptions();
        $targetVersion = !empty($targetVersionOptions) ? (string) $targetVersionOptions[0]['value'] : '';
        $targetFileMode = 'default';
        $sourceSelectMode = self::SCANNER_SOURCE_MODE_CURRENT;
        $sourceUploadToken = $this->normalizeString($request->get('source_upload_token', ''), '');
        $sourceUploadInfo = $this->getSavedScannerUploadFile($sourceUploadToken);
        $targetUploadToken = $this->normalizeString($request->get('target_upload_token', ''), '');
        $targetUploadInfo = $this->getSavedScannerUploadFile($targetUploadToken);
        $targetInfo = null;
        $connectorRows = array();
        $connectorTargetCodes = array();
        $pluginConnectorEnabled = false;
        $moduleDefinitions = array();
        $scannerOptions = $this->buildDefaultScannerOptions($moduleDefinitions);
        $scannerModuleSections = $this->buildScannerModuleSections($moduleDefinitions);
        $scanResult = null;
        $step = 1;

        $presetBackupName = $this->normalizeString($request->get('preset_backup_name', ''), '');
        $sourceFileKey = $this->normalizeString($request->get('source_file_key', $presetBackupName), '');
        if ($presetBackupName !== '') {
            $sourceSelectMode = self::SCANNER_SOURCE_MODE_SAVED;
        }
        if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_SAVED && $sourceFileKey === '' && !empty($sourceFileOptions)) {
            $sourceFileKey = (string) $sourceFileOptions[0]['key'];
        }
        $sourceFileInfo = $this->findSourceFileOption($sourceFileOptions, $sourceFileKey);
        if (!is_array($sourceFileInfo) && $sourceFileKey !== '') {
            $sourceFileInfo = $this->buildSourceFileOptionFromBackupName($sourceFileKey);
        }
        $sourceSnapshot = null;

        if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_SAVED && $sourceFileKey !== '') {
            try {
                $sourceSnapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
                if (is_array($sourceSnapshot)) {
                    $sourceInfo = $this->buildScannerInfoFromSnapshot($sourceSnapshot, $sourceInfo);
                }
            } catch (\Exception $e) {
                $app->addError($e->getMessage(), 'admin');
            }
        }

        if ($request->isMethod('POST')) {
            try {
                $this->isTokenValid($app);
                $action = $this->normalizeString($request->get('action', 'to_step2'), 'to_step2');
                $targetVersion = $this->normalizeString($request->get('target_version', $targetVersion), $targetVersion);
                $targetFileMode = $this->normalizeTargetFileMode(
                    $this->normalizeString($request->get('target_file_mode', $targetFileMode), $targetFileMode),
                    $targetFileMode
                );
                $sourceSelectMode = $this->normalizeSourceSelectMode(
                    $this->normalizeString($request->get('source_select_mode', $sourceSelectMode), $sourceSelectMode),
                    $sourceSelectMode
                );
                $sourceFileKey = $this->normalizeString($request->get('source_file_key', $sourceFileKey), $sourceFileKey);
                $sourceUploadToken = $this->normalizeString($request->get('source_upload_token', $sourceUploadToken), $sourceUploadToken);
                $sourceUploadInfo = $this->getSavedScannerUploadFile($sourceUploadToken);
                $targetUploadToken = $this->normalizeString($request->get('target_upload_token', $targetUploadToken), $targetUploadToken);
                $targetUploadInfo = $this->getSavedScannerUploadFile($targetUploadToken);
                $sourceFileInfo = $this->findSourceFileOption($sourceFileOptions, $sourceFileKey);
                if (!is_array($sourceFileInfo) && $sourceFileKey !== '') {
                    $sourceFileInfo = $this->buildSourceFileOptionFromBackupName($sourceFileKey);
                }
                $pluginConnectorEnabled = $request->request->has('enable_plugin_connectors');
                $hasVisibleScannerModuleSelection = $this->countVisibleScannerModulesLocal(
                    $scannerOptions['modules'],
                    $moduleDefinitions
                ) > 0;

                if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_CURRENT) {
                    $currentBackup = $this->createScannerDefaultSourceBackupLocal();
                    $sourceFileKey = isset($currentBackup['key']) ? (string) $currentBackup['key'] : self::SCANNER_DEFAULT_SOURCE_BACKUP_NAME;
                    $sourceFileInfo = $currentBackup;
                    $sourceUploadToken = '';
                    $sourceUploadInfo = null;
                    $sourceSnapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
                } elseif ($sourceSelectMode === self::SCANNER_SOURCE_MODE_UPLOAD) {
                    if (!is_array($sourceUploadInfo)) {
                        throw new \RuntimeException('移行元バックアップファイルをアップロードしてください。');
                    }
                    $sourceSnapshot = $this->loadSnapshotFromScannerUploadToken($sourceUploadToken);
                    $sourceFileKey = '';
                    $sourceFileInfo = null;
                } else {
                    $sourceUploadToken = '';
                    $sourceUploadInfo = null;
                    if ($sourceFileKey === '') {
                        throw new \RuntimeException('移行元バックアップファイルを選択してください。');
                    }
                    $sourceSnapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
                    if (!is_array($sourceFileInfo)) {
                        $sourceFileInfo = $this->buildSourceFileOptionFromBackupName($sourceFileKey);
                    }
                }
                if (is_array($sourceSnapshot)) {
                    $sourceInfo = $this->buildScannerInfoFromSnapshot($sourceSnapshot, $sourceInfo);
                }
                if ($targetFileMode === 'upload') {
                    if (!is_array($targetUploadInfo)) {
                        throw new \RuntimeException('移行先バックアップファイルをアップロードしてください。');
                    }
                    $targetInfo = $this->buildScannerTargetInfoFromUploadToken($targetUploadToken, $targetVersion);
                } else {
                    $targetUploadToken = 'default:'.$targetVersion;
                    $targetUploadInfo = array(
                        'token' => $targetUploadToken,
                        'name' => $targetVersion !== '' ? $targetVersion.'.zip' : '',
                        'size' => 0,
                        'size_label' => '0 MB',
                        'updated_at' => '',
                    );
                    $targetInfo = $this->buildScannerTargetInfoFromUploadToken($targetUploadToken, $targetVersion);
                }

                $targetSnapshot = $this->loadSnapshotFromScannerUploadToken($targetUploadToken);
                if (is_array($sourceSnapshot) && is_array($targetSnapshot)) {
                    $moduleDefinitions = $this->buildScannerModuleDefinitionsForContext($sourceSnapshot, $targetSnapshot);
                    $scannerModuleSections = $this->buildScannerModuleSections($moduleDefinitions);
                    $scannerOptions = $this->normalizeScannerOptionsFromRequest(
                        $request,
                        $this->buildDefaultScannerOptions($moduleDefinitions),
                        $moduleDefinitions
                    );
                    $hasVisibleScannerModuleSelection = $this->countVisibleScannerModulesLocal(
                        $scannerOptions['modules'],
                        $moduleDefinitions
                    ) > 0;
                    $scannerOptions['modules'] = $this->appendHiddenScannerModulesLocal(
                        $scannerOptions['modules'],
                        $moduleDefinitions
                    );
                }

                if ($action === 'run_scan' && !$hasVisibleScannerModuleSelection) {
                    $app->addError($app->trans('data_migration_backup.admin.scanner.modules_required'), 'admin');
                    $action = 'refresh_step2';
                }

                if ($action === 'to_step2' || $action === 'refresh_step2' || $action === 'back_step2' || $action === 'run_scan') {
                    $step = ($action === 'run_scan') ? 3 : 2;
                    $connectorRows = $this->buildConnectorRows($sourceInfo, $targetInfo);
                    $connectorTargetCodes = $this->normalizeConnectorTargetCodesFromRequest($request, $connectorRows);
                    if ($pluginConnectorEnabled && empty($connectorTargetCodes)) {
                        $connectorTargetCodes = $this->autoSelectConnectorTargetCodes($connectorRows);
                    }
                    if (!$pluginConnectorEnabled) {
                        $connectorTargetCodes = array();
                    }
                    $connectorRows = $this->applyConnectorSelection($connectorRows, $connectorTargetCodes);
                    $connectorStatusMap = $this->normalizeConnectorStatusMapFromRequest($request, $connectorRows);
                    $connectorNoteMap = $this->normalizeConnectorNoteMapFromRequest($request, $connectorRows);
                    if ($pluginConnectorEnabled) {
                        $connectorRows = $this->applyConnectorScanResults(
                            $connectorRows,
                            $connectorTargetCodes,
                            $targetUploadToken,
                            $sourceSnapshot
                        );
                    } elseif (!empty($connectorStatusMap) || !empty($connectorNoteMap)) {
                        $connectorRows = $this->applyConnectorStatusOverrides($connectorRows, $connectorStatusMap, $connectorNoteMap);
                    }

                    if ($action === 'run_scan') {
                        $scanResult = $this->buildScanResult(
                            $sourceSnapshot,
                            $this->resolveScannerSourceArchivePathLocal($sourceSelectMode, $sourceFileKey, $sourceUploadToken),
                            $targetUploadToken,
                            $targetVersion,
                            $scannerOptions,
                            $pluginConnectorEnabled,
                            $connectorRows
                        );
                    }
                } else {
                    $step = 1;
                }
            } catch (\Exception $e) {
                $app->addError($e->getMessage(), 'admin');
            }
        }

        return $app->render('DataMigrationBackup30/Resource/template/admin/scanner.twig', $this->withToken(array(
            'step' => $step,
            'sourceInfo' => $sourceInfo,
            'sourceFileOptions' => $sourceFileOptions,
            'sourceSelectMode' => $sourceSelectMode,
            'sourceFileKey' => $sourceFileKey,
            'sourceFileInfo' => $sourceFileInfo,
            'sourceUploadToken' => $sourceUploadToken,
            'sourceUploadInfo' => $sourceUploadInfo,
            'targetVersionOptions' => $targetVersionOptions,
            'targetVersion' => $targetVersion,
            'targetFileMode' => $targetFileMode,
            'connectorTargetCodes' => $connectorTargetCodes,
            'connectorRows' => $connectorRows,
            'targetUploadToken' => $targetUploadToken,
            'targetUploadInfo' => $targetUploadInfo,
            'targetInfo' => $targetInfo,
            'pluginConnectorEnabled' => $pluginConnectorEnabled,
            'scanResult' => $scanResult,
            'scannerOptions' => $scannerOptions,
            'scannerModuleSections' => $scannerModuleSections,
        )));
    }

    public function scannerChunkInit(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        try {
            $this->validateFlexibleCsrfToken($app, $request);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.invalid_csrf'),
            ), 400);
        }

        $fileName = $this->normalizeString($request->request->get('file_name', ''), '');
        $totalChunks = max(1, (int) $request->request->get('total_chunks', 1));
        $fileSize = max(0, (int) $request->request->get('file_size', 0));

        try {
            $upload = $this->initChunkUploadLocal($fileName, $totalChunks, $fileSize);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), 400);
        }

        return $app->json(array(
            'success' => true,
            'upload_id' => (string) (isset($upload['id']) ? $upload['id'] : ''),
            'total_chunks' => (int) (isset($upload['total_chunks']) ? $upload['total_chunks'] : 1),
        ));
    }

    public function scannerChunkAppend(Application $app, Request $request, $uploadId)
    {
        $this->bootstrapContext($app);

        try {
            $this->validateFlexibleCsrfToken($app, $request);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.invalid_csrf'),
            ), 400);
        }

        $chunkIndex = (int) $request->get('chunk_index', -1);
        if ($chunkIndex < 0) {
            return $app->json(array(
                'success' => false,
                'message' => 'チャンク番号が不正です。',
            ), 400);
        }

        $chunk = $request->files->get('chunk');
        $rawBody = (string) $request->getContent();
        if (!$chunk instanceof UploadedFile && $rawBody === '') {
            return $app->json(array(
                'success' => false,
                'message' => 'チャンクデータが見つかりません。',
            ), 400);
        }

        try {
            if ($chunk instanceof UploadedFile) {
                $result = $this->appendChunkUploadLocal($uploadId, $chunk, $chunkIndex);
            } else {
                $result = $this->appendChunkUploadBinaryLocal($uploadId, $rawBody, $chunkIndex);
            }
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), 400);
        }

        return $app->json(array(
            'success' => true,
            'progress' => (int) (isset($result['progress']) ? $result['progress'] : 0),
        ));
    }

    public function scannerChunkComplete(Application $app, Request $request, $uploadId)
    {
        $this->bootstrapContext($app);

        try {
            $this->validateFlexibleCsrfToken($app, $request);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.invalid_csrf'),
            ), 400);
        }

        try {
            $saved = $this->completeChunkUploadLocal($uploadId);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), 400);
        }

        return $app->json(array(
            'success' => true,
            'token' => (string) (isset($saved['token']) ? $saved['token'] : ''),
            'name' => (string) (isset($saved['name']) ? $saved['name'] : ''),
            'size' => (int) (isset($saved['size']) ? $saved['size'] : 0),
            'size_label' => (string) (isset($saved['size_label']) ? $saved['size_label'] : ''),
            'updated_at' => (string) (isset($saved['updated_at']) ? $saved['updated_at'] : ''),
        ));
    }

    public function scannerChunkCancel(Application $app, Request $request, $uploadId)
    {
        $this->bootstrapContext($app);

        try {
            $this->validateFlexibleCsrfToken($app, $request);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.invalid_csrf'),
            ), 400);
        }

        $this->cancelChunkUploadLocal($uploadId);

        return $app->json(array(
            'success' => true,
        ));
    }

    public function scannerScanConnectors(Application $app, Request $request)
    {
        $this->bootstrapContext($app);
        $sourceInfo = $this->buildScannerSourceInfo();
        $sourceSelectMode = $this->normalizeSourceSelectMode(
            $this->normalizeString($request->get('source_select_mode', self::SCANNER_SOURCE_MODE_CURRENT), self::SCANNER_SOURCE_MODE_CURRENT),
            self::SCANNER_SOURCE_MODE_CURRENT
        );
        $sourceFileKey = $this->normalizeString($request->get('source_file_key', ''), '');
        $sourceUploadToken = $this->normalizeString($request->get('source_upload_token', ''), '');
        $targetUploadToken = $this->normalizeString($request->get('target_upload_token', ''), '');
        $targetVersion = $this->normalizeString($request->get('target_version', ''), '');

        try {
            if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_CURRENT) {
                $currentBackup = $this->createScannerDefaultSourceBackupLocal();
                $sourceFileKey = isset($currentBackup['key']) ? (string) $currentBackup['key'] : self::SCANNER_DEFAULT_SOURCE_BACKUP_NAME;
                $snapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
            } elseif ($sourceSelectMode === self::SCANNER_SOURCE_MODE_UPLOAD) {
                if ($sourceUploadToken === '') {
                    return $app->json(array('success' => false, 'message' => '移行元バックアップファイルをアップロードしてください。'), 400);
                }
                $snapshot = $this->loadSnapshotFromScannerUploadToken($sourceUploadToken);
            } else {
                if ($sourceFileKey === '') {
                    return $app->json(array('success' => false, 'message' => '移行元バックアップファイルを選択してください。'), 400);
                }
                $snapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
            }
            if (is_array($snapshot)) {
                $sourceInfo = $this->buildScannerInfoFromSnapshot($snapshot, $sourceInfo);
            }
        } catch (\Exception $e) {
            return $app->json(array('success' => false, 'message' => $e->getMessage()), 400);
        }

        try {
            $targetInfo = $this->buildScannerTargetInfoFromUploadToken($targetUploadToken, $targetVersion);
        } catch (\Exception $e) {
            return $app->json(array('success' => false, 'message' => $e->getMessage()), 400);
        }
        $connectorRows = $this->buildConnectorRows($sourceInfo, $targetInfo);
        $connectorTargetCodes = $this->normalizeConnectorTargetCodesFromRequest($request, $connectorRows);
        $connectorRows = $this->applyConnectorSelection($connectorRows, $connectorTargetCodes);
        $connectorRows = $this->applyConnectorScanResults($connectorRows, $connectorTargetCodes, $targetUploadToken, $snapshot);

        return $app->json(array(
            'success' => true,
            'rows' => $connectorRows,
        ));
    }

    public function scannerScanTheme(Application $app, Request $request)
    {
        $this->bootstrapContext($app);
        $sourceInfo = $this->buildScannerSourceInfo();
        $sourceSelectMode = $this->normalizeSourceSelectMode(
            $this->normalizeString($request->get('source_select_mode', self::SCANNER_SOURCE_MODE_CURRENT), self::SCANNER_SOURCE_MODE_CURRENT),
            self::SCANNER_SOURCE_MODE_CURRENT
        );
        $sourceFileKey = $this->normalizeString($request->get('source_file_key', ''), '');
        $sourceUploadToken = $this->normalizeString($request->get('source_upload_token', ''), '');
        $targetUploadToken = $this->normalizeString($request->get('target_upload_token', ''), '');
        $targetVersion = $this->normalizeString($request->get('target_version', ''), '');
        $sourceArchivePath = $this->resolveScannerSourceArchivePathLocal($sourceSelectMode, $sourceFileKey, $sourceUploadToken);

        try {
            if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_CURRENT) {
                $currentBackup = $this->createScannerDefaultSourceBackupLocal();
                $sourceFileKey = isset($currentBackup['key']) ? (string) $currentBackup['key'] : self::SCANNER_DEFAULT_SOURCE_BACKUP_NAME;
                $sourceSnapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
            } elseif ($sourceSelectMode === self::SCANNER_SOURCE_MODE_UPLOAD) {
                if ($sourceUploadToken === '') {
                    return $app->json(array('success' => false, 'message' => '移行元バックアップファイルをアップロードしてください。'), 400);
                }
                $sourceSnapshot = $this->loadSnapshotFromScannerUploadToken($sourceUploadToken);
            } else {
                if ($sourceFileKey === '') {
                    return $app->json(array('success' => false, 'message' => '移行元バックアップファイルを選択してください。'), 400);
                }
                $sourceSnapshot = $this->loadSnapshotFromBackupFileName($sourceFileKey);
            }
            if (is_array($sourceSnapshot)) {
                $sourceInfo = $this->buildScannerInfoFromSnapshot($sourceSnapshot, $sourceInfo);
            }
        } catch (\Exception $e) {
            return $app->json(array('success' => false, 'message' => $e->getMessage()), 400);
        }

        try {
            $targetSnapshot = $this->loadSnapshotFromScannerUploadToken($targetUploadToken);
        } catch (\Exception $e) {
            return $app->json(array('success' => false, 'message' => $e->getMessage()), 400);
        }

        $target = is_array($targetSnapshot) ? $targetSnapshot : array();
        $targetPlugins = $this->normalizeSnapshotPlugins(isset($target['plugins']) ? $target['plugins'] : array());
        $themeMigration = $this->buildThemeMigrationResult(
            true,
            !empty($sourceInfo['backup_theme']),
            isset($sourceInfo['theme_code']) ? (string) $sourceInfo['theme_code'] : '',
            isset($sourceInfo['theme_name']) ? (string) $sourceInfo['theme_name'] : '',
            $sourceArchivePath,
            $targetPlugins,
            is_array($sourceSnapshot) ? (isset($sourceSnapshot['plugins']) && is_array($sourceSnapshot['plugins']) ? $sourceSnapshot['plugins'] : array()) : array(),
            is_array($sourceSnapshot) ? (isset($sourceSnapshot['theme_dependency_scan']) ? $sourceSnapshot['theme_dependency_scan'] : array()) : array()
        );

        return $app->json(array(
            'success' => true,
            'theme_migration' => $themeMigration,
            'target_version' => $targetVersion,
        ));
    }

    public function run(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        try {
            $this->isTokenValid($app);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.invalid_csrf'),
            ), 400);
        }

        @ignore_user_abort(true);
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $tableOptions = $this->precheckService->listTables();
        $themeCode = isset($app['config']['template_code']) ? (string) $app['config']['template_code'] : 'default';
        $formData = $this->buildFormData($request, $tableOptions, $themeCode);

        if (
            !$formData['backup_db']
            && !$formData['backup_plugin']
            && !$formData['backup_assets']
            && !$formData['backup_app_assets']
            && !$formData['backup_theme']
        ) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.select_component'),
            ), 400);
        }

        try {
            $backup = $this->backupBuilderService->createBackup($formData);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.failed'),
                'detail' => $e->getMessage(),
            ), 500);
        }

        $downloads = array();
        if (!empty($backup['download_files']) && is_array($backup['download_files'])) {
            foreach ($backup['download_files'] as $file) {
                if (!is_array($file) || empty($file['name'])) {
                    continue;
                }
                $downloads[] = array(
                    'name' => (string) $file['name'],
                    'size' => isset($file['size']) ? (int) $file['size'] : 0,
                    'kind' => isset($file['kind']) ? (string) $file['kind'] : 'backup',
                    'destination' => isset($file['destination']) ? (string) $file['destination'] : '',
                    'download_url' => $this->buildDownloadUrl($app, (string) $file['name']),
                );
            }
        }

        return $app->json(array(
            'success' => true,
            'download_url' => $this->buildDownloadUrl($app, (string) $backup['name']),
            'name' => (string) $backup['name'],
            'size' => isset($backup['size']) ? (int) $backup['size'] : 0,
            'backup_profile' => isset($backup['backup_profile']) ? (string) $backup['backup_profile'] : '',
            'downloads' => $downloads,
        ));
    }

    public function precheck(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        try {
            $this->isTokenValid($app);
        } catch (\Exception $e) {
            return $app->json(array(
                'success' => false,
                'message' => $app->trans('data_migration_backup.admin.message.invalid_csrf'),
            ), 400);
        }

        $tableOptions = $this->precheckService->listTables();
        $themeCode = isset($app['config']['template_code']) ? (string) $app['config']['template_code'] : 'default';
        $formData = $this->buildFormData($request, $tableOptions, $themeCode);
        $checks = $this->precheckService->collectChecks($formData);

        return $app->json(array(
            'success' => true,
            'precheck' => $checks,
        ));
    }

    public function downloadByName(Application $app, $name)
    {
        $this->bootstrapContext($app);

        return $this->createDownloadResponse($app, is_scalar($name) ? (string) $name : '');
    }

    public function downloadQuery(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        return $this->createDownloadResponse($app, $this->normalizeString($request->query->get('name', ''), ''));
    }

    private function createDownloadResponse(Application $app, $requestedName)
    {
        $path = null;
        foreach ($this->buildDownloadNameCandidates($requestedName) as $candidate) {
            $path = $this->backupBuilderService->resolveBackupFile($candidate);
            if ($path !== null) {
                $requestedName = $candidate;
                break;
            }
        }
        if ($path === null) {
            throw new NotFoundHttpException();
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            throw new NotFoundHttpException();
        }

        $response = new Response($content);
        $downloadName = basename($requestedName);
        if ($downloadName === '' || $downloadName !== basename($path)) {
            $downloadName = basename($path);
        }

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadName, $downloadName);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Length', (string) filesize($path));
        $response->headers->set('Content-Encoding', 'identity');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Cache-Control', 'private, no-transform');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    private function buildDownloadUrl(Application $app, $name)
    {
        return $app->url('data_migration_backup_admin_download_query').'?name='.rawurlencode((string) $name);
    }

    private function buildDownloadNameCandidates($rawName)
    {
        $seed = trim((string) $rawName);
        if ($seed === '') {
            return array();
        }

        $candidates = array($seed);
        $decodedRaw = rawurldecode($seed);
        if ($decodedRaw !== $seed) {
            $candidates[] = $decodedRaw;
        }

        $decodedUrl = urldecode($seed);
        if ($decodedUrl !== $seed) {
            $candidates[] = $decodedUrl;
        }

        $expanded = array();
        foreach ($candidates as $candidate) {
            $expanded[$candidate] = true;
            $rawAgain = rawurldecode($candidate);
            if ($rawAgain !== $candidate) {
                $expanded[$rawAgain] = true;
            }
            $urlAgain = urldecode($candidate);
            if ($urlAgain !== $candidate) {
                $expanded[$urlAgain] = true;
            }
        }

        return array_keys($expanded);
    }

    public function delete(Application $app, Request $request, $name)
    {
        $this->bootstrapContext($app);

        try {
            $this->isTokenValid($app);
        } catch (\Exception $e) {
            $app->addError($app->trans('data_migration_backup.admin.message.invalid_csrf'), 'admin');

            return $app->redirect($app->url('data_migration_backup_admin_files'));
        }

        if ($this->backupBuilderService->deleteBackupFile($name)) {
            $app->addSuccess($app->trans('data_migration_backup.admin.message.delete_done'), 'admin');
        } else {
            $app->addError($app->trans('data_migration_backup.admin.message.delete_failed'), 'admin');
        }

        return $app->redirect($app->url('data_migration_backup_admin_files'));
    }

    public function restore(Application $app, Request $request, $name)
    {
        $this->bootstrapContext($app);

        try {
            $this->isTokenValid($app);
        } catch (\Exception $e) {
            $app->addError($app->trans('data_migration_backup.admin.message.invalid_csrf'), 'admin');

            return $app->redirect($app->url('data_migration_backup_admin_files'));
        }

        try {
            $fileInfo = $this->findBackupFileInfoByNameLocal($name);
            if (!is_array($fileInfo) || empty($fileInfo['restore_available']) || empty($fileInfo['path'])) {
                throw new \RuntimeException($app->trans('data_migration_backup.admin.message.restore_unavailable'));
            }

            $this->restoreDatabaseFromArchivePathLocal((string) $fileInfo['path']);
            $app->addSuccess($app->trans('data_migration_backup.admin.message.restore_done'), 'admin');
        } catch (\Exception $e) {
            $message = trim($app->trans('data_migration_backup.admin.message.restore_failed').' '.$e->getMessage());
            $app->addError($message, 'admin');
        }

        return $app->redirect($app->url('data_migration_backup_admin_files'));
    }

    public function deleteAll(Application $app, Request $request)
    {
        $this->bootstrapContext($app);

        try {
            $this->isTokenValid($app);
        } catch (\Exception $e) {
            $app->addError($app->trans('data_migration_backup.admin.message.invalid_csrf'), 'admin');

            return $app->redirect($app->url('data_migration_backup_admin_files'));
        }

        $total = count($this->backupBuilderService->listBackupFiles());
        if ($total === 0) {
            return $app->redirect($app->url('data_migration_backup_admin_files'));
        }

        $deleted = $this->backupBuilderService->deleteAllBackupFiles();
        if ($deleted === $total) {
            $app->addSuccess($app->trans('data_migration_backup.admin.message.delete_all_done'), 'admin');
        } else {
            $app->addError($app->trans('data_migration_backup.admin.message.delete_all_failed'), 'admin');
        }

        return $app->redirect($app->url('data_migration_backup_admin_files'));
    }

    private function bootstrapContext(Application $app)
    {
        $this->app = $app;
        $this->precheckService = $app['eccube.plugin.data_migration_backup.service.precheck'];
        $this->backupBuilderService = $app['eccube.plugin.data_migration_backup.service.builder'];
    }

    private function withToken($parameters)
    {
        if (!is_array($parameters)) {
            $parameters = array();
        }

        $parameters['token_name'] = Constant::TOKEN_NAME;
        $parameters['token_value'] = $this->app['form.csrf_provider']->getToken(Constant::TOKEN_NAME)->getValue();
        $parameters['demo_backup_mode'] = $this->isDemoBackupModeEnabledLocal();
        $parameters['demo_backup'] = $this->getDemoBackupConfigLocal();

        return $parameters;
    }

    private function isDemoBackupModeEnabledLocal()
    {
        if (!isset($this->app['demo_backup.policy'])) {
            return false;
        }

        return $this->app['demo_backup.policy']->shouldRestrictCurrentUser($this->app);
    }

    private function getDemoBackupConfigLocal()
    {
        if (!isset($this->app['demo_backup'])) {
            return array();
        }

        $config = $this->app['demo_backup'];
        if (!is_array($config)) {
            return array();
        }

        return $config;
    }

    private function findBackupFileInfoByNameLocal($name)
    {
        $safeName = basename((string) $name);
        if ($safeName === '') {
            return null;
        }

        foreach ($this->backupBuilderService->listBackupFiles() as $fileInfo) {
            if (!is_array($fileInfo) || empty($fileInfo['name'])) {
                continue;
            }
            if ((string) $fileInfo['name'] === $safeName) {
                return $fileInfo;
            }
        }

        return null;
    }

    private function restoreDatabaseFromArchivePathLocal($archivePath)
    {
        @set_time_limit(0);

        $path = trim((string) $archivePath);
        if ($path === '' || !is_file($path)) {
            throw new \RuntimeException('復元対象のバックアップファイルが見つかりません。');
        }

        $tmpRoot = sys_get_temp_dir().'/dmb_restore_'.sha1($path.'|'.microtime(true).mt_rand(1000, 999999));
        $extractDir = $tmpRoot.'/extract';
        @mkdir($extractDir, 0777, true);

        try {
            $lower = strtolower($path);
            if ($this->endsWith($lower, '.zip')) {
                $this->extractZipArchiveLocal($path, $extractDir);
            } else {
                $this->extractTarGzArchiveLocal($path, $extractDir);
            }

            $sqlPath = $this->findFileRecursiveLocal($extractDir, 'database.sql');
            $databaseSchemaPath = $this->findFileRecursiveLocal($extractDir, 'databases.json');
            $precheckPath = $this->findFileRecursiveLocal($extractDir, 'precheck.json');

            if ($sqlPath === null || !is_file($sqlPath)) {
                throw new \RuntimeException('バックアップ内に database.sql がありません。');
            }
            if ($databaseSchemaPath === null || !is_file($databaseSchemaPath)) {
                throw new \RuntimeException('バックアップ内に databases.json がありません。');
            }

            $databaseSchema = $this->parseJsonFileLocal($databaseSchemaPath);
            $precheck = $this->parseJsonFileLocal($precheckPath);
            $facts = is_array($precheck) && isset($precheck['facts']) && is_array($precheck['facts']) ? $precheck['facts'] : array();
            $databaseTables = $this->resolveSnapshotDatabaseTablesLocal($precheck, $facts, $databaseSchema);
            $tablesToRestore = array_keys($databaseTables);
            if (empty($tablesToRestore)) {
                throw new \RuntimeException('復元対象のテーブル情報を取得できません。');
            }

            $conn = $this->app['orm.em']->getConnection();
            $currentTables = $this->listCurrentDatabaseTablesLocal($conn);
            $missingTables = array();
            foreach ($tablesToRestore as $tableName) {
                $normalizedTable = $this->normalizeSqlTableNameLocal($tableName);
                if ($normalizedTable === '') {
                    continue;
                }
                if (!isset($currentTables[$normalizedTable])) {
                    $missingTables[] = $normalizedTable;
                }
            }
            if (!empty($missingTables)) {
                throw new \RuntimeException('現在のデータベースに存在しないテーブルがあります: '.implode(', ', array_slice($missingTables, 0, 10)));
            }

            $fkDisabled = false;
            try {
                $this->setForeignKeyChecksLocal($conn, false);
                $fkDisabled = true;
                $conn->beginTransaction();
                $this->clearTablesForRestoreLocal($conn, $tablesToRestore);
                $targetMap = array();
                foreach ($tablesToRestore as $tableName) {
                    $normalizedTable = $this->normalizeSqlTableNameLocal($tableName);
                    if ($normalizedTable !== '') {
                        $targetMap[$normalizedTable] = true;
                    }
                }
                $this->iterateSqlInsertRowsLocal($sqlPath, $targetMap, function ($table, $row) use ($conn) {
                    $conn->insert($table, $row);
                });
                $conn->commit();
            } catch (\Exception $e) {
                if ($conn->isTransactionActive()) {
                    $conn->rollBack();
                }
                throw $e;
            } finally {
                if ($fkDisabled) {
                    $this->setForeignKeyChecksLocal($conn, true);
                }
            }
        } finally {
            $this->removePathRecursive($tmpRoot);
        }
    }

    private function listCurrentDatabaseTablesLocal($conn)
    {
        $rows = array();
        try {
            $schemaManager = method_exists($conn, 'createSchemaManager') ? $conn->createSchemaManager() : $conn->getSchemaManager();
            $tables = $schemaManager->listTableNames();
        } catch (\Exception $e) {
            return $rows;
        }

        foreach ($tables as $tableName) {
            $normalized = $this->normalizeSqlTableNameLocal((string) $tableName);
            if ($normalized !== '') {
                $rows[$normalized] = true;
            }
        }

        return $rows;
    }

    private function clearTablesForRestoreLocal($conn, $tables)
    {
        $platform = strtolower((string) $conn->getDatabasePlatform()->getName());
        foreach ($tables as $tableName) {
            $normalized = $this->normalizeSqlTableNameLocal($tableName);
            if ($normalized === '') {
                continue;
            }
            $quotedTable = $conn->getDatabasePlatform()->quoteIdentifier($normalized);
            $conn->executeUpdate('DELETE FROM '.$quotedTable);
        }

        if ($platform === 'sqlite') {
            foreach ($tables as $tableName) {
                $normalized = $this->normalizeSqlTableNameLocal($tableName);
                if ($normalized === '') {
                    continue;
                }
                try {
                    $conn->executeUpdate('DELETE FROM sqlite_sequence WHERE name = ?', array($normalized));
                } catch (\Exception $e) {
                }
            }
            try {
                $conn->executeUpdate('DELETE FROM sqlite_sequence WHERE name IS NULL');
            } catch (\Exception $e) {
            }
        }
    }

    private function setForeignKeyChecksLocal($conn, $enabled)
    {
        $platform = strtolower((string) $conn->getDatabasePlatform()->getName());
        if ($platform === 'sqlite') {
            $conn->executeUpdate('PRAGMA foreign_keys = '.($enabled ? 'ON' : 'OFF'));
            return;
        }
        if ($platform === 'mysql') {
            $conn->executeUpdate('SET FOREIGN_KEY_CHECKS = '.($enabled ? '1' : '0'));
            return;
        }
        if ($platform === 'postgresql') {
            $conn->executeUpdate(sprintf("SET session_replication_role = '%s'", $enabled ? 'origin' : 'replica'));
        }
    }

    private function buildFormData(Request $request, $tableOptions, $themeCode)
    {
        $defaults = array(
            'backup_profile' => 'migrate_default',
            'backup_db' => true,
            'db_scope' => 'all',
            'db_tables' => $tableOptions,
            'backup_plugin' => false,
            'backup_assets' => true,
            'backup_app_assets' => true,
            'backup_theme' => true,
            'theme_code' => $themeCode,
        );

        if (!$request->isMethod('POST')) {
            return $defaults;
        }

        $profile = $this->normalizeString($request->get('backup_profile', 'migrate_default'), 'migrate_default');
        if (!in_array($profile, array('migrate_default', 'custom'), true)) {
            $profile = 'migrate_default';
        }

        if ($profile === 'migrate_default') {
            return $defaults;
        }

        $scope = $this->normalizeString($request->get('db_scope', 'all'), 'all');
        if (!in_array($scope, array('all', 'selected'), true)) {
            $scope = 'all';
        }

        $selectedTables = $this->normalizeSelectedTables($request->get('db_tables', array()), $tableOptions);

        return array(
            'backup_profile' => 'custom',
            'backup_db' => $request->request->has('backup_db'),
            'db_scope' => $scope,
            'db_tables' => $scope === 'all' ? $tableOptions : $selectedTables,
            'backup_plugin' => $request->request->has('backup_plugin'),
            'backup_assets' => $request->request->has('backup_assets'),
            'backup_app_assets' => $request->request->has('backup_app_assets'),
            'backup_theme' => $request->request->has('backup_theme'),
            'theme_code' => $themeCode,
        );
    }

    private function normalizeSelectedTables($selected, $tableOptions)
    {
        if (!is_array($selected)) {
            return array();
        }

        $selected = array_values(array_unique(array_map('strval', $selected)));
        if (empty($selected)) {
            return array();
        }

        return array_values(array_filter($tableOptions, function ($table) use ($selected) {
            return in_array($table, $selected, true);
        }));
    }

    private function normalizeString($value, $default)
    {
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $value = trim((string) $value);
            if ($value !== '') {
                return $value;
            }
        }

        return $default;
    }

    private function normalizeBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            return false;
        }
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, array('1', 'true', 'on', 'yes'), true);
    }

    private function normalizeSourceSelectMode($mode, $default)
    {
        $mode = trim((string) $mode);
        if (!in_array($mode, array(self::SCANNER_SOURCE_MODE_CURRENT, self::SCANNER_SOURCE_MODE_SAVED, self::SCANNER_SOURCE_MODE_UPLOAD), true)) {
            return $default;
        }

        return $mode;
    }

    private function normalizeTargetFileMode($mode, $default)
    {
        $mode = trim((string) $mode);
        if ($mode !== 'upload' && $mode !== 'default') {
            return $default;
        }

        return $mode;
    }

    private function buildScannerSourceInfo()
    {
        $themeCode = isset($this->app['config']['template_code']) ? (string) $this->app['config']['template_code'] : 'default';

        return array(
            'eccube_version' => Constant::VERSION,
            'php_version' => PHP_VERSION,
            'generated_at' => date('Y-m-d H:i:s'),
            'database_platform' => $this->backupBuilderService->getCurrentDatabasePlatformName(),
            'backup_theme' => false,
            'theme_code' => $themeCode,
            'theme_name' => $themeCode,
            'plugins' => $this->backupBuilderService->getSourcePluginsSnapshot(false),
            'options' => array(),
        );
    }

    private function buildSourceFileOptions()
    {
        $rows = array();
        foreach ($this->backupBuilderService->listBackupFiles() as $file) {
            if (!is_array($file) || empty($file['name'])) {
                continue;
            }
            $name = (string) $file['name'];
            if (strpos($name, 'data_migration_backup_') !== 0) {
                continue;
            }
            $typeLabel = isset($file['type_label']) ? trim((string) $file['type_label']) : '';
            $modifiedAt = '';
            if (isset($file['modified_at']) && $file['modified_at'] instanceof \DateTimeInterface) {
                $modifiedAt = $file['modified_at']->format('Y-m-d H:i:s');
            }
            $size = isset($file['size']) ? (int) $file['size'] : 0;
            $rows[] = array(
                'key' => $name,
                'name' => $name,
                'size' => $size,
                'size_label' => $this->formatFileSize($size),
                'updated_at' => $modifiedAt,
                'type_label' => $typeLabel !== '' ? $typeLabel : 'バックアップ',
            );
        }

        return $rows;
    }

    private function createScannerDefaultSourceBackupLocal()
    {
        $themeCode = isset($this->app['config']['template_code']) ? (string) $this->app['config']['template_code'] : 'default';
        $options = array(
            'backup_profile' => 'migrate_default',
            'backup_db' => true,
            'db_scope' => 'all',
            'db_tables' => $this->precheckService->listTables(),
            'backup_plugin' => false,
            'backup_assets' => false,
            'backup_app_assets' => false,
            'backup_theme' => true,
            'theme_code' => $themeCode,
            'fixed_backup_name' => self::SCANNER_DEFAULT_SOURCE_BACKUP_NAME,
        );

        $backup = $this->backupBuilderService->createBackup($options);
        $name = !empty($backup['name']) ? (string) $backup['name'] : self::SCANNER_DEFAULT_SOURCE_BACKUP_NAME;
        $info = $this->buildSourceFileOptionFromBackupName($name);
        if (!is_array($info)) {
            throw new \RuntimeException('現在のシステムから移行元バックアップファイルを作成できませんでした。');
        }

        return $info;
    }

    private function findSourceFileOption($options, $key)
    {
        foreach ($options as $option) {
            if (is_array($option) && isset($option['key']) && (string) $option['key'] === (string) $key) {
                return $option;
            }
        }

        return null;
    }

    private function buildSourceFileOptionFromBackupName($backupName)
    {
        $name = trim((string) $backupName);
        if ($name === '') {
            return null;
        }

        $path = $this->backupBuilderService->resolveBackupFile($name);
        if (!is_string($path) || $path === '' || !is_file($path)) {
            return null;
        }

        $size = (int) @filesize($path);
        $updatedAt = '';
        try {
            $updatedAt = (new \DateTime('@'.(string) @filemtime($path)))->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $updatedAt = '';
        }

        return array(
            'key' => $name,
            'name' => $name,
            'size' => $size,
            'size_label' => $this->formatFileSize($size),
            'updated_at' => $updatedAt,
            'type_label' => 'バックアップ',
        );
    }

    private function getTargetVersionOptions()
    {
        return array(
            array('value' => '4.3', 'label' => 'EC-CUBE 4.3'),
            array('value' => '4.2', 'label' => 'EC-CUBE 4.2'),
        );
    }

    private function buildScannerTargetInfo($targetVersion)
    {
        $fallback = array(
            'eccube_version' => $targetVersion !== '' ? $targetVersion : '-',
            'php_version' => '-',
            'generated_at' => '',
            'database_platform' => $this->backupBuilderService->getCurrentDatabasePlatformName(),
            'backup_theme' => false,
            'theme_code' => '',
            'theme_name' => '',
            'plugins' => array(),
            'options' => array(),
        );

        try {
            $snapshot = $this->loadSnapshotFromTargetVersion($targetVersion);
            if (is_array($snapshot)) {
                return $this->buildScannerInfoFromSnapshot($snapshot, $fallback);
            }
        } catch (\Exception $e) {
        }

        return $fallback;
    }

    private function buildScannerTargetInfoFromUploadToken($targetUploadToken, $targetVersion)
    {
        $fallback = $this->buildScannerTargetInfo($targetVersion);
        $token = trim((string) $targetUploadToken);
        if ($token === '') {
            return $fallback;
        }

        $this->assertSelectedTargetVersionMatchesUploadToken($token, $targetVersion);
        $snapshot = $this->loadSnapshotFromScannerUploadToken($token);
        if (is_array($snapshot)) {
            return $this->buildScannerInfoFromSnapshot($snapshot, $fallback);
        }

        return $fallback;
    }

    private function assertSelectedTargetVersionMatchesUploadToken($token, $selectedTargetVersion)
    {
        $selected = trim((string) $selectedTargetVersion);
        if ($selected === '') {
            return;
        }

        $snapshot = $this->loadSnapshotFromScannerUploadToken($token);
        $actual = trim((string) (is_array($snapshot) && isset($snapshot['eccube_version']) ? $snapshot['eccube_version'] : ''));
        $result = $this->compareSelectedTargetVersionLocal($selected, $actual);
        if (empty($result['compatible'])) {
            throw new \RuntimeException((string) (isset($result['message']) ? $result['message'] : '移行先バージョンを判定できません。'));
        }
    }

    private function parseMajorMinorLocal($version)
    {
        if (preg_match('/^(\\d+)\\.(\\d+)/', trim((string) $version), $matches) !== 1) {
            return null;
        }

        return array(
            'major' => (int) $matches[1],
            'minor' => (int) $matches[2],
        );
    }

    private function compareSelectedTargetVersionLocal($selectedTargetVersion, $actualTargetVersion)
    {
        $selected = $this->parseMajorMinorLocal($selectedTargetVersion);
        $actual = $this->parseMajorMinorLocal($actualTargetVersion);
        if (!is_array($selected) || !is_array($actual)) {
            return array(
                'compatible' => false,
                'message' => '選択した移行先バージョンとアップロードファイルのバージョンを判定できません。',
            );
        }

        $sameMinor = $selected['major'] === $actual['major'] && $selected['minor'] === $actual['minor'];
        if (!$sameMinor) {
            return array(
                'compatible' => false,
                'message' => sprintf(
                    '選択した移行先バージョン(%s)とアップロードファイルのバージョン(%s)が一致しません。',
                    $selectedTargetVersion,
                    $actualTargetVersion
                ),
            );
        }

        return array(
            'compatible' => true,
            'message' => sprintf(
                '移行先バージョン確認: 選択 %s / ファイル %s',
                $selectedTargetVersion,
                $actualTargetVersion
            ),
        );
    }

    private function buildDefaultScannerOptions($moduleDefinitions = null)
    {
        if (!is_array($moduleDefinitions) || empty($moduleDefinitions)) {
            $moduleDefinitions = $this->getScannerModuleDefinitions();
        }
        $defaultModules = array();
        foreach ($moduleDefinitions as $moduleKey => $definition) {
            if (!is_array($definition) || empty($definition['tables'])) {
                continue;
            }
            if (!empty($definition['default_checked'])) {
                $defaultModules[] = (string) $moduleKey;
            }
        }

        return array(
            'clear_target_before_import' => true,
            'disable_foreign_key_checks' => true,
            'use_custom_mapping' => true,
            'conflict_policy' => 'overwrite',
            'modules' => $defaultModules,
            'migrate_theme' => false,
        );
    }

    private function normalizeScannerOptionsFromRequest(Request $request, $defaults, $moduleDefinitions = null)
    {
        $moduleMap = is_array($moduleDefinitions) && !empty($moduleDefinitions)
            ? $moduleDefinitions
            : $this->getScannerModuleDefinitions();
        $modules = $request->get('modules', null);
        if (!is_array($modules)) {
            if ($request->request->has('modules_present')) {
                $modules = array();
            } else {
                $modules = $defaults['modules'];
            }
        }
        $normalizedModules = array();
        foreach ((array) $modules as $moduleKey) {
            $normalizedKey = $this->normalizeScannerModuleKeyLocal($moduleKey);
            if ($normalizedKey !== '' && isset($moduleMap[$normalizedKey])) {
                $normalizedModules[] = $normalizedKey;
            }
        }
        $modules = array_values(array_unique($normalizedModules));

        return array(
            'clear_target_before_import' => $defaults['clear_target_before_import'],
            'disable_foreign_key_checks' => $defaults['disable_foreign_key_checks'],
            'use_custom_mapping' => $defaults['use_custom_mapping'],
            'conflict_policy' => $defaults['conflict_policy'],
            'modules' => $modules,
            'migrate_theme' => $request->request->has('migrate_theme'),
        );
    }

    private function appendHiddenScannerModulesLocal(array $modules, array $moduleDefinitions)
    {
        $normalized = array_values(array_unique(array_filter(array_map('strval', $modules), static function ($moduleKey) {
            return trim((string) $moduleKey) !== '';
        })));

        foreach ($moduleDefinitions as $moduleKey => $definition) {
            $section = is_array($definition) && isset($definition['section'])
                ? (string) $definition['section']
                : 'migration';
            if (
                !is_array($definition)
                || $section !== 'master'
                || empty($definition['tables'])
            ) {
                continue;
            }
            $normalized[] = (string) $moduleKey;
        }

        return array_values(array_unique($normalized));
    }

    private function countVisibleScannerModulesLocal(array $modules, array $moduleDefinitions)
    {
        $count = 0;
        foreach ($modules as $moduleKey) {
            $normalizedKey = trim((string) $moduleKey);
            if ($normalizedKey === '' || !isset($moduleDefinitions[$normalizedKey])) {
                continue;
            }
            $definition = $moduleDefinitions[$normalizedKey];
            $section = is_array($definition) && isset($definition['section'])
                ? (string) $definition['section']
                : 'migration';
            if (!is_array($definition) || $section === 'master') {
                continue;
            }
            ++$count;
        }

        return $count;
    }

    private function buildConnectorRows($sourceInfo, $targetInfo)
    {
        $rows = array();
        $targetPlugins = isset($targetInfo['plugins']) && is_array($targetInfo['plugins']) ? $targetInfo['plugins'] : array();
        $targetOptions = $this->buildTargetPluginOptions($targetPlugins);
        foreach ($sourceInfo['plugins'] as $plugin) {
            if (!is_array($plugin) || empty($plugin['enabled'])) {
                continue;
            }
            $sourceCode = isset($plugin['code']) ? trim((string) $plugin['code']) : '';
            if ($sourceCode === '' || $this->isInternalMigrationPluginCode($sourceCode)) {
                continue;
            }
            $matchedTargetCode = $this->findMatchedTargetPluginCode($sourceCode, $targetOptions);
            $rows[] = array(
                'source_code' => $sourceCode,
                'source_name' => isset($plugin['name']) ? (string) $plugin['name'] : '',
                'source_version' => isset($plugin['version']) ? (string) $plugin['version'] : '',
                'target_code' => $matchedTargetCode,
                'target_options' => $targetOptions,
                'status' => $matchedTargetCode !== '' ? 'supported' : 'pending',
                'note' => '',
            );
        }

        return $rows;
    }

    private function buildTargetPluginOptions($plugins)
    {
        $rows = array();
        foreach ($plugins as $plugin) {
            if (!is_array($plugin) || empty($plugin['enabled']) || empty($plugin['code'])) {
                continue;
            }
            $code = trim((string) $plugin['code']);
            if ($code === '' || $this->isInternalMigrationPluginCode($code)) {
                continue;
            }
            $label = trim((string) $plugin['name']);
            if ($label === '') {
                $label = $code;
            }
            $version = trim(isset($plugin['version']) ? (string) $plugin['version'] : '');
            if ($version !== '') {
                $label .= ' / '.$version;
            }
            $label .= ' ('.$code.')';
            $rows[] = array(
                'code' => $code,
                'label' => $label,
            );
        }

        usort($rows, function ($a, $b) {
            return strcasecmp((string) $a['code'], (string) $b['code']);
        });

        return $rows;
    }

    private function normalizeConnectorTargetCodesFromRequest(Request $request, $connectorRows)
    {
        $map = $request->get('connector_target_codes', array());
        if (!is_array($map)) {
            return array();
        }

        $validSourceCodes = array();
        foreach ($connectorRows as $row) {
            if (is_array($row) && !empty($row['source_code'])) {
                $validSourceCodes[(string) $row['source_code']] = true;
            }
        }
        $shouldValidateSourceCodes = !empty($validSourceCodes);

        $rows = array();
        foreach ($map as $sourceCode => $targetCode) {
            $sourceCode = trim((string) $sourceCode);
            $targetCode = trim((string) $targetCode);
            if ($sourceCode === '') {
                continue;
            }
            if ($shouldValidateSourceCodes && !isset($validSourceCodes[$sourceCode])) {
                continue;
            }
            $rows[$sourceCode] = $targetCode;
        }

        return $rows;
    }

    private function applyConnectorSelection($connectorRows, $connectorTargetCodes)
    {
        foreach ($connectorRows as $index => $row) {
            if (!is_array($row)) {
                continue;
            }
            $sourceCode = isset($row['source_code']) ? (string) $row['source_code'] : '';
            $targetCode = isset($connectorTargetCodes[$sourceCode]) ? (string) $connectorTargetCodes[$sourceCode] : '';
            $targetOptions = isset($row['target_options']) && is_array($row['target_options']) ? $row['target_options'] : array();
            $validTargetCodes = array();
            foreach ($targetOptions as $option) {
                if (!is_array($option) || empty($option['code'])) {
                    continue;
                }
                $validTargetCodes[(string) $option['code']] = true;
            }
            $row['target_code'] = $targetCode;
            if ($targetCode === '') {
                $row['status'] = 'pending';
                $row['note'] = '';
            } elseif (isset($validTargetCodes[$targetCode])) {
                $row['status'] = 'supported';
                $row['note'] = '';
            } else {
                $row['status'] = 'unsupported';
                $row['note'] = '選択した移行先プラグインが見つかりません。';
            }
            $connectorRows[$index] = $row;
        }

        return $connectorRows;
    }

    private function normalizeConnectorStatusMapFromRequest(Request $request, $connectorRows)
    {
        $map = $request->get('connector_statuses', array());
        if (!is_array($map)) {
            return array();
        }

        $allowed = array(
            'pending' => true,
            'supported' => true,
            'unsupported' => true,
        );
        $validSourceCodes = array();
        foreach ($connectorRows as $row) {
            if (is_array($row) && !empty($row['source_code'])) {
                $validSourceCodes[(string) $row['source_code']] = true;
            }
        }

        $rows = array();
        foreach ($map as $sourceCode => $status) {
            $sourceCode = trim((string) $sourceCode);
            $status = trim((string) $status);
            if ($sourceCode === '' || !isset($validSourceCodes[$sourceCode]) || !isset($allowed[$status])) {
                continue;
            }
            $rows[$sourceCode] = $status;
        }

        return $rows;
    }

    private function normalizeConnectorNoteMapFromRequest(Request $request, $connectorRows)
    {
        $map = $request->get('connector_notes', array());
        if (!is_array($map)) {
            return array();
        }

        $validSourceCodes = array();
        foreach ($connectorRows as $row) {
            if (is_array($row) && !empty($row['source_code'])) {
                $validSourceCodes[(string) $row['source_code']] = true;
            }
        }

        $rows = array();
        foreach ($map as $sourceCode => $note) {
            $sourceCode = trim((string) $sourceCode);
            if ($sourceCode === '' || !isset($validSourceCodes[$sourceCode])) {
                continue;
            }
            $rows[$sourceCode] = trim((string) $note);
        }

        return $rows;
    }

    private function applyConnectorStatusOverrides($connectorRows, $connectorStatusMap, $connectorNoteMap)
    {
        foreach ($connectorRows as $index => $row) {
            if (!is_array($row) || empty($row['source_code'])) {
                continue;
            }

            $sourceCode = (string) $row['source_code'];
            if (isset($connectorStatusMap[$sourceCode])) {
                $row['status'] = (string) $connectorStatusMap[$sourceCode];
            }
            if (array_key_exists($sourceCode, $connectorNoteMap)) {
                $row['note'] = (string) $connectorNoteMap[$sourceCode];
            }
            $connectorRows[$index] = $row;
        }

        return $connectorRows;
    }

    private function applyConnectorScanResults($connectorRows, $connectorTargetCodes, $targetUploadToken, $sourceSnapshot)
    {
        $token = trim((string) $targetUploadToken);
        if ($token === '') {
            return $connectorRows;
        }

        $targetSnapshot = $this->loadSnapshotFromScannerUploadToken($token);
        $scanRows = $this->scanConnectorRowsBySnapshot(
            is_array($targetSnapshot) ? $targetSnapshot : array(),
            is_array($connectorTargetCodes) ? $connectorTargetCodes : array(),
            is_array($sourceSnapshot) ? $sourceSnapshot : null
        );
        $scanMap = array();
        foreach ($scanRows as $scanRow) {
            if (!is_array($scanRow) || empty($scanRow['source_code'])) {
                continue;
            }
            $scanMap[(string) $scanRow['source_code']] = $scanRow;
        }

        foreach ($connectorRows as $index => $row) {
            if (!is_array($row) || empty($row['source_code'])) {
                continue;
            }

            $sourceCode = (string) $row['source_code'];
            if (!isset($scanMap[$sourceCode]) || !is_array($scanMap[$sourceCode])) {
                continue;
            }

            $scanRow = $scanMap[$sourceCode];
            if (array_key_exists('target_code', $scanRow)) {
                $row['target_code'] = trim((string) $scanRow['target_code']);
            }
            $row['status'] = trim((string) (isset($scanRow['status']) ? $scanRow['status'] : 'pending'));
            $row['note'] = trim((string) (isset($scanRow['note']) ? $scanRow['note'] : ''));
            $connectorRows[$index] = $row;
        }

        return $connectorRows;
    }

    private function scanConnectorRowsBySnapshot($targetSnapshot, $connectorTargetCodes, $sourceSnapshot)
    {
        $normalizedMap = array();
        foreach ($connectorTargetCodes as $sourceCode => $targetCode) {
            $source = trim((string) $sourceCode);
            $target = trim((string) $targetCode);
            if ($source === '' || $target === '') {
                continue;
            }
            $normalizedMap[$source] = $target;
        }

        $sourcePlugins = $this->normalizeSnapshotPlugins(isset($sourceSnapshot['plugins']) ? $sourceSnapshot['plugins'] : array());
        $targetPlugins = $this->normalizeSnapshotPlugins(isset($targetSnapshot['plugins']) ? $targetSnapshot['plugins'] : array());
        $sourcePluginTables = $this->normalizePluginTableMapLocal(isset($sourceSnapshot['source_plugin_tables']) ? $sourceSnapshot['source_plugin_tables'] : array());
        $targetPluginTables = $this->normalizePluginTableMapLocal(isset($targetSnapshot['source_plugin_tables']) ? $targetSnapshot['source_plugin_tables'] : array());

        $rows = array();
        foreach ($sourcePlugins as $plugin) {
            if (!is_array($plugin) || empty($plugin['enabled'])) {
                continue;
            }

            $sourceCode = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));
            if ($sourceCode === '' || $this->isInternalMigrationPluginCode($sourceCode)) {
                continue;
            }

            $selectedTargetCode = isset($normalizedMap[$sourceCode]) ? trim((string) $normalizedMap[$sourceCode]) : '';
            if ($selectedTargetCode === '') {
                $rows[] = array(
                    'source_code' => $sourceCode,
                    'target_code' => '',
                    'status' => 'pending',
                    'note' => '',
                );
                continue;
            }

            $targetPlugin = $this->findTargetPluginByCodeLocal($targetPlugins, $selectedTargetCode);
            if (!is_array($targetPlugin)) {
                $rows[] = array(
                    'source_code' => $sourceCode,
                    'target_code' => $selectedTargetCode,
                    'status' => 'unsupported',
                    'note' => '選択した移行先プラグインが見つかりません。',
                );
                continue;
            }

            if (empty($targetPlugin['enabled'])) {
                $rows[] = array(
                    'source_code' => $sourceCode,
                    'target_code' => trim((string) (isset($targetPlugin['code']) ? $targetPlugin['code'] : $selectedTargetCode)),
                    'status' => 'unsupported',
                    'note' => '移行先でプラグインが無効です。',
                );
                continue;
            }

            $targetCode = trim((string) (isset($targetPlugin['code']) ? $targetPlugin['code'] : $selectedTargetCode));
            $sourceTables = array();
            if (isset($sourcePluginTables[$sourceCode]['tables']) && is_array($sourcePluginTables[$sourceCode]['tables'])) {
                $sourceTables = $sourcePluginTables[$sourceCode]['tables'];
            }
            $targetTables = array();
            if (isset($targetPluginTables[$targetCode]['tables']) && is_array($targetPluginTables[$targetCode]['tables'])) {
                $targetTables = $targetPluginTables[$targetCode]['tables'];
            }

            $schemaResult = $this->comparePluginSchemaLocal($sourceTables, $targetTables);
            $rows[] = array(
                'source_code' => $sourceCode,
                'target_code' => $targetCode,
                'status' => $schemaResult['status'] === 'ok' ? 'supported' : 'unsupported',
                'note' => $schemaResult['status'] === 'ok' ? '' : (string) $schemaResult['note'],
            );
        }

        return $rows;
    }

    private function normalizePluginTableMapLocal($map)
    {
        $rows = array();
        if (!is_array($map)) {
            return $rows;
        }

        foreach ($map as $pluginCode => $pluginDef) {
            if (!is_string($pluginCode) || !is_array($pluginDef)) {
                continue;
            }

            $tables = isset($pluginDef['tables']) && is_array($pluginDef['tables']) ? $pluginDef['tables'] : array();
            $normalizedTables = array();
            foreach ($tables as $tableName => $tableDef) {
                if (!is_string($tableName) || !is_array($tableDef)) {
                    continue;
                }

                $columns = isset($tableDef['columns']) && is_array($tableDef['columns']) ? $tableDef['columns'] : array();
                $normalizedColumns = array();
                foreach ($columns as $columnName => $columnDef) {
                    if (!is_string($columnName)) {
                        continue;
                    }

                    $type = '';
                    if (is_array($columnDef) && isset($columnDef['type'])) {
                        $type = (string) $columnDef['type'];
                    }
                    $normalizedColumns[strtolower($columnName)] = array('type' => $type);
                }

                if (!empty($normalizedColumns)) {
                    $normalizedTables[strtolower($tableName)] = array('columns' => $normalizedColumns);
                }
            }

            $code = trim((string) $pluginCode);
            if ($code !== '' && !empty($normalizedTables)) {
                $rows[$code] = array('tables' => $normalizedTables);
            }
        }

        return $rows;
    }

    private function findTargetPluginByCodeLocal($targetPlugins, $targetCode)
    {
        $requested = trim((string) $targetCode);
        if ($requested === '') {
            return null;
        }

        foreach ($targetPlugins as $plugin) {
            if (!is_array($plugin)) {
                continue;
            }
            $code = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));
            if ($code !== '' && strcasecmp($code, $requested) === 0) {
                return $plugin;
            }
        }

        return null;
    }

    private function comparePluginSchemaLocal($sourceTables, $targetTables)
    {
        if (empty($sourceTables)) {
            return array(
                'status' => 'error',
                'note' => '移行元テーブル情報を取得できません。',
            );
        }
        if (empty($targetTables)) {
            return array(
                'status' => 'error',
                'note' => '移行先プラグインのデータベーステーブル情報を取得できません。',
            );
        }

        $sourceTableNames = array_keys($sourceTables);
        $targetTableNames = array_keys($targetTables);
        $commonTables = array_values(array_intersect($sourceTableNames, $targetTableNames));
        if (empty($commonTables)) {
            return array(
                'status' => 'error',
                'note' => '共通のプラグインテーブルが見つかりません。',
            );
        }

        foreach ($commonTables as $tableName) {
            $sourceColumns = array();
            if (isset($sourceTables[$tableName]['columns']) && is_array($sourceTables[$tableName]['columns'])) {
                $sourceColumns = array_keys($sourceTables[$tableName]['columns']);
            }
            $targetColumns = array();
            if (isset($targetTables[$tableName]['columns']) && is_array($targetTables[$tableName]['columns'])) {
                $targetColumns = array_keys($targetTables[$tableName]['columns']);
            }
            if (empty($sourceColumns) || empty($targetColumns)) {
                continue;
            }

            $commonColumns = array_intersect($sourceColumns, $targetColumns);
            if (empty($commonColumns)) {
                return array(
                    'status' => 'error',
                    'note' => sprintf('%s のカラム構成が一致しません。', $tableName),
                );
            }
        }

        $missingTargetTables = array_values(array_diff($sourceTableNames, $targetTableNames));
        if (!empty($missingTargetTables)) {
            return array(
                'status' => 'warning',
                'note' => sprintf('移行先に存在しないテーブルがあります: %s', implode(', ', $missingTargetTables)),
            );
        }

        return array(
            'status' => 'ok',
            'note' => '互換性を確認しました。',
        );
    }

    private function buildScanResult($sourceSnapshot, $sourceArchivePath, $targetUploadToken, $targetVersion, $scannerOptions, $pluginConnectorEnabled, $connectorRows)
    {
        $source = is_array($sourceSnapshot) ? $sourceSnapshot : array();
        $targetSnapshot = $this->loadSnapshotFromScannerUploadToken($targetUploadToken);
        $target = is_array($targetSnapshot) ? $targetSnapshot : array();

        $sourceVersion = trim((string) $this->resolveSnapshotVersionForModulesLocal($source));
        $sourcePhp = isset($source['php_version']) ? trim((string) $source['php_version']) : '';
        $sourceDb = isset($source['database_platform']) ? trim((string) $source['database_platform']) : '';
        $sourceBackupTheme = !empty($source['backup_theme']);
        $sourceThemeCode = isset($source['theme_code']) ? trim((string) $source['theme_code']) : '';
        $sourceThemeName = isset($source['theme_name']) ? trim((string) $source['theme_name']) : '';

        $targetVersionActual = trim((string) $this->resolveSnapshotVersionForModulesLocal($target));
        $targetPhp = isset($target['php_version']) ? trim((string) $target['php_version']) : '';
        $targetDb = isset($target['database_platform']) ? trim((string) $target['database_platform']) : '';
        $targetGeneratedAt = isset($target['generated_at']) ? trim((string) $target['generated_at']) : '';
        $targetPlugins = $this->normalizeSnapshotPlugins(isset($target['plugins']) ? $target['plugins'] : array());
        $moduleMap = $this->buildScannerModuleDefinitionsForContext($source, $target);

        $selectedModules = $this->normalizeSelectedScannerModulesLocal(
            isset($scannerOptions['modules']) ? $scannerOptions['modules'] : array(),
            $moduleMap
        );
        $versionResult = $this->compareVersionCompatibilityLocal($sourceVersion, $targetVersionActual);
        $selectedTargetVersionResult = $targetVersion !== '' ? $this->compareSelectedTargetVersionLocal($targetVersion, $targetVersionActual) : null;
        $selectedSourceCodes = array();

        $pluginResults = array();
        $hasFatalError = empty($versionResult['compatible']);
        if (is_array($selectedTargetVersionResult) && empty($selectedTargetVersionResult['compatible'])) {
            $hasFatalError = true;
        }
        $hasWarning = false;
        $hasPluginSkip = false;
        $skippedPluginCount = 0;
        $migratablePluginCount = 0;
        $warningPluginCount = 0;

        if ($pluginConnectorEnabled) {
            foreach ($connectorRows as $row) {
                if (!is_array($row) || empty($row['source_code'])) {
                    continue;
                }

                $sourceCode = trim((string) $row['source_code']);
                $targetCode = trim((string) (isset($row['target_code']) ? $row['target_code'] : ''));
                if ($targetCode === '') {
                    continue;
                }

                $selectedSourceCodes[$sourceCode] = true;
                $status = trim((string) (isset($row['status']) ? $row['status'] : 'pending'));
                $note = trim((string) (isset($row['note']) ? $row['note'] : ''));
                $targetPlugin = $this->findTargetPluginByCodeLocal($targetPlugins, $targetCode);
                $targetName = is_array($targetPlugin) && isset($targetPlugin['name']) ? trim((string) $targetPlugin['name']) : '';
                $targetPluginVersion = is_array($targetPlugin) && isset($targetPlugin['version']) ? trim((string) $targetPlugin['version']) : '';

                $resultStatus = 'ok';
                if ($status === 'unsupported') {
                    if (strpos($note, '無効') !== false) {
                        $resultStatus = 'warning';
                    } else {
                        $resultStatus = 'error';
                    }
                } elseif ($status === 'pending') {
                    $resultStatus = 'warning';
                    if ($note === '') {
                        $note = '移行先プラグインが未選択です。';
                    }
                }

                if ($resultStatus === 'error') {
                    $hasPluginSkip = true;
                    ++$skippedPluginCount;
                } elseif ($resultStatus === 'warning') {
                    $hasWarning = true;
                    ++$warningPluginCount;
                    ++$migratablePluginCount;
                } else {
                    ++$migratablePluginCount;
                }

                $pluginResults[] = array(
                    'source_code' => $sourceCode,
                    'source_name' => isset($row['source_name']) ? trim((string) $row['source_name']) : $sourceCode,
                    'source_version' => isset($row['source_version']) ? trim((string) $row['source_version']) : '',
                    'target_code' => $targetCode,
                    'target_name' => $targetName,
                    'target_version' => $targetPluginVersion,
                    'status' => $resultStatus,
                    'note' => $note,
                );
            }
        }
        $selectedSourceCodes = array_keys($selectedSourceCodes);
        $skipPluginCompatibility = $pluginConnectorEnabled && empty($selectedSourceCodes);

        $tableStats = $this->buildScannerTableStatsLocal(
            $selectedModules,
            isset($source['database_tables']) && is_array($source['database_tables']) ? $source['database_tables'] : array(),
            isset($source['source_plugin_tables']) && is_array($source['source_plugin_tables']) ? $source['source_plugin_tables'] : array(),
            $pluginResults,
            isset($source['sql_counts']) && is_array($source['sql_counts']) ? $source['sql_counts'] : array(),
            $moduleMap
        );
        $fullSqlPreview = $this->loadFullScannerPreviewFromArchivePathLocal($sourceArchivePath, $tableStats);
        $preview = $this->buildScannerPreviewDataLocal(
            $tableStats,
            !empty($fullSqlPreview) ? $fullSqlPreview : (isset($source['sql_preview']) && is_array($source['sql_preview']) ? $source['sql_preview'] : array()),
            isset($source['database_tables']) && is_array($source['database_tables']) ? $source['database_tables'] : array()
        );
        $precheckRows = $this->buildScannerPrecheckRowsLocal(
            $sourceVersion,
            $sourcePhp,
            $sourceDb,
            $targetVersionActual,
            $targetPhp,
            $targetDb,
            $versionResult,
            $selectedTargetVersionResult,
            $selectedSourceCodes,
            $migratablePluginCount,
            $skippedPluginCount
        );

        $themeSelected = !empty($scannerOptions['migrate_theme']);
        $themeMigration = $this->buildThemeMigrationResult(
            $themeSelected,
            $sourceBackupTheme,
            $sourceThemeCode,
            $sourceThemeName,
            $sourceArchivePath,
            $targetPlugins,
            isset($source['plugins']) && is_array($source['plugins']) ? $source['plugins'] : array(),
            isset($source['theme_dependency_scan']) && is_array($source['theme_dependency_scan']) ? $source['theme_dependency_scan'] : array()
        );
        $themeSummary = array();
        if ($themeSelected) {
            if ($themeMigration['status'] === 'error') {
                $hasFatalError = true;
                $themeSummary[] = array(
                    'level' => 'error',
                    'text' => (string) $themeMigration['note'],
                );
            } elseif ($themeMigration['status'] === 'warning') {
                $hasWarning = true;
                $themeSummary[] = array(
                    'level' => 'warning',
                    'text' => (string) $themeMigration['note'],
                );
            }
        }

        $summary = array();
        $summary[] = array(
            'level' => !empty($versionResult['compatible']) ? 'ok' : 'error',
            'text' => $versionResult['message'],
        );
        if (is_array($selectedTargetVersionResult)) {
            $summary[] = array(
                'level' => !empty($selectedTargetVersionResult['compatible']) ? 'ok' : 'error',
                'text' => isset($selectedTargetVersionResult['message']) ? (string) $selectedTargetVersionResult['message'] : '',
            );
        }
        if (!empty($themeSummary)) {
            foreach ($themeSummary as $themeSummaryRow) {
                $summary[] = $themeSummaryRow;
            }
        }
        if ($skipPluginCompatibility) {
            $summary[] = array(
                'level' => 'ok',
                'text' => '連携プラグイン比較は未選択のためスキップしました。',
            );
        }
        if ($hasFatalError) {
            $summary[] = array(
                'level' => 'error',
                'text' => '移行不可の項目があります。内容を確認してください。',
            );
        } else {
            if ($hasPluginSkip) {
                $summary[] = array(
                    'level' => 'warning',
                    'text' => sprintf('連携プラグイン %d 件は互換性不足のためスキップされます。', $skippedPluginCount),
                );
            }
            if ($hasWarning) {
                $summary[] = array(
                    'level' => 'warning',
                    'text' => '要確認の項目があります。必要に応じて設定を見直してください。',
                );
            }
            $summary[] = array(
                'level' => 'ok',
                'text' => '移行可能なデータを対象に実行できます。',
            );
        }

        $status = 'ok';
        if ($hasFatalError) {
            $status = 'error';
        } elseif ($hasWarning || $hasPluginSkip) {
            $status = 'warning';
        }

        return array(
            'status' => $status,
            'can_migrate' => !$hasFatalError,
            'summary' => $summary,
            'source' => array(
                'eccube_version' => $sourceVersion,
                'php_version' => $sourcePhp,
                'database_platform' => $sourceDb,
            ),
            'target' => array(
                'eccube_version' => $targetVersionActual,
                'php_version' => $targetPhp,
                'database_platform' => $targetDb,
                'generated_at' => $targetGeneratedAt,
            ),
            'plugin_results' => $pluginResults,
            'table_stats' => $tableStats,
            'preview' => $preview,
            'precheck_rows' => $precheckRows,
            'theme_migration' => $themeMigration,
            'skipped_plugin_count' => $skippedPluginCount,
            'migratable_plugin_count' => $pluginConnectorEnabled ? $migratablePluginCount : 0,
            'warning_plugin_count' => $warningPluginCount,
        );
    }

    private function buildThemeMigrationResult($themeSelected, $sourceBackupTheme, $sourceThemeCode, $sourceThemeName, $sourceArchivePath, $targetPlugins, $sourcePlugins = array(), $storedThemeDependencyScan = array())
    {
        $themeCode = trim((string) $sourceThemeCode);
        $themeName = trim((string) $sourceThemeName);
        $themeMigration = array(
            'enabled' => !empty($themeSelected),
            'migratable' => false,
            'status' => 'pending',
            'requires_acknowledgement' => false,
            'source_theme_code' => $themeCode,
            'source_theme_name' => $themeName,
            'note' => !empty($themeSelected) ? '' : 'テーマ移行は選択されていません。',
            'dependency_scan' => array(
                'scanned' => false,
                'dependencies' => array(),
                'resolved' => array(),
                'issues' => array(),
                'note' => '',
            ),
        );

        if (empty($themeSelected)) {
            return $themeMigration;
        }
        if (!$sourceBackupTheme) {
            $themeMigration['status'] = 'warning';
            $themeMigration['note'] = '移行元バックアップにテーマファイルが含まれていないため、テーマを移行できません。';

            return $themeMigration;
        }
        if ($themeCode === '' && $themeName === '') {
            $themeMigration['status'] = 'warning';
            $themeMigration['note'] = 'テーマ情報を判定できないため、テーマ移行の可否を確認してください。';

            return $themeMigration;
        }

        $themeMigration['migratable'] = true;
        $themeMigration['status'] = 'ok';
        $themeMigration['note'] = 'テーマデータを安全に移行できます。';

        $dependencyScan = $this->resolveStoredThemeDependencyScanForTarget(
            $this->enrichThemeDependencyScanWithPluginNames($storedThemeDependencyScan, $sourcePlugins),
            $targetPlugins
        );
        if (
            empty($dependencyScan['scanned'])
            && empty($dependencyScan['dependencies'])
            && empty($dependencyScan['issues'])
        ) {
            $archivePath = trim((string) $sourceArchivePath);
            if ($archivePath === '' || !is_file($archivePath)) {
                return $themeMigration;
            }
            $dependencyScan = $this->analyzeThemePluginDependenciesFromBackupArchive(
                $archivePath,
                $targetPlugins,
                $sourcePlugins
            );
        }
        $themeMigration['dependency_scan'] = $dependencyScan;
        if (!empty($dependencyScan['issues'])) {
            $themeMigration['migratable'] = true;
            $themeMigration['status'] = 'warning';
            $themeMigration['requires_acknowledgement'] = false;
            $themeMigration['note'] = $this->buildThemeDependencyWarningNote(count($dependencyScan['issues']));

            return $themeMigration;
        }

        if (!empty($dependencyScan['scanned']) && !empty($dependencyScan['dependencies'])) {
            $themeMigration['note'] = 'テーマデータを安全に移行できます。';
        } elseif (!$dependencyScan['scanned'] && !empty($dependencyScan['note'])) {
            $themeMigration['status'] = 'warning';
            $themeMigration['note'] = (string) $dependencyScan['note'];
        }

        return $themeMigration;
    }

    private function resolveStoredThemeDependencyScanForTarget($storedScan, $targetPlugins)
    {
        $scan = is_array($storedScan) ? $storedScan : array();
        $references = isset($scan['references']) && is_array($scan['references']) ? $scan['references'] : array();
        $dependencies = isset($scan['dependencies']) && is_array($scan['dependencies']) ? $scan['dependencies'] : array();
        $note = isset($scan['note']) ? trim((string) $scan['note']) : '';
        $scanned = !empty($scan['scanned']) || !empty($references) || !empty($dependencies);

        if (!$scanned) {
            return array(
                'scanned' => false,
                'dependencies' => array(),
                'resolved' => array(),
                'issues' => array(),
                'note' => $note,
            );
        }

        $targetOptions = $this->buildThemeTargetPluginOptions($targetPlugins);
        $resolved = array();
        $issues = array();
        $dependencyMap = array();
        foreach ($dependencies as $pluginCode) {
            $code = trim((string) $pluginCode);
            if ($code !== '') {
                $dependencyMap[$code] = true;
            }
        }
        foreach ($references as $reference) {
            if (!is_array($reference)) {
                continue;
            }
            $pluginCode = trim((string) (isset($reference['plugin_code']) ? $reference['plugin_code'] : ''));
            $pluginName = trim((string) (isset($reference['plugin_name']) ? $reference['plugin_name'] : ''));
            $file = trim((string) (isset($reference['file']) ? $reference['file'] : ''));
            $line = (int) (isset($reference['line']) ? $reference['line'] : 0);
            $snippet = isset($reference['snippet']) ? trim((string) $reference['snippet']) : '';
            if ($pluginCode === '') {
                continue;
            }
            $dependencyMap[$pluginCode] = true;
            $matchedTargetCode = $this->findMatchedThemeTargetPluginCode($pluginCode, $targetOptions);
            if ($matchedTargetCode !== '') {
                $resolved[$pluginCode] = $matchedTargetCode;
                continue;
            }

            $issueKey = strtolower($file.'|'.$line.'|'.$pluginCode.'|'.$snippet);
            $issues[$issueKey] = array(
                'file' => $file !== '' ? $file : '-',
                'line' => $line,
                'snippet' => $snippet,
                'plugin_code' => $pluginCode,
                'plugin_name' => $pluginName,
                'edit_url' => $this->resolveThemeIssueEditUrl($file),
                'message' => sprintf(
                    '%s%s に Plugin\\\\%s の参照がありますが、移行先で有効なプラグインが見つかりません。レイアウトから該当ブロックを外すか、%s を修正してください。',
                    $file !== '' ? $file : 'テーマTwig',
                    $line > 0 ? ' '.$line.'行目' : '',
                    $pluginCode,
                    $file !== '' ? basename($file) : 'Twig'
                ),
            );
        }

        $dependencyList = array_values(array_keys($dependencyMap));
        sort($dependencyList);
        ksort($resolved);

        return array(
            'scanned' => true,
            'dependencies' => $dependencyList,
            'resolved' => $resolved,
            'issues' => array_values($issues),
            'note' => $note,
        );
    }

    private function analyzeThemePluginDependenciesFromBackupArchive($sourceArchivePath, $targetPlugins, $sourcePlugins = array())
    {
        $result = array(
            'scanned' => false,
            'dependencies' => array(),
            'resolved' => array(),
            'issues' => array(),
            'note' => '',
        );
        $pluginNameMap = $this->buildPluginCodeNameMapLocal($sourcePlugins);

        $archivePath = trim((string) $sourceArchivePath);
        if ($archivePath === '' || !is_file($archivePath)) {
            $result['note'] = 'テーマ依存チェックに必要なバックアップファイルを読み込めません。';

            return $result;
        }

        $tmpRoot = sys_get_temp_dir().'/dmb_theme_scan_'.sha1(uniqid('', true).mt_rand(1000, 999999));
        $extractDir = $tmpRoot.'/backup';
        $themeExtractDir = $tmpRoot.'/theme';
        @mkdir($extractDir, 0777, true);
        @mkdir($themeExtractDir, 0777, true);

        try {
            $lower = strtolower($archivePath);
            if ($this->endsWith($lower, '.zip')) {
                $this->extractZipArchiveLocal($archivePath, $extractDir);
            } else {
                $this->extractTarGzArchiveLocal($archivePath, $extractDir);
            }

            $themeArchivePath = $this->findThemeArchivePathFromExtractDirLocal($extractDir);
            if ($themeArchivePath === '') {
                $result['note'] = 'バックアップ内のテーマアーカイブを検出できません。';

                return $result;
            }

            $this->extractTarGzArchiveLocal($themeArchivePath, $themeExtractDir);
            $targetOptions = $this->buildThemeTargetPluginOptions($targetPlugins);
            $dependencies = array();
            $resolved = array();
            $issues = array();
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($themeExtractDir, \FilesystemIterator::SKIP_DOTS),
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
                $relativePath = ltrim(str_replace(str_replace('\\', '/', $themeExtractDir), '', $filePath), '/');
                $pluginReferences = $this->extractThemePluginReferencesFromTwigContent($raw);
                if (empty($pluginReferences)) {
                    continue;
                }
                foreach ($pluginReferences as $reference) {
                    $pluginCode = trim((string) (isset($reference['plugin_code']) ? $reference['plugin_code'] : ''));
                    $line = (int) (isset($reference['line']) ? $reference['line'] : 0);
                    $snippet = isset($reference['snippet']) ? trim((string) $reference['snippet']) : '';
                    if ($pluginCode === '') {
                        continue;
                    }
                    $dependencies[$pluginCode] = true;
                    $matchedTargetCode = $this->findMatchedThemeTargetPluginCode($pluginCode, $targetOptions);
                    if ($matchedTargetCode !== '') {
                        $resolved[$pluginCode] = $matchedTargetCode;
                        continue;
                    }
                    $issueKey = strtolower($relativePath.'|'.$line.'|'.$pluginCode.'|'.$snippet);
                    $issues[$issueKey] = array(
                        'file' => $relativePath,
                        'line' => $line,
                        'snippet' => $snippet,
                        'plugin_code' => $pluginCode,
                        'plugin_name' => isset($pluginNameMap[strtolower($pluginCode)]) ? $pluginNameMap[strtolower($pluginCode)] : '',
                        'edit_url' => $this->resolveThemeIssueEditUrl($relativePath),
                        'message' => sprintf(
                            '%s%s に Plugin\\\\%s の参照がありますが、移行先で有効なプラグインが見つかりません。レイアウトから該当ブロックを外すか、%s を修正してください。',
                            $relativePath,
                            $line > 0 ? ' '.$line.'行目' : '',
                            $pluginCode,
                            basename($relativePath)
                        ),
                    );
                }
            }

            $result['scanned'] = true;
            $result['dependencies'] = array_values(array_keys($dependencies));
            sort($result['dependencies']);
            ksort($resolved);
            $result['resolved'] = $resolved;
            $result['issues'] = array_values($issues);

            return $result;
        } catch (\Exception $e) {
            $result['note'] = 'テーマ依存チェックを完了できませんでした。';

            return $result;
        } finally {
            $this->removePathRecursive($tmpRoot);
        }
    }

    private function findThemeArchivePathFromExtractDirLocal($extractDir)
    {
        $root = trim((string) $extractDir);
        if ($root === '' || !is_dir($root)) {
            return '';
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($iterator as $File) {
            if (!$File instanceof \SplFileInfo || !$File->isFile()) {
                continue;
            }
            $name = trim((string) $File->getFilename());
            if ($name !== '' && preg_match('/^theme_.+\.tar\.gz$/i', $name)) {
                return (string) $File->getPathname();
            }
        }

        return '';
    }

    private function buildThemeTargetPluginOptions($targetPlugins)
    {
        $rows = array();
        foreach ($targetPlugins as $plugin) {
            if (!is_array($plugin)) {
                continue;
            }
            $code = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));
            if ($code === '' || $this->isInternalMigrationPluginCode($code)) {
                continue;
            }
            if (!$this->normalizeEnabledFlag(isset($plugin['enabled']) ? $plugin['enabled'] : null, false)) {
                continue;
            }
            $rows[] = array('code' => $code);
        }

        return $rows;
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

    private function buildScannerTableStatsLocal($selectedModules, $sourceDatabaseTables, $sourcePluginTables, $pluginResults, $sourceSqlCounts, $moduleMap)
    {
        $rows = array();

        foreach ($selectedModules as $moduleKey) {
            if (!isset($moduleMap[$moduleKey])) {
                continue;
            }
            $module = $moduleMap[$moduleKey];
            foreach ($module['tables'] as $tableName) {
                $table = trim((string) $tableName);
                $normalized = strtolower($table);
                if ($table === '' || isset($rows[$normalized])) {
                    continue;
                }
                $rows[$normalized] = array(
                    'table' => $table,
                    'group' => (string) $module['label'],
                    'type' => 'core',
                    'estimated_rows' => isset($sourceSqlCounts[$table])
                        ? (string) max(0, (int) $sourceSqlCounts[$table])
                        : $this->resolveEstimatedRowsLabelLocal(isset($sourceDatabaseTables[$table]) ? $sourceDatabaseTables[$table] : null),
                );
            }
        }

        foreach ($pluginResults as $row) {
            if (!is_array($row) || (isset($row['status']) && (string) $row['status'] === 'error')) {
                continue;
            }
            $sourceCode = isset($row['source_code']) ? trim((string) $row['source_code']) : '';
            if ($sourceCode === '') {
                continue;
            }
            $pluginLabel = isset($row['source_name']) ? trim((string) $row['source_name']) : $sourceCode;
            $tables = isset($sourcePluginTables[$sourceCode]['tables']) && is_array($sourcePluginTables[$sourceCode]['tables'])
                ? $sourcePluginTables[$sourceCode]['tables']
                : array();
            foreach ($tables as $tableName => $tableDef) {
                $table = trim((string) $tableName);
                $normalized = strtolower($table);
                if ($table === '' || isset($rows[$normalized])) {
                    continue;
                }
                $rows[$normalized] = array(
                    'table' => $table,
                    'group' => '連携プラグイン: '.$pluginLabel,
                    'type' => 'plugin',
                    'estimated_rows' => isset($sourceSqlCounts[$table])
                        ? (string) max(0, (int) $sourceSqlCounts[$table])
                        : $this->resolveEstimatedRowsLabelLocal(isset($sourceDatabaseTables[$table]) ? $sourceDatabaseTables[$table] : null),
                );
            }
        }

        return array_values($rows);
    }

    private function buildScannerPreviewDataLocal($tableStats, $sourceSqlPreview, $sourceDatabaseTables)
    {
        $preview = array();
        foreach ($tableStats as $row) {
            if (!is_array($row) || empty($row['table'])) {
                continue;
            }

            $table = trim((string) $row['table']);
            $rows = array();
            if (isset($sourceSqlPreview[$table]) && is_array($sourceSqlPreview[$table])) {
                foreach ($sourceSqlPreview[$table] as $record) {
                    if (!is_array($record)) {
                        continue;
                    }

                    $line = array();
                    foreach ($record as $key => $value) {
                        if (!is_scalar($key) && !(is_object($key) && method_exists($key, '__toString'))) {
                            continue;
                        }

                        $column = trim((string) $key);
                        if ($column === '') {
                            continue;
                        }

                        if ($value === null) {
                            $line[$column] = '';
                        } elseif (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                            $line[$column] = (string) $value;
                        } else {
                            $line[$column] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }

                    if (!empty($line)) {
                        $rows[] = $line;
                    }
                }
            }

            $preview[$table] = $rows;
        }

        return $preview;
    }

    private function resolveScannerSourceArchivePathLocal($sourceSelectMode, $sourceFileKey, $sourceUploadToken)
    {
        if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_CURRENT) {
            $backupName = trim((string) $sourceFileKey);
            if ($backupName === '') {
                $backupName = self::SCANNER_DEFAULT_SOURCE_BACKUP_NAME;
            }
            $path = $this->backupBuilderService->resolveBackupFile($backupName);

            return is_string($path) && $path !== '' && is_file($path) ? $path : '';
        }

        if ($sourceSelectMode === self::SCANNER_SOURCE_MODE_UPLOAD) {
            $upload = $this->getSavedScannerUploadFile($sourceUploadToken);
            if (!is_array($upload) || empty($upload['path'])) {
                return '';
            }

            $path = trim((string) $upload['path']);

            return is_file($path) ? $path : '';
        }

        $backupName = trim((string) $sourceFileKey);
        if ($backupName === '') {
            return '';
        }

        $path = $this->backupBuilderService->resolveBackupFile($backupName);

        return is_string($path) && $path !== '' && is_file($path) ? $path : '';
    }

    private function loadFullScannerPreviewFromArchivePathLocal($archivePath, $tableStats)
    {
        $path = trim((string) $archivePath);
        if ($path === '' || !is_file($path)) {
            return array();
        }

        $tables = array();
        foreach ($tableStats as $row) {
            if (!is_array($row) || empty($row['table'])) {
                continue;
            }
            $table = $this->normalizeSqlTableNameLocal((string) $row['table']);
            if ($table !== '') {
                $tables[] = $table;
            }
        }
        $tables = array_values(array_unique($tables));
        if (empty($tables)) {
            return array();
        }

        $tmpRoot = sys_get_temp_dir().'/dmb_preview_full_'.sha1($path.'|'.microtime(true).mt_rand(1000, 999999));
        $extractDir = $tmpRoot.'/extract';
        @mkdir($extractDir, 0777, true);

        try {
            $lower = strtolower($path);
            if ($this->endsWith($lower, '.zip')) {
                $this->extractZipArchiveLocal($path, $extractDir);
            } else {
                $this->extractTarGzArchiveLocal($path, $extractDir);
            }

            $analysis = $this->analyzeSqlFromExtractDirLocal($extractDir, $tables, 0);

            return isset($analysis['preview']) && is_array($analysis['preview']) ? $analysis['preview'] : array();
        } catch (\Exception $e) {
            return array();
        } finally {
            $this->removePathRecursive($tmpRoot);
        }
    }

    private function buildScannerPrecheckRowsLocal(
        $sourceVersion,
        $sourcePhp,
        $sourceDb,
        $targetVersionActual,
        $targetPhp,
        $targetDb,
        $versionResult,
        $selectedTargetVersionResult,
        $selectedSourceCodes,
        $migratablePluginCount,
        $skippedPluginCount
    ) {
        $rows = array();
        $rows[] = array(
            'label' => 'EC-CUBEバージョン',
            'value' => sprintf('移行元 %s / 移行先 %s', $sourceVersion !== '' ? $sourceVersion : '-', $targetVersionActual !== '' ? $targetVersionActual : '-'),
            'status' => !empty($versionResult['compatible']) ? 'ok' : 'warning',
            'suggest' => !empty($versionResult['compatible']) ? '-' : '移行先のEC-CUBEバージョンを見直してください。',
        );
        if (is_array($selectedTargetVersionResult)) {
            $rows[] = array(
                'label' => '選択バージョン一致',
                'value' => isset($selectedTargetVersionResult['message']) ? (string) $selectedTargetVersionResult['message'] : '-',
                'status' => !empty($selectedTargetVersionResult['compatible']) ? 'ok' : 'warning',
                'suggest' => !empty($selectedTargetVersionResult['compatible']) ? '-' : '移行先バージョンとアップロードファイルを一致させてください。',
            );
        }

        $phpCompatible = true;
        $sourcePhpParsed = $this->parsePhpMajorMinorLocal($sourcePhp);
        $targetPhpParsed = $this->parsePhpMajorMinorLocal($targetPhp);
        if (is_array($sourcePhpParsed) && is_array($targetPhpParsed)) {
            if ($targetPhpParsed['major'] < $sourcePhpParsed['major']) {
                $phpCompatible = false;
            } elseif ($targetPhpParsed['major'] === $sourcePhpParsed['major'] && $targetPhpParsed['minor'] < $sourcePhpParsed['minor']) {
                $phpCompatible = false;
            }
        }
        $rows[] = array(
            'label' => 'PHPバージョン',
            'value' => sprintf('移行元 %s / 移行先 %s', $sourcePhp !== '' ? $sourcePhp : '-', $targetPhp !== '' ? $targetPhp : '-'),
            'status' => $phpCompatible ? 'ok' : 'warning',
            'suggest' => $phpCompatible ? '-' : '移行先PHPを移行元以上に合わせてください。',
        );

        $dbCompatible = ($sourceDb !== '' && $targetDb !== '') ? strcasecmp($sourceDb, $targetDb) === 0 : false;
        $rows[] = array(
            'label' => 'データベース',
            'value' => sprintf('移行元 %s / 移行先 %s', $sourceDb !== '' ? $sourceDb : '-', $targetDb !== '' ? $targetDb : '-'),
            'status' => $dbCompatible ? 'ok' : 'warning',
            'suggest' => $dbCompatible ? '-' : '異なるデータベース種別では差分確認が必要です。',
        );

        $rows[] = array(
            'label' => '連携プラグイン判定',
            'value' => sprintf('選択 %d 件 / 移行可 %d 件 / スキップ %d 件', count($selectedSourceCodes), $migratablePluginCount, $skippedPluginCount),
            'status' => $skippedPluginCount > 0 ? 'warning' : 'ok',
            'suggest' => $skippedPluginCount > 0 ? 'スキップ対象プラグインの互換性を確認してください。' : '-',
        );

        return $rows;
    }

    private function resolveEstimatedRowsLabelLocal($tableDef)
    {
        if (!is_array($tableDef)) {
            return '0';
        }

        $candidates = array(
            isset($tableDef['rows']) ? $tableDef['rows'] : null,
            isset($tableDef['row_count']) ? $tableDef['row_count'] : null,
            isset($tableDef['estimated_rows']) ? $tableDef['estimated_rows'] : null,
        );
        foreach ($candidates as $candidate) {
            if (is_numeric($candidate)) {
                return (string) max(0, (int) $candidate);
            }
        }

        return '0';
    }

    private function normalizeSelectedScannerModulesLocal($selectedModules, $moduleMap = null)
    {
        if (!is_array($moduleMap) || empty($moduleMap)) {
            $moduleMap = $this->getScannerModuleDefinitions();
        }
        $rows = array();
        foreach ($selectedModules as $moduleKey) {
            $key = $this->normalizeScannerModuleKeyLocal($moduleKey);
            if ($key !== '' && isset($moduleMap[$key])) {
                $rows[] = $key;
            }
        }

        return array_values(array_unique($rows));
    }

    private function normalizeScannerModuleKeyLocal($moduleKey)
    {
        $key = trim((string) $moduleKey);
        if ($key === '') {
            return '';
        }

        $aliases = array(
            'member_data' => 'customer',
            'product_data' => 'product',
            'order_data' => 'order',
            'layout_page' => 'design_page',
            'order_report_status' => 'order_status_master',
            'system_config_data' => 'system_data',
            'admin_data' => 'admin_member',
        );

        return isset($aliases[$key]) ? $aliases[$key] : $key;
    }

    private function compareVersionCompatibilityLocal($sourceVersion, $targetVersion)
    {
        $source = $this->parseMajorMinorLocal($sourceVersion);
        $target = $this->parseMajorMinorLocal($targetVersion);
        if (!is_array($source) || !is_array($target)) {
            return array(
                'compatible' => false,
                'message' => 'EC-CUBEバージョン情報を取得できないため判定できません。',
            );
        }

        $supportedCrossMajor = ($source['major'] === 2 || $source['major'] === 3) && $target['major'] === 4;
        if ($source['major'] !== $target['major'] && !$supportedCrossMajor) {
            return array(
                'compatible' => false,
                'message' => sprintf('EC-CUBE移行パス: 移行元 %s / 移行先 %s', $sourceVersion, $targetVersion),
            );
        }

        if ($source['major'] === $target['major'] && $target['minor'] < $source['minor']) {
            return array(
                'compatible' => false,
                'message' => sprintf('EC-CUBE移行パス: 移行元 %s / 移行先 %s', $sourceVersion, $targetVersion),
            );
        }

        return array(
            'compatible' => true,
            'message' => sprintf('EC-CUBE移行パス: 移行元 %s / 移行先 %s', $sourceVersion, $targetVersion),
        );
    }

    private function parsePhpMajorMinorLocal($version)
    {
        if (preg_match('/^(\\d+)\\.(\\d+)/', trim((string) $version), $matches) !== 1) {
            return null;
        }

        return array(
            'major' => (int) $matches[1],
            'minor' => (int) $matches[2],
        );
    }

    private function autoSelectConnectorTargetCodes($connectorRows)
    {
        $rows = array();
        foreach ($connectorRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $sourceCode = isset($row['source_code']) ? trim((string) $row['source_code']) : '';
            $targetCode = isset($row['target_code']) ? trim((string) $row['target_code']) : '';
            if ($sourceCode === '' || $targetCode === '') {
                continue;
            }
            $rows[$sourceCode] = $targetCode;
        }

        return $rows;
    }

    private function findMatchedTargetPluginCode($sourceCode, $targetOptions)
    {
        $exact = trim((string) $sourceCode);
        if ($exact === '') {
            return '';
        }
        $exactNormalized = $this->normalizePluginCodeForMatch($exact, false);
        $looseNormalized = $this->normalizePluginCodeForMatch($exact, true);

        foreach ($targetOptions as $option) {
            if (!is_array($option) || empty($option['code'])) {
                continue;
            }
            $targetCode = trim((string) $option['code']);
            if ($targetCode !== '' && strcasecmp($targetCode, $exact) === 0) {
                return $targetCode;
            }
        }

        foreach ($targetOptions as $option) {
            if (!is_array($option) || empty($option['code'])) {
                continue;
            }
            $targetCode = trim((string) $option['code']);
            if ($targetCode !== '' && $this->normalizePluginCodeForMatch($targetCode, false) === $exactNormalized) {
                return $targetCode;
            }
        }

        foreach ($targetOptions as $option) {
            if (!is_array($option) || empty($option['code'])) {
                continue;
            }
            $targetCode = trim((string) $option['code']);
            if ($targetCode !== '' && $this->normalizePluginCodeForMatch($targetCode, true) === $looseNormalized) {
                return $targetCode;
            }
        }

        return '';
    }

    private function findMatchedThemeTargetPluginCode($sourceCode, $targetOptions)
    {
        $exact = trim((string) $sourceCode);
        if ($exact === '') {
            return '';
        }
        $exactNormalized = $this->normalizePluginCodeForMatch($exact, false);

        foreach ($targetOptions as $option) {
            if (!is_array($option) || empty($option['code'])) {
                continue;
            }
            $targetCode = trim((string) $option['code']);
            if ($targetCode !== '' && strcasecmp($targetCode, $exact) === 0) {
                return $targetCode;
            }
        }

        foreach ($targetOptions as $option) {
            if (!is_array($option) || empty($option['code'])) {
                continue;
            }
            $targetCode = trim((string) $option['code']);
            if ($targetCode !== '' && $this->normalizePluginCodeForMatch($targetCode, false) === $exactNormalized) {
                return $targetCode;
            }
        }

        return '';
    }

    private function normalizePluginCodeForMatch($code, $removeDigits)
    {
        $normalized = strtolower(trim((string) $code));
        if ($removeDigits) {
            $normalized = preg_replace('/[^a-z]/', '', $normalized);
        } else {
            $normalized = preg_replace('/[^a-z0-9]/', '', $normalized);
        }

        return is_string($normalized) ? $normalized : '';
    }

    private function isInternalMigrationPluginCode($code)
    {
        $normalized = $this->normalizePluginCodeForMatch($code, false);

        return strpos($normalized, 'datamigrationassistant') === 0
            || strpos($normalized, 'datamigrationbackup') === 0;
    }

    private function buildScannerInfoFromSnapshot($snapshot, $fallback)
    {
        $source = is_array($snapshot) ? $snapshot : array();
        $base = is_array($fallback) ? $fallback : array();

        $info = $base;
        $info['eccube_version'] = !empty($source['eccube_version']) ? (string) $source['eccube_version'] : (isset($base['eccube_version']) ? (string) $base['eccube_version'] : '-');
        $info['php_version'] = !empty($source['php_version']) ? (string) $source['php_version'] : (isset($base['php_version']) ? (string) $base['php_version'] : '-');
        $info['generated_at'] = !empty($source['generated_at']) ? (string) $source['generated_at'] : (isset($base['generated_at']) ? (string) $base['generated_at'] : '');
        $info['database_platform'] = !empty($source['database_platform']) ? (string) $source['database_platform'] : (isset($base['database_platform']) ? (string) $base['database_platform'] : '');
        $info['backup_theme'] = !empty($source['backup_theme']);
        $info['theme_code'] = !empty($source['theme_code']) ? (string) $source['theme_code'] : (isset($base['theme_code']) ? (string) $base['theme_code'] : '');
        $info['theme_name'] = !empty($source['theme_name']) ? (string) $source['theme_name'] : (isset($base['theme_name']) ? (string) $base['theme_name'] : '');
        $info['plugins'] = $this->normalizeSnapshotPlugins(isset($source['plugins']) ? $source['plugins'] : array());
        $themeDependencyScan = isset($source['theme_dependency_scan']) && is_array($source['theme_dependency_scan'])
            ? $source['theme_dependency_scan']
            : (isset($base['theme_dependency_scan']) && is_array($base['theme_dependency_scan']) ? $base['theme_dependency_scan'] : array());
        $info['theme_dependency_scan'] = $this->enrichThemeDependencyScanWithPluginNames($themeDependencyScan, $info['plugins']);
        $info['options'] = isset($base['options']) && is_array($base['options']) ? $base['options'] : array();

        return $info;
    }

    private function loadSnapshotFromBackupFileName($backupName)
    {
        $path = $this->backupBuilderService->resolveBackupFile($backupName);
        if ($path === null) {
            throw new \RuntimeException('選択した移行元バックアップファイルが見つかりません。');
        }

        return $this->loadSnapshotFromArchivePath($path);
    }

    private function loadSnapshotFromScannerUploadToken($token)
    {
        $defaultTargetVersion = $this->parseDefaultTargetUploadToken($token);
        if ($defaultTargetVersion !== '') {
            $snapshot = $this->loadSnapshotFromTargetVersion($defaultTargetVersion);
            if (is_array($snapshot)) {
                return $snapshot;
            }

            throw new \RuntimeException('標準構成ファイルを読み込めません。');
        }

        $upload = $this->getSavedScannerUploadFile($token);
        if (!is_array($upload)) {
            throw new \RuntimeException('アップロード済みファイルが見つかりません。');
        }

        $path = isset($upload['path']) ? trim((string) $upload['path']) : '';
        if ($path === '' || !is_file($path)) {
            throw new \RuntimeException('アップロード済みファイルを読み込めません。');
        }

        return $this->loadSnapshotFromArchivePath($path);
    }

    private function loadSnapshotFromTargetVersion($targetVersion)
    {
        $version = trim((string) $targetVersion);
        if ($version === '') {
            return null;
        }

        $rootDir = isset($this->app['config']['root_dir']) ? rtrim((string) $this->app['config']['root_dir'], '/') : '';
        if ($rootDir === '') {
            return null;
        }

        $baseDir = $rootDir.'/app/Plugin/DataMigrationBackup30/storage/default';
        $candidates = array(
            $baseDir.'/'.$version.'.zip',
            $baseDir.'/'.$version.'.tar.gz',
            $baseDir.'/'.$version.'.tgz',
            $baseDir.'/'.$version.'.tar',
        );

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $this->loadSnapshotFromArchivePath($candidate);
            }
        }

        return null;
    }

    private function loadSnapshotFromArchivePath($archivePath)
    {
        $path = trim((string) $archivePath);
        if ($path === '' || !is_file($path)) {
            return null;
        }

        $tmpRoot = sys_get_temp_dir().'/dmb_snapshot_'.sha1($path.'|'.microtime(true).mt_rand(1000, 999999));
        $extractDir = $tmpRoot.'/extract';
        @mkdir($extractDir, 0777, true);

        try {
            $lower = strtolower($path);
            if ($this->endsWith($lower, '.zip')) {
                $this->extractZipArchiveLocal($path, $extractDir);
            } else {
                $this->extractTarGzArchiveLocal($path, $extractDir);
            }

            $manifestPath = $this->findFileRecursiveLocal($extractDir, 'manifest.json');
            $precheckPath = $this->findFileRecursiveLocal($extractDir, 'precheck.json');
            $databaseSchemaPath = $this->findFileRecursiveLocal($extractDir, 'databases.json');

            $manifest = $this->parseJsonFileLocal($manifestPath);
            $precheck = $this->parseJsonFileLocal($precheckPath);
            $databaseSchema = $this->parseJsonFileLocal($databaseSchemaPath);

            if (!is_array($manifest) || !is_array($precheck)) {
                throw new \RuntimeException('バックアップ内の manifest.json / precheck.json を読み込めません。');
            }

            $facts = isset($precheck['facts']) && is_array($precheck['facts']) ? $precheck['facts'] : array();
            $options = isset($manifest['options']) && is_array($manifest['options']) ? $manifest['options'] : array();

            $plugins = array();
            if (isset($manifest['source_plugins']) && is_array($manifest['source_plugins'])) {
                $plugins = $manifest['source_plugins'];
            } elseif (isset($facts['all_plugins']) && is_array($facts['all_plugins'])) {
                $plugins = $facts['all_plugins'];
            } elseif (isset($facts['active_plugins']) && is_array($facts['active_plugins'])) {
                $plugins = $facts['active_plugins'];
            }
            $sourcePluginTables = array();
            if (isset($manifest['source_plugin_tables']) && is_array($manifest['source_plugin_tables'])) {
                $sourcePluginTables = $manifest['source_plugin_tables'];
            } elseif (isset($facts['source_plugin_tables']) && is_array($facts['source_plugin_tables'])) {
                $sourcePluginTables = $facts['source_plugin_tables'];
            }
            $themeDependencyScan = array();
            if (isset($manifest['theme_dependency_scan']) && is_array($manifest['theme_dependency_scan'])) {
                $themeDependencyScan = $manifest['theme_dependency_scan'];
            } elseif (isset($facts['theme_dependency_scan']) && is_array($facts['theme_dependency_scan'])) {
                $themeDependencyScan = $facts['theme_dependency_scan'];
            }
            $themeDependencyScan = $this->enrichThemeDependencyScanWithPluginNames($themeDependencyScan, $plugins);

            $generatedAt = '';
            if (!empty($precheck['generated_at'])) {
                $generatedAt = trim((string) $precheck['generated_at']);
            } elseif (!empty($manifest['generated_at'])) {
                $generatedAt = trim((string) $manifest['generated_at']);
            }
            if ($generatedAt !== '') {
                try {
                    $generatedAt = (new \DateTime($generatedAt))->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                }
            }

            $databasePlatform = '';
            if (!empty($facts['database_platform'])) {
                $databasePlatform = trim((string) $facts['database_platform']);
            } elseif (is_array($databaseSchema) && !empty($databaseSchema['database_platform'])) {
                $databasePlatform = trim((string) $databaseSchema['database_platform']);
            }

            $databaseTables = $this->resolveSnapshotDatabaseTablesLocal($precheck, $facts, $databaseSchema);
            $tablesForSql = $this->collectSqlTargetTablesLocal($databaseTables, is_array($sourcePluginTables) ? $sourcePluginTables : array());
            $sqlAnalysis = $this->analyzeSqlFromExtractDirLocal($extractDir, $tablesForSql, 20);

            $backupTheme = !empty($options['backup_theme']);
            if (!$backupTheme && isset($manifest['components']) && is_array($manifest['components'])) {
                foreach ($manifest['components'] as $component) {
                    $componentName = trim((string) $component);
                    if ($componentName !== '' && preg_match('/^theme_.+\.tar\.gz$/i', $componentName)) {
                        $backupTheme = true;
                        break;
                    }
                }
            }

            $themeCode = !empty($options['theme_code']) ? trim((string) $options['theme_code']) : '';
            $themeName = !empty($options['theme_name']) ? trim((string) $options['theme_name']) : '';
            if ($themeName === '' && !empty($facts['theme_name'])) {
                $themeName = trim((string) $facts['theme_name']);
            }

            return array(
                'eccube_version' => $this->resolveSnapshotEccubeVersionLocal($precheck, $facts),
                'php_version' => !empty($facts['php_version']) ? trim((string) $facts['php_version']) : '',
                'generated_at' => $generatedAt,
                'database_platform' => $databasePlatform,
                'backup_theme' => $backupTheme,
                'theme_code' => $themeCode,
                'theme_name' => $themeName,
                'theme_dependency_scan' => $themeDependencyScan,
                'plugins' => $plugins,
                'source_plugin_tables' => $sourcePluginTables,
                'database_tables' => $databaseTables,
                'sql_counts' => is_array($sqlAnalysis) && isset($sqlAnalysis['counts']) && is_array($sqlAnalysis['counts']) ? $sqlAnalysis['counts'] : array(),
                'sql_preview' => is_array($sqlAnalysis) && isset($sqlAnalysis['preview']) && is_array($sqlAnalysis['preview']) ? $sqlAnalysis['preview'] : array(),
            );
        } finally {
            $this->removePathRecursive($tmpRoot);
        }
    }

    private function resolveSnapshotEccubeVersionLocal($precheck, $facts)
    {
        $candidates = array(
            is_array($facts) && isset($facts['eccube_version']) ? $facts['eccube_version'] : '',
            is_array($precheck) && isset($precheck['eccube_version']) ? $precheck['eccube_version'] : '',
        );

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function resolveSnapshotDatabaseTablesLocal($precheck, $facts, $databaseSchema)
    {
        if (is_array($databaseSchema) && isset($databaseSchema['tables']) && is_array($databaseSchema['tables']) && !empty($databaseSchema['tables'])) {
            return $databaseSchema['tables'];
        }

        $candidates = array();
        if (isset($facts['database_tables']) && is_array($facts['database_tables'])) {
            $candidates[] = $facts['database_tables'];
        }
        if (isset($precheck['database_tables']) && is_array($precheck['database_tables'])) {
            $candidates[] = $precheck['database_tables'];
        }
        if (isset($facts['db_tables']) && is_array($facts['db_tables'])) {
            $candidates[] = $facts['db_tables'];
        }
        if (isset($precheck['db_tables']) && is_array($precheck['db_tables'])) {
            $candidates[] = $precheck['db_tables'];
        }

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeSnapshotDatabaseTablesLocal($candidate);
            if (!empty($normalized)) {
                return $normalized;
            }
        }

        return array();
    }

    private function normalizeSnapshotDatabaseTablesLocal($tables)
    {
        $rows = array();
        if (!is_array($tables)) {
            return $rows;
        }

        foreach ($tables as $tableName => $tableDefinition) {
            $name = '';
            if (!is_int($tableName)) {
                $name = trim((string) $tableName);
            } elseif (is_scalar($tableDefinition) || (is_object($tableDefinition) && method_exists($tableDefinition, '__toString'))) {
                $name = trim((string) $tableDefinition);
            }

            if ($name === '') {
                continue;
            }

            $rows[$name] = !is_int($tableName) && is_array($tableDefinition) ? $tableDefinition : array();
        }

        if (!empty($rows)) {
            ksort($rows);
        }

        return $rows;
    }

    private function extractZipArchiveLocal($archivePath, $extractDir)
    {
        $zip = new \ZipArchive();
        if ($zip->open($archivePath) !== true) {
            throw new \RuntimeException('ZIPファイルを展開できません。');
        }
        $zip->extractTo($extractDir);
        $zip->close();
    }

    private function extractTarGzArchiveLocal($archivePath, $extractDir)
    {
        $tarSource = $archivePath;
        $lower = strtolower($archivePath);
        if ($this->endsWith($lower, '.tgz')) {
            $renamed = substr($archivePath, 0, -4).'.tar.gz';
            @copy($archivePath, $renamed);
            $tarSource = $renamed;
        }

        try {
            $tar = new \PharData($tarSource);
            $tar->extractTo($extractDir, null, true);
        } catch (\Exception $e) {
            if ($this->endsWith(strtolower($tarSource), '.tar.gz') && $this->extractTarGzArchiveWithTarCommandLocal($tarSource, $extractDir)) {
                return;
            }
            throw new \RuntimeException('TAR.GZファイルを展開できません。');
        }
    }

    private function extractTarGzArchiveWithTarCommandLocal($archivePath, $extractDir)
    {
        if (!function_exists('exec')) {
            return false;
        }

        $archive = (string) $archivePath;
        $target = (string) $extractDir;
        if ($archive === '' || !is_file($archive) || $target === '') {
            return false;
        }

        $command = 'tar -xzf '.escapeshellarg($archive).' -C '.escapeshellarg($target).' 2>&1';
        $output = array();
        $exitCode = 0;
        @exec($command, $output, $exitCode);

        return $exitCode === 0;
    }

    private function findFileRecursiveLocal($rootDir, $targetName)
    {
        if (!is_dir($rootDir)) {
            return null;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }
            if ((string) $file->getFilename() === (string) $targetName) {
                return $file->getPathname();
            }
        }

        return null;
    }

    private function parseJsonFileLocal($path)
    {
        if (!is_string($path) || $path === '' || !is_file($path)) {
            return null;
        }
        $raw = @file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return null;
        }
        $json = json_decode($raw, true);

        return is_array($json) ? $json : null;
    }

    private function collectSqlTargetTablesLocal($databaseTables, $sourcePluginTables)
    {
        $rows = array();

        foreach ($databaseTables as $tableName => $tableDef) {
            if (is_scalar($tableName) || (is_object($tableName) && method_exists($tableName, '__toString'))) {
                $name = $this->normalizeSqlTableNameLocal((string) $tableName);
                if ($name !== '') {
                    $rows[$name] = true;
                }
            } elseif (is_scalar($tableDef) || (is_object($tableDef) && method_exists($tableDef, '__toString'))) {
                $name = $this->normalizeSqlTableNameLocal((string) $tableDef);
                if ($name !== '') {
                    $rows[$name] = true;
                }
            }
        }

        foreach ($sourcePluginTables as $pluginDef) {
            if (!is_array($pluginDef)) {
                continue;
            }

            $tables = isset($pluginDef['tables']) && is_array($pluginDef['tables']) ? $pluginDef['tables'] : array();
            foreach ($tables as $tableName => $tableDef) {
                if (is_scalar($tableName) || (is_object($tableName) && method_exists($tableName, '__toString'))) {
                    $name = $this->normalizeSqlTableNameLocal((string) $tableName);
                    if ($name !== '') {
                        $rows[$name] = true;
                    }
                } elseif (is_scalar($tableDef) || (is_object($tableDef) && method_exists($tableDef, '__toString'))) {
                    $name = $this->normalizeSqlTableNameLocal((string) $tableDef);
                    if ($name !== '') {
                        $rows[$name] = true;
                    }
                }
            }
        }

        return array_keys($rows);
    }

    private function analyzeSqlFromExtractDirLocal($extractDir, $targetTables, $previewLimit)
    {
        $sqlPath = $this->findFileRecursiveLocal($extractDir, 'database.sql');
        if ($sqlPath === null || !is_file($sqlPath)) {
            return array('counts' => array(), 'preview' => array());
        }

        try {
            return $this->analyzeSqlInsertFileLocal($sqlPath, $targetTables, $previewLimit);
        } catch (\Exception $e) {
            return array('counts' => array(), 'preview' => array());
        }
    }

    private function analyzeSqlInsertFileLocal($sqlPath, $targetTables, $previewLimit)
    {
        $targetMap = array();
        foreach ($targetTables as $table) {
            if (!is_scalar($table) && !(is_object($table) && method_exists($table, '__toString'))) {
                continue;
            }

            $normalized = $this->normalizeSqlTableNameLocal((string) $table);
            if ($normalized !== '') {
                $targetMap[$normalized] = true;
            }
        }

        if (empty($targetMap)) {
            return array('counts' => array(), 'preview' => array());
        }

        $counts = array();
        foreach (array_keys($targetMap) as $tableName) {
            $counts[$tableName] = 0;
        }

        $preview = array();
        $unlimitedPreview = (int) $previewLimit <= 0;
        $limit = $unlimitedPreview ? 0 : max(1, (int) $previewLimit);

        $this->iterateSqlInsertRowsLocal($sqlPath, $targetMap, function ($table, $row) use (&$counts, &$preview, $unlimitedPreview, $limit) {
            if (!isset($counts[$table])) {
                $counts[$table] = 0;
            }
            ++$counts[$table];
            if (!isset($preview[$table])) {
                $preview[$table] = array();
            }
            if ($unlimitedPreview || count($preview[$table]) < $limit) {
                $preview[$table][] = $row;
            }
        });

        return array(
            'counts' => $counts,
            'preview' => $preview,
        );
    }

    private function iterateSqlInsertRowsLocal($sqlPath, $targetMap, callable $rowCallback)
    {
        if (!is_file($sqlPath)) {
            throw new \RuntimeException('SQLファイルが存在しません。');
        }

        $fp = fopen($sqlPath, 'rb');
        if (!$fp) {
            throw new \RuntimeException('SQLファイルを開けません。');
        }

        $buffer = '';
        $collecting = false;

        while (($line = fgets($fp)) !== false) {
            $isInsertStart = preg_match('/^\s*INSERT\s+INTO\s+/i', $line) === 1;

            if (!$collecting && !$isInsertStart) {
                continue;
            }

            if ($isInsertStart && !$collecting) {
                $buffer = $line;
                $collecting = true;
            } else {
                $buffer .= $line;
            }

            if ($collecting && preg_match('/;\s*$/', trim($line)) === 1) {
                $this->parseSqlInsertStatementLocal($buffer, $targetMap, $rowCallback);
                $buffer = '';
                $collecting = false;
            }
        }

        if ($collecting && $buffer !== '') {
            $this->parseSqlInsertStatementLocal($buffer, $targetMap, $rowCallback);
        }

        fclose($fp);
    }

    private function parseSqlInsertStatementLocal($sql, $targetMap, callable $rowCallback)
    {
        $sql = trim((string) $sql);
        if ($sql === '') {
            return;
        }

        if (preg_match('/^INSERT\s+INTO\s+(.+?)\s*\((.*?)\)\s*VALUES\s*(.+);$/is', $sql, $matches) !== 1) {
            return;
        }

        $table = $this->normalizeSqlTableNameLocal(isset($matches[1]) ? (string) $matches[1] : '');
        if ($table === '' || !isset($targetMap[$table])) {
            return;
        }

        $columns = $this->parseSqlColumnsLocal(isset($matches[2]) ? (string) $matches[2] : '');
        if (empty($columns)) {
            return;
        }

        $tuples = $this->splitSqlTuplesLocal(isset($matches[3]) ? (string) $matches[3] : '');
        foreach ($tuples as $tuple) {
            $values = $this->splitSqlTupleValuesLocal($tuple);
            if (count($values) !== count($columns)) {
                continue;
            }

            $row = array();
            foreach ($columns as $index => $column) {
                $row[$column] = $this->decodeSqlTokenLocal($values[$index]);
            }
            call_user_func($rowCallback, $table, $row);
        }
    }

    private function parseSqlColumnsLocal($columnsRaw)
    {
        $rows = array();
        foreach (explode(',', (string) $columnsRaw) as $col) {
            $name = trim(str_replace(array('`', '"'), '', $col));
            if ($name !== '') {
                $rows[] = $name;
            }
        }

        return $rows;
    }

    private function splitSqlTuplesLocal($valuesRaw)
    {
        $valuesRaw = trim((string) $valuesRaw);
        $tuples = array();
        $len = strlen($valuesRaw);
        $depth = 0;
        $inQuote = false;
        $escape = false;
        $buf = '';

        for ($i = 0; $i < $len; ++$i) {
            $ch = $valuesRaw[$i];

            if ($inQuote) {
                $buf .= $ch;
                if ($escape) {
                    $escape = false;
                    continue;
                }
                if ($ch === '\\') {
                    $escape = true;
                    continue;
                }
                if ($ch === "'") {
                    $inQuote = false;
                }

                continue;
            }

            if ($ch === "'") {
                $inQuote = true;
                $buf .= $ch;
                continue;
            }

            if ($ch === '(') {
                if ($depth === 0) {
                    $buf = '';
                } else {
                    $buf .= $ch;
                }
                ++$depth;
                continue;
            }

            if ($ch === ')') {
                --$depth;
                if ($depth === 0) {
                    $tuples[] = $buf;
                    $buf = '';
                    continue;
                }
                if ($depth > 0) {
                    $buf .= $ch;
                }

                continue;
            }

            if ($depth > 0) {
                $buf .= $ch;
            }
        }

        return $tuples;
    }

    private function splitSqlTupleValuesLocal($tuple)
    {
        $values = array();
        $len = strlen($tuple);
        $inQuote = false;
        $escape = false;
        $buf = '';

        for ($i = 0; $i < $len; ++$i) {
            $ch = $tuple[$i];

            if ($inQuote) {
                $buf .= $ch;
                if ($escape) {
                    $escape = false;
                    continue;
                }
                if ($ch === '\\') {
                    $escape = true;
                    continue;
                }
                if ($ch === "'") {
                    $inQuote = false;
                }

                continue;
            }

            if ($ch === "'") {
                $inQuote = true;
                $buf .= $ch;
                continue;
            }

            if ($ch === ',') {
                $values[] = trim($buf);
                $buf = '';
                continue;
            }

            $buf .= $ch;
        }

        $values[] = trim($buf);

        return $values;
    }

    private function decodeSqlTokenLocal($token)
    {
        $token = trim((string) $token);
        if (strcasecmp($token, 'NULL') === 0) {
            return null;
        }

        if (preg_match('/^b\'(0|1)\'$/i', $token, $matches) === 1) {
            return $matches[1] === '1' ? 1 : 0;
        }

        if (strlen($token) >= 2 && $token[0] === "'" && substr($token, -1) === "'") {
            $value = substr($token, 1, -1);
            $value = str_replace(array('\\\\', '\\0', '\\n', '\\r', '\\t', "\\'"), array('\\', "\0", "\n", "\r", "\t", "'"), $value);

            return $value;
        }

        if (is_numeric($token)) {
            return strpos($token, '.') !== false ? (float) $token : (int) $token;
        }

        return $token;
    }

    private function normalizeSqlTableNameLocal($raw)
    {
        $table = trim((string) $raw);
        $table = preg_replace('/\s+/', '', $table);
        if ($table === null) {
            $table = '';
        }
        $table = str_replace(array('`', '"'), '', $table);
        if (strpos($table, '.') !== false) {
            $parts = explode('.', $table);
            $table = (string) end($parts);
        }

        return strtolower($table);
    }

    private function normalizeSnapshotPlugins($plugins)
    {
        $rows = array();
        foreach ($plugins as $plugin) {
            if (!is_array($plugin)) {
                continue;
            }
            $code = trim((string) (isset($plugin['code']) ? $plugin['code'] : ''));
            if ($code === '') {
                continue;
            }
            $rows[] = array(
                'code' => $code,
                'name' => trim((string) (isset($plugin['name']) ? $plugin['name'] : $code)),
                'version' => trim((string) (isset($plugin['version']) ? $plugin['version'] : '')),
                'enabled' => $this->normalizeEnabledFlag(isset($plugin['enabled']) ? $plugin['enabled'] : null, false),
            );
        }
        usort($rows, function ($a, $b) {
            return strcasecmp((string) $a['code'], (string) $b['code']);
        });

        return $rows;
    }

    private function normalizeEnabledFlag($value, $default)
    {
        if ($value === null) {
            return (bool) $default;
        }
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return ((int) $value) > 0;
        }
        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            if (in_array($normalized, array('1', 'true', 'yes', 'on'), true)) {
                return true;
            }
            if (in_array($normalized, array('0', 'false', 'no', 'off'), true)) {
                return false;
            }
        }

        return (bool) $default;
    }

    private function removePathRecursive($path)
    {
        if ($path === '' || $path === null) {
            return;
        }
        if (is_file($path) || is_link($path)) {
            @unlink($path);

            return;
        }
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if (!is_array($items)) {
            @rmdir($path);

            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $this->removePathRecursive($path.'/'.$item);
        }
        @rmdir($path);
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

    private function isSupportedArchiveFileNameLocal($fileName)
    {
        $lower = strtolower(trim((string) $fileName));
        if ($lower === '') {
            return false;
        }

        return $this->endsWith($lower, '.tar.gz')
            || $this->endsWith($lower, '.tgz')
            || $this->endsWith($lower, '.tar')
            || $this->endsWith($lower, '.zip');
    }

    private function getScannerUploadDirLocal()
    {
        $rootDir = isset($this->app['config']['root_dir']) ? rtrim((string) $this->app['config']['root_dir'], '/') : '';
        if ($rootDir !== '') {
            return $rootDir.'/app/cache/DataMigrationBackup30/scanner_uploads';
        }

        return sys_get_temp_dir().'/DataMigrationBackup30/scanner_uploads';
    }

    private function getScannerChunkDirLocal()
    {
        return $this->getScannerUploadDirLocal().'/chunks';
    }

    private function sanitizeScannerHexTokenLocal($token)
    {
        return preg_replace('/[^a-f0-9]/', '', strtolower(trim((string) $token)));
    }

    private function getChunkMetaPathLocal($uploadId)
    {
        $safeId = $this->sanitizeScannerHexTokenLocal($uploadId);
        if ($safeId === '') {
            throw new \RuntimeException('チャンクアップロードIDが不正です。');
        }

        return $this->getScannerChunkDirLocal().'/'.$safeId.'.json';
    }

    private function writeChunkMetaLocal($uploadId, $meta)
    {
        $metaPath = $this->getChunkMetaPathLocal($uploadId);
        @file_put_contents($metaPath, (string) json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function readChunkMetaLocal($uploadId)
    {
        try {
            $metaPath = $this->getChunkMetaPathLocal($uploadId);
        } catch (\Exception $e) {
            return null;
        }

        if (!is_file($metaPath)) {
            return null;
        }

        $raw = @file_get_contents($metaPath);
        if ($raw === false || trim($raw) === '') {
            return null;
        }

        $meta = json_decode($raw, true);

        return is_array($meta) ? $meta : null;
    }

    private function deleteChunkMetaLocal($uploadId)
    {
        try {
            $metaPath = $this->getChunkMetaPathLocal($uploadId);
        } catch (\Exception $e) {
            return;
        }

        if (is_file($metaPath)) {
            @unlink($metaPath);
        }
    }

    private function initChunkUploadLocal($originalName, $totalChunks, $totalSize)
    {
        $name = trim((string) $originalName);
        if ($name === '') {
            throw new \RuntimeException('移行先ファイルを選択してください。');
        }
        if (!$this->isSupportedArchiveFileNameLocal($name)) {
            throw new \RuntimeException('移行先ファイルは .tar.gz / .tgz / .tar / .zip を指定してください。');
        }

        $chunkDir = $this->getScannerChunkDirLocal();
        if (!is_dir($chunkDir) && !@mkdir($chunkDir, 0777, true) && !is_dir($chunkDir)) {
            throw new \RuntimeException('チャンクアップロード用ディレクトリを作成できません。');
        }

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $name ?: 'target.tar.gz');
        $id = bin2hex(random_bytes(16));
        $meta = array(
            'id' => $id,
            'file_name' => $safeName,
            'original_name' => $name,
            'file_size' => max(0, (int) $totalSize),
            'total_chunks' => max(1, (int) $totalChunks),
            'uploaded_chunks' => 0,
            'tmp_path' => $chunkDir.'/'.$id.'.part',
            'created_at' => date('c'),
        );
        $this->writeChunkMetaLocal($id, $meta);

        return array(
            'id' => $id,
            'total_chunks' => (int) $meta['total_chunks'],
            'file_name' => (string) $meta['original_name'],
            'file_size' => (int) $meta['file_size'],
        );
    }

    private function appendChunkUploadLocal($uploadId, UploadedFile $chunkFile, $chunkIndex)
    {
        $inputPath = $chunkFile->getPathname();
        if (!is_file($inputPath)) {
            throw new \RuntimeException('チャンクデータが見つかりません。');
        }

        $binary = @file_get_contents($inputPath);
        if (!is_string($binary)) {
            throw new \RuntimeException('チャンクデータの読み込みに失敗しました。');
        }

        return $this->appendChunkUploadBinaryLocal($uploadId, $binary, $chunkIndex);
    }

    private function appendChunkUploadBinaryLocal($uploadId, $chunkBinary, $chunkIndex)
    {
        $meta = $this->readChunkMetaLocal($uploadId);
        if (!is_array($meta)) {
            throw new \RuntimeException('チャンクアップロード情報が見つかりません。');
        }

        $expectedIndex = (int) (isset($meta['uploaded_chunks']) ? $meta['uploaded_chunks'] : 0);
        if ((int) $chunkIndex !== $expectedIndex) {
            throw new \RuntimeException('チャンク順序が不正です。');
        }

        $tmpPath = isset($meta['tmp_path']) ? trim((string) $meta['tmp_path']) : '';
        if ($tmpPath === '') {
            throw new \RuntimeException('一時ファイルパスが不正です。');
        }

        $bytes = @file_put_contents($tmpPath, (string) $chunkBinary, FILE_APPEND);
        if (!is_int($bytes) || $bytes < 0) {
            throw new \RuntimeException('チャンク書き込みに失敗しました。');
        }

        $meta['uploaded_chunks'] = $expectedIndex + 1;
        $this->writeChunkMetaLocal($uploadId, $meta);

        $totalChunks = max(1, (int) (isset($meta['total_chunks']) ? $meta['total_chunks'] : 1));
        $uploadedChunks = min($totalChunks, (int) $meta['uploaded_chunks']);
        $progress = (int) floor(($uploadedChunks / $totalChunks) * 100);

        return array(
            'progress' => min(100, max(0, $progress)),
            'uploaded_chunks' => $uploadedChunks,
            'total_chunks' => $totalChunks,
        );
    }

    private function completeChunkUploadLocal($uploadId)
    {
        $meta = $this->readChunkMetaLocal($uploadId);
        if (!is_array($meta)) {
            throw new \RuntimeException('チャンクアップロード情報が見つかりません。');
        }

        $totalChunks = max(1, (int) (isset($meta['total_chunks']) ? $meta['total_chunks'] : 1));
        $uploadedChunks = (int) (isset($meta['uploaded_chunks']) ? $meta['uploaded_chunks'] : 0);
        if ($uploadedChunks < $totalChunks) {
            throw new \RuntimeException('すべてのチャンクがアップロードされていません。');
        }

        $tmpPath = isset($meta['tmp_path']) ? trim((string) $meta['tmp_path']) : '';
        if ($tmpPath === '' || !is_file($tmpPath)) {
            throw new \RuntimeException('結合ファイルが見つかりません。');
        }

        $uploadDir = $this->getScannerUploadDirLocal();
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            throw new \RuntimeException('保存ディレクトリを作成できません。');
        }

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) (isset($meta['file_name']) ? $meta['file_name'] : 'target.tar.gz'));
        $storedName = sprintf('target_chunk_%s_%s', date('YmdHis'), $safeName);
        $targetPath = $uploadDir.'/'.$storedName;
        if (!@rename($tmpPath, $targetPath)) {
            throw new \RuntimeException('アップロードファイルの確定に失敗しました。');
        }

        $this->deleteChunkMetaLocal($uploadId);

        $realPath = realpath($targetPath);
        if ($realPath === false || !is_file($realPath)) {
            throw new \RuntimeException('移行先ファイルの保存に失敗しました。');
        }

        $token = bin2hex(random_bytes(16));
        $originalName = trim((string) (isset($meta['original_name']) ? $meta['original_name'] : $safeName));
        $size = (int) (@filesize($realPath) ?: 0);
        $updatedTs = (int) (@filemtime($realPath) ?: time());
        $savedMeta = array(
            'token' => $token,
            'path' => $realPath,
            'name' => $originalName !== '' ? $originalName : $safeName,
            'size' => $size,
            'updated_at_ts' => $updatedTs,
            'updated_at' => date('Y-m-d H:i:s', $updatedTs),
        );
        $metaPath = $uploadDir.'/'.$token.'.json';
        @file_put_contents($metaPath, (string) json_encode($savedMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return array(
            'token' => $token,
            'name' => (string) $savedMeta['name'],
            'size' => $size,
            'size_label' => $this->formatFileSize($size),
            'updated_at' => date('Y-m-d H:i:s', $updatedTs),
        );
    }

    private function cancelChunkUploadLocal($uploadId)
    {
        $meta = $this->readChunkMetaLocal($uploadId);
        if (is_array($meta)) {
            $tmpPath = trim((string) (isset($meta['tmp_path']) ? $meta['tmp_path'] : ''));
            if ($tmpPath !== '' && is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }

        $this->deleteChunkMetaLocal($uploadId);
    }

    private function getSavedScannerUploadFile($token)
    {
        $safeToken = $this->sanitizeScannerHexTokenLocal($token);
        if ($safeToken === '') {
            return null;
        }

        $metaPath = $this->getScannerUploadDirLocal().'/'.$safeToken.'.json';
        if (!is_file($metaPath)) {
            return null;
        }

        $raw = @file_get_contents($metaPath);
        if ($raw === false || trim($raw) === '') {
            return null;
        }

        $meta = json_decode($raw, true);
        if (!is_array($meta)) {
            return null;
        }

        $path = trim((string) (isset($meta['path']) ? $meta['path'] : ''));
        if ($path === '' || !is_file($path)) {
            return null;
        }

        $name = trim((string) (isset($meta['name']) ? $meta['name'] : basename($path)));
        $size = (int) (@filesize($path) ?: (isset($meta['size']) ? (int) $meta['size'] : 0));
        $updatedTs = (int) (@filemtime($path) ?: (isset($meta['updated_at_ts']) ? (int) $meta['updated_at_ts'] : 0));
        $updatedAt = $updatedTs > 0 ? date('Y-m-d H:i:s', $updatedTs) : trim((string) (isset($meta['updated_at']) ? $meta['updated_at'] : ''));

        return array(
            'token' => $safeToken,
            'path' => $path,
            'name' => $name,
            'size' => $size,
            'size_label' => $this->formatFileSize($size),
            'updated_at' => $updatedAt,
        );
    }

    private function parseDefaultTargetUploadToken($token)
    {
        $value = trim((string) $token);
        if ($value === '') {
            return '';
        }
        if (strpos($value, 'default:') !== 0) {
            return '';
        }

        return trim(substr($value, 8));
    }

    private function validateFlexibleCsrfToken(Application $app, Request $request)
    {
        $csrf = $app['form.csrf_provider'];
        $name = Constant::TOKEN_NAME;
        $tokenValue = $request->request->get($name);
        if ($tokenValue === null || $tokenValue === '') {
            $tokenValue = $request->query->get($name);
        }

        if (!$csrf->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken($name, $tokenValue))) {
            throw new \RuntimeException('CSRF token is invalid.');
        }

        return true;
    }

    private function getScannerModuleDefinitions()
    {
        $definitions = array();
        foreach ($this->getScannerModuleBlueprintsLocal() as $moduleKey => $blueprint) {
            $tables = array();
            if (!empty($blueprint['families']) && is_array($blueprint['families'])) {
                foreach ($blueprint['families'] as $family) {
                    if (!is_array($family) || empty($family[0])) {
                        continue;
                    }
                    $tables[] = (string) $family[0];
                }
            }

            if (empty($tables)) {
                continue;
            }

            $definitions[$moduleKey] = array(
                'label' => (string) $blueprint['label'],
                'section' => (string) $blueprint['section'],
                'status' => (string) $blueprint['status'],
                'note' => (string) $blueprint['note'],
                'post_notes' => isset($blueprint['post_notes']) && is_array($blueprint['post_notes']) ? $blueprint['post_notes'] : array(),
                'tables' => array_values(array_unique($tables)),
                'valid_tables' => array_values(array_unique($tables)),
                'warning_tables' => array(),
                'all_tables' => array_values(array_unique($tables)),
                'default_checked' => !empty($blueprint['default_checked']),
            );
        }

        return $this->buildVisibleScannerModuleDefinitionsLocal($definitions);
    }

    private function buildScannerModuleDefinitionsForContext($sourceSnapshot, $targetSnapshot)
    {
        $sourceVersion = $this->resolveSnapshotVersionForModulesLocal($sourceSnapshot);
        $targetVersion = $this->resolveSnapshotVersionForModulesLocal($targetSnapshot);
        $sourceSeries = $this->normalizeConstructorSeriesLocal($sourceVersion);
        $targetSeries = $this->normalizeConstructorSeriesLocal($targetVersion);
        if ($sourceSeries === '' || $targetSeries === '') {
            return array();
        }

        $sourceVersionTables = $this->getConstructorTablesForSeriesLocal($sourceSeries);
        $targetVersionTables = $this->getConstructorTablesForSeriesLocal($targetSeries);
        $sourceDatabaseTables = $this->resolveSnapshotTablesForModulesLocal($sourceSnapshot);
        $targetDatabaseTables = $this->resolveSnapshotTablesForModulesLocal($targetSnapshot);
        $sourceAvailable = $this->buildAvailableTableSetLocal(
            $sourceVersionTables,
            $sourceDatabaseTables
        );
        $targetAvailable = $this->buildAvailableTableSetLocal(
            $targetVersionTables,
            $targetDatabaseTables
        );
        $sourceSchemaTables = $this->normalizeCoreTableSchemaMapLocal($sourceDatabaseTables);
        $targetSchemaTables = $this->normalizeCoreTableSchemaMapLocal($targetDatabaseTables);

        if (empty($sourceAvailable) || empty($targetAvailable)) {
            return array();
        }

        $definitions = array();
        foreach ($this->getScannerModuleBlueprintsLocal($sourceSeries, $targetSeries) as $moduleKey => $blueprint) {
            $resolved = $this->resolveScannerModuleTablesLocal(
                isset($blueprint['families']) && is_array($blueprint['families']) ? $blueprint['families'] : array(),
                $sourceAvailable,
                $targetAvailable
            );
            if (empty($resolved['valid_tables'])) {
                continue;
            }

            $status = (string) $blueprint['status'];
            $note = (string) $blueprint['note'];
            if (!empty($resolved['warning_tables']) && $status === 'ok') {
                $status = 'warn';
            }
            if ($note === '' && !empty($resolved['warning_tables'])) {
                $note = '対象バージョン差異のため除外されるテーブルがあります。';
            }

            $schemaWarnings = $this->assessScannerModuleSchemaWarningsLocal(
                isset($resolved['matched_pairs']) && is_array($resolved['matched_pairs']) ? $resolved['matched_pairs'] : array(),
                $sourceSchemaTables,
                $targetSchemaTables
            );
            if (!empty($schemaWarnings)) {
                $status = 'warn';
                $schemaNote = sprintf(
                    'カラム構成の差が大きいテーブルがあります: %s',
                    implode(', ', $schemaWarnings)
                );
                $note = $note !== '' ? trim($note.' '.$schemaNote) : $schemaNote;
            }

            $definitions[$moduleKey] = array(
                'label' => (string) $blueprint['label'],
                'section' => (string) $blueprint['section'],
                'status' => $status,
                'note' => $note,
                'post_notes' => isset($blueprint['post_notes']) && is_array($blueprint['post_notes']) ? $blueprint['post_notes'] : array(),
                'tables' => $resolved['valid_tables'],
                'valid_tables' => $resolved['valid_tables'],
                'warning_tables' => $resolved['warning_tables'],
                'all_tables' => array_values(array_unique(array_merge($resolved['valid_tables'], $resolved['warning_tables']))),
                'display_tables' => $resolved['display_valid_tables'],
                'display_valid_tables' => $resolved['display_valid_tables'],
                'display_warning_tables' => $resolved['display_warning_tables'],
                'display_all_tables' => array_values(array_unique(array_merge($resolved['display_valid_tables'], $resolved['display_warning_tables']))),
                'default_checked' => !empty($blueprint['default_checked']),
            );
        }

        return $this->buildVisibleScannerModuleDefinitionsLocal($definitions);
    }

    private function buildVisibleScannerModuleDefinitionsLocal($moduleDefinitions)
    {
        $visibleDefinitions = array();

        foreach ($moduleDefinitions as $moduleKey => $definition) {
            if (!is_array($definition)) {
                continue;
            }

            $tables = $this->filterHiddenScannerTablesLocal(
                isset($definition['tables']) && is_array($definition['tables']) ? $definition['tables'] : array()
            );
            $validTables = $this->filterHiddenScannerTablesLocal(
                isset($definition['valid_tables']) && is_array($definition['valid_tables']) ? $definition['valid_tables'] : $tables
            );
            $warningTables = $this->filterHiddenScannerTablesLocal(
                isset($definition['warning_tables']) && is_array($definition['warning_tables']) ? $definition['warning_tables'] : array()
            );
            $allTables = $this->filterHiddenScannerTablesLocal(
                isset($definition['all_tables']) && is_array($definition['all_tables'])
                    ? $definition['all_tables']
                    : array_merge($tables, $validTables, $warningTables)
            );
            $displayTables = $this->filterHiddenScannerTablesLocal($tables);
            $displayValidTables = $this->filterHiddenScannerTablesLocal($validTables);
            $displayWarningTables = $this->filterHiddenScannerTablesLocal($warningTables);
            $displayAllTables = $this->filterHiddenScannerTablesLocal(array_merge($displayTables, $displayValidTables, $displayWarningTables));

            if ((string) $moduleKey === 'shop_setting') {
                $displayTables = array_values(array_filter($displayTables, function ($tableName) {
                    return $tableName !== 'dtb_template';
                }));
                $displayValidTables = array_values(array_filter($displayValidTables, function ($tableName) {
                    return $tableName !== 'dtb_template';
                }));
                $displayWarningTables = array_values(array_filter($displayWarningTables, function ($tableName) {
                    return $tableName !== 'dtb_template';
                }));
                $displayAllTables = array_values(array_filter($displayAllTables, function ($tableName) {
                    return $tableName !== 'dtb_template';
                }));
            }

            if (isset($definition['section']) && (string) $definition['section'] === 'master') {
                $displayTables = array();
                $displayValidTables = array();
                $displayWarningTables = array();
                $displayAllTables = array();
            }

            if (empty($tables)) {
                $tables = !empty($validTables) ? $validTables : $allTables;
            }
            if (empty($allTables)) {
                $allTables = array_values(array_unique(array_merge($tables, $validTables, $warningTables)));
            }
            if (empty($displayTables)) {
                $displayTables = !empty($displayValidTables) ? $displayValidTables : $displayAllTables;
            }
            if (empty($displayAllTables)) {
                $displayAllTables = array_values(array_unique(array_merge($displayTables, $displayValidTables, $displayWarningTables)));
            }

            if (empty($tables) && empty($allTables) && (!isset($definition['section']) || (string) $definition['section'] !== 'master')) {
                continue;
            }

            $definition['tables'] = $tables;
            $definition['valid_tables'] = $validTables;
            $definition['warning_tables'] = $warningTables;
            $definition['all_tables'] = $allTables;
            $definition['display_tables'] = $displayTables;
            $definition['display_valid_tables'] = $displayValidTables;
            $definition['display_warning_tables'] = $displayWarningTables;
            $definition['display_all_tables'] = $displayAllTables;

            if (empty($warningTables) && isset($definition['status']) && (string) $definition['status'] === 'warn') {
                $definition['status'] = 'ok';
            }

            $visibleDefinitions[$moduleKey] = $definition;
        }

        return $visibleDefinitions;
    }

    private function filterHiddenScannerTablesLocal($tables)
    {
        $rows = array();

        foreach ((array) $tables as $tableName) {
            $table = strtolower(trim((string) $tableName));
            if ($table === '' || $this->shouldHideScannerTableLocal($table)) {
                continue;
            }

            $rows[] = $table;
        }

        $rows = array_values(array_unique($rows));
        rsort($rows, SORT_STRING);

        return $rows;
    }

    private function shouldHideScannerTableLocal($tableName)
    {
        return strpos(strtolower(trim((string) $tableName)), 'mtb_') === 0;
    }

    private function resolveSnapshotVersionForModulesLocal($snapshot)
    {
        if (!is_array($snapshot)) {
            return '';
        }

        $facts = isset($snapshot['facts']) && is_array($snapshot['facts']) ? $snapshot['facts'] : array();
        $precheck = isset($snapshot['precheck']) && is_array($snapshot['precheck']) ? $snapshot['precheck'] : array();
        $candidates = array(
            isset($snapshot['eccube_version']) ? $snapshot['eccube_version'] : '',
            isset($facts['eccube_version']) ? $facts['eccube_version'] : '',
            isset($precheck['eccube_version']) ? $precheck['eccube_version'] : '',
        );

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function resolveSnapshotTablesForModulesLocal($snapshot)
    {
        if (!is_array($snapshot)) {
            return array();
        }

        $facts = isset($snapshot['facts']) && is_array($snapshot['facts']) ? $snapshot['facts'] : array();
        $precheck = isset($snapshot['precheck']) && is_array($snapshot['precheck']) ? $snapshot['precheck'] : array();
        $candidates = array();
        if (isset($snapshot['database_tables']) && is_array($snapshot['database_tables'])) {
            $candidates[] = $snapshot['database_tables'];
        }
        if (isset($snapshot['db_tables']) && is_array($snapshot['db_tables'])) {
            $candidates[] = $snapshot['db_tables'];
        }
        if (isset($facts['database_tables']) && is_array($facts['database_tables'])) {
            $candidates[] = $facts['database_tables'];
        }
        if (isset($facts['db_tables']) && is_array($facts['db_tables'])) {
            $candidates[] = $facts['db_tables'];
        }
        if (isset($precheck['database_tables']) && is_array($precheck['database_tables'])) {
            $candidates[] = $precheck['database_tables'];
        }
        if (isset($precheck['db_tables']) && is_array($precheck['db_tables'])) {
            $candidates[] = $precheck['db_tables'];
        }

        foreach ($candidates as $candidate) {
            if (!empty($this->normalizeTableSetLocal($candidate))) {
                return $candidate;
            }
        }

        return array();
    }

    private function buildScannerModuleSections($moduleDefinitions)
    {
        $sections = array(
            'migration' => array(),
            'master' => array(),
        );

        foreach ($moduleDefinitions as $moduleKey => $definition) {
            if (!is_array($definition)) {
                continue;
            }

            $section = isset($definition['section']) ? (string) $definition['section'] : 'migration';
            $displayTables = isset($definition['display_tables']) && is_array($definition['display_tables'])
                ? $definition['display_tables']
                : (isset($definition['tables']) && is_array($definition['tables']) ? $definition['tables'] : array());
            if (empty($displayTables) && $section !== 'master') {
                continue;
            }
            if (!isset($sections[$section])) {
                $sections[$section] = array();
            }

            $sections[$section][] = array(
                'key' => (string) $moduleKey,
                'label' => isset($definition['label']) ? (string) $definition['label'] : (string) $moduleKey,
                'status' => isset($definition['status']) ? (string) $definition['status'] : 'ok',
                'note' => isset($definition['note']) ? (string) $definition['note'] : '',
                'tables' => $displayTables,
                'all_tables' => isset($definition['display_all_tables']) && is_array($definition['display_all_tables'])
                    ? $definition['display_all_tables']
                    : (isset($definition['all_tables']) && is_array($definition['all_tables']) ? $definition['all_tables'] : array()),
                'warning_tables' => isset($definition['display_warning_tables']) && is_array($definition['display_warning_tables'])
                    ? $definition['display_warning_tables']
                    : (isset($definition['warning_tables']) && is_array($definition['warning_tables']) ? $definition['warning_tables'] : array()),
                'valid_tables' => isset($definition['display_valid_tables']) && is_array($definition['display_valid_tables'])
                    ? $definition['display_valid_tables']
                    : (isset($definition['valid_tables']) && is_array($definition['valid_tables']) ? $definition['valid_tables'] : array()),
                'post_notes' => isset($definition['post_notes']) && is_array($definition['post_notes']) ? $definition['post_notes'] : array(),
                'default_checked' => !empty($definition['default_checked']),
            );
        }

        return $sections;
    }

    private function getScannerModuleBlueprintsLocal($sourceSeries = '', $targetSeries = '')
    {
        $shopSettingFamilies = $this->buildShopSettingFamiliesLocal($targetSeries);
        $layoutPageFamilies = $this->buildLayoutPageFamiliesLocal($sourceSeries, $targetSeries);

        $legacy30To4xScanAliases = array(
            'delivery_schedule' => array('dtb_delivery_date', 'dtb_delivery_duration'),
            'order_item' => array('dtb_order_detail', 'dtb_order_item'),
            'product_tag_master' => array('mtb_tag', 'dtb_tag'),
            'sale_type' => array('mtb_product_type', 'mtb_sale_type'),
        );

        return array(
            'customer' => array(
                'label' => '会員データ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '会員データを移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_customer'),
                    array('dtb_customer_address'),
                ),
            ),
            'product' => array(
                'label' => '商品データ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '商品・規格・関連データを移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_product'),
                    array('dtb_product_class'),
                    array('dtb_product_stock'),
                    array('dtb_class_name'),
                    array('dtb_class_category'),
                    array('dtb_product_image'),
                    array('dtb_product_category'),
                    $legacy30To4xScanAliases['product_tag_master'],
                    array('dtb_product_tag'),
                ),
            ),
            'category' => array(
                'label' => 'カテゴリ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'カテゴリ情報を移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_category'),
                ),
            ),
            'news_data' => array(
                'label' => 'お知らせ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'お知らせデータを移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_news'),
                ),
            ),
            'order' => array(
                'label' => '受注データ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '受注・受注明細を移行対象として実行します。',
                'post_notes' => array(
                    'dtb_order.order_status_id が NULL の行が含まれる場合は、移行実行時に警告ログへ記録します。移行後に状態確認してください。',
                ),
                'default_checked' => true,
                'families' => array(
                    array('dtb_order'),
                    $legacy30To4xScanAliases['order_item'],
                    array('dtb_shipping'),
                ),
            ),
            'payment_delivery' => array(
                'label' => '支払・配送',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '支払方法・配送方法の設定を移行対象として実行します。',
                'post_notes' => array(
                    'dtb_payment は公開状態を維持したまま移行します。移行後に設定内容を確認してください。',
                    'dtb_delivery は公開状態を維持したまま移行します。移行後に設定内容を確認してください。',
                ),
                'default_checked' => true,
                'families' => array(
                    array('dtb_payment'),
                    array('dtb_payment_option'),
                    array('dtb_delivery'),
                    array('dtb_delivery_fee'),
                    array('dtb_delivery_time'),
                    $legacy30To4xScanAliases['delivery_schedule'],
                ),
            ),
            'tax' => array(
                'label' => '税設定',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '税率・税設定データを移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_tax_rule'),
                ),
            ),
            'tag_favorite' => array(
                'label' => 'お気に入り',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'お気に入り関連データを移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_customer_favorite_product'),
                ),
            ),
            'cart' => array(
                'label' => 'カート',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'カート関連データを移行対象として実行します。',
                'post_notes' => array(
                    'dtb_cart は移行後に全件クリアします。セッション差異で無効なカートが残るのを防ぐためです。',
                    'dtb_cart_item は移行後に全件クリアします。セッション差異で無効なカート明細が残るのを防ぐためです。',
                ),
                'default_checked' => true,
                'families' => array(
                    array('dtb_cart'),
                    array('dtb_cart_item'),
                ),
            ),
            'shop_setting' => array(
                'label' => 'ショップ設定',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'ショップ基本設定を移行対象として実行します。',
                'post_notes' => array(
                    'dtb_template は移行対象外です。移行先EC-CUBEで既存テンプレートを利用するため、取り込みません。',
                ),
                'default_checked' => true,
                'families' => $shopSettingFamilies,
            ),
            'design_page' => array(
                'label' => 'レイアウト・ページ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'レイアウト・ページ設定を移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => $layoutPageFamilies,
            ),
            'mail_template_history' => array(
                'label' => 'メールテンプレート・履歴',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'メールテンプレート・送信履歴を移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_mail_template'),
                    array('dtb_mail_history'),
                ),
            ),
            'order_status_master' => array(
                'label' => '受注帳票・受注ステータス',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '受注帳票・ステータス設定を移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_order_pdf'),
                    array('mtb_order_status'),
                    array('mtb_order_status_color'),
                    array('mtb_customer_order_status'),
                    array('mtb_order_item_type'),
                ),
            ),
            'system_data' => array(
                'label' => 'システム設定データ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => 'システム設定データを移行対象として実行します。',
                'post_notes' => array(
                    'dtb_plugin は移行対象外です。プラグイン本体情報は移行先で管理し、連携プラグインデータは移行先で選択したプラグインに対応する plg_* テーブルのみ移行します。',
                ),
                'default_checked' => true,
                'families' => array(
                    array('dtb_csv'),
                ),
            ),
            'admin_member' => array(
                'label' => '管理者データ',
                'section' => 'migration',
                'status' => 'ok',
                'note' => '管理者データを移行対象として実行します。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('dtb_member'),
                    array('dtb_authority_role'),
                    array('mtb_authority'),
                    array('dtb_login_history'),
                    array('mtb_login_history_status'),
                ),
            ),
            'system_master_warning' => array(
                'label' => 'システムマスタ（差分あり）',
                'section' => 'master',
                'status' => 'warn',
                'note' => '差分があるため、移行後に確認してください。',
                'post_notes' => array(),
                'default_checked' => false,
                'families' => $sourceSeries === '3.0' && in_array($targetSeries, array('4.0', '4.1', '4.2', '4.3'), true)
                    ? array()
                    : array(
                        array('mtb_product_status'),
                        $legacy30To4xScanAliases['sale_type'],
                        array('mtb_tax_display_type'),
                        array('mtb_tax_type'),
                    ),
            ),
            'system_master_valid' => array(
                'label' => 'システムマスタ（一致）',
                'section' => 'master',
                'status' => 'ok',
                'note' => '差分がないため、通常は安全に移行できます。',
                'post_notes' => array(),
                'default_checked' => true,
                'families' => array(
                    array('mtb_pref'),
                    array('mtb_country'),
                    array('mtb_job'),
                    array('mtb_csv_type'),
                    array('mtb_customer_status'),
                    array('mtb_device_type'),
                    array('mtb_page_max'),
                    array('mtb_product_list_max'),
                    array('mtb_product_list_order_by'),
                    array('mtb_work'),
                    array('mtb_sex'),
                ),
            ),
        );
    }

    private function buildShopSettingFamiliesLocal($targetSeries)
    {
        $families = array(
            array('dtb_base_info'),
            array('dtb_template'),
        );

        // 4.0 には dtb_calendar / dtb_tradelaw が存在しません。
        if ($targetSeries === '' || in_array($targetSeries, array('4.1', '4.2', '4.3'), true)) {
            $families[] = array('dtb_calendar');
        }

        // dtb_tradelaw は 4.2 以降でのみ確認対象とします。
        if ($targetSeries === '' || in_array($targetSeries, array('4.2', '4.3'), true)) {
            $families[] = array('dtb_tradelaw');
        }

        return $families;
    }

    private function buildLayoutPageFamiliesLocal($sourceSeries, $targetSeries)
    {
        // EC-CUBE 3.0 -> 4.x ではページ/レイアウト管理の構造差が大きいため、
        // バックアップ側のスキャン対象から外し、テーマ移行セクションで扱います。
        if ($sourceSeries === '3.0' && in_array($targetSeries, array('4.0', '4.1', '4.2', '4.3'), true)) {
            return array();
        }

        return array(
            array('dtb_layout'),
            array('dtb_page'),
            array('dtb_page_layout'),
            array('dtb_block'),
            array('dtb_block_position'),
        );
    }

    private function resolveScannerModuleTablesLocal($families, $sourceAvailable, $targetAvailable)
    {
        $validTables = array();
        $warningTables = array();
        $matchedPairs = array();
        $displayValidTables = array();
        $displayWarningTables = array();

        foreach ($families as $family) {
            if (!is_array($family) || empty($family)) {
                continue;
            }

            $normalizedFamily = array();
            foreach ($family as $candidate) {
                $candidateName = strtolower(trim((string) $candidate));
                if ($candidateName !== '') {
                    $normalizedFamily[] = $candidateName;
                }
            }
            if (empty($normalizedFamily)) {
                continue;
            }
            $displayTable = (string) end($normalizedFamily);

            $resolvedSource = '';
            foreach ($normalizedFamily as $name) {
                if ($name !== '' && isset($sourceAvailable[$name])) {
                    $resolvedSource = $name;
                    break;
                }
            }
            if ($resolvedSource === '') {
                continue;
            }

            $hasTarget = false;
            $resolvedTarget = '';
            foreach ($normalizedFamily as $name) {
                if ($name !== '' && isset($targetAvailable[$name])) {
                    $hasTarget = true;
                    $resolvedTarget = $name;
                    break;
                }
            }

            if ($hasTarget) {
                $validTables[] = $resolvedSource;
                if ($resolvedTarget !== '') {
                    $matchedPairs[$resolvedSource] = $resolvedTarget;
                }
                if ($displayTable !== '') {
                    $displayValidTables[] = $displayTable;
                }
            } else {
                $warningTables[] = $resolvedSource;
                if ($displayTable !== '') {
                    $displayWarningTables[] = $displayTable;
                }
            }
        }

        return array(
            'valid_tables' => array_values(array_unique($validTables)),
            'warning_tables' => array_values(array_unique($warningTables)),
            'matched_pairs' => $matchedPairs,
            'display_valid_tables' => array_values(array_unique($displayValidTables)),
            'display_warning_tables' => array_values(array_unique($displayWarningTables)),
        );
    }

    private function normalizeCoreTableSchemaMapLocal($tables)
    {
        $rows = array();
        if (!is_array($tables)) {
            return $rows;
        }

        foreach ($tables as $tableName => $tableDef) {
            if (!is_string($tableName) || !is_array($tableDef)) {
                continue;
            }

            $columns = isset($tableDef['columns']) && is_array($tableDef['columns']) ? $tableDef['columns'] : array();
            $normalizedColumns = array();
            foreach ($columns as $columnName => $columnDef) {
                if (!is_string($columnName)) {
                    continue;
                }

                $name = strtolower(trim((string) $columnName));
                if ($name !== '') {
                    $normalizedColumns[$name] = true;
                }
            }

            $table = strtolower(trim((string) $tableName));
            if ($table !== '' && !empty($normalizedColumns)) {
                $rows[$table] = array('columns' => $normalizedColumns);
            }
        }

        return $rows;
    }

    private function assessScannerModuleSchemaWarningsLocal($matchedPairs, $sourceSchemaTables, $targetSchemaTables)
    {
        $warnings = array();

        foreach ((array) $matchedPairs as $sourceTable => $targetTable) {
            $sourceName = strtolower(trim((string) $sourceTable));
            $targetName = strtolower(trim((string) $targetTable));
            if ($sourceName === '' || $targetName === '') {
                continue;
            }

            $sourceColumns = isset($sourceSchemaTables[$sourceName]['columns']) && is_array($sourceSchemaTables[$sourceName]['columns'])
                ? array_keys($sourceSchemaTables[$sourceName]['columns'])
                : array();
            $targetColumns = isset($targetSchemaTables[$targetName]['columns']) && is_array($targetSchemaTables[$targetName]['columns'])
                ? array_keys($targetSchemaTables[$targetName]['columns'])
                : array();
            if (empty($sourceColumns) || empty($targetColumns)) {
                continue;
            }

            $commonColumns = array_intersect($sourceColumns, $targetColumns);
            if (empty($commonColumns)) {
                $warnings[] = sprintf('%s → %s', $sourceName, $targetName);
                continue;
            }

            $compatibility = count($commonColumns) / max(1, min(count($sourceColumns), count($targetColumns)));
            if ($compatibility < 0.4) {
                $warnings[] = sprintf('%s → %s', $sourceName, $targetName);
            }
        }

        return array_values(array_unique($warnings));
    }

    private function buildAvailableTableSetLocal($versionTables, $databaseTables)
    {
        $versionSet = $this->normalizeTableSetLocal($versionTables);
        $databaseSet = $this->normalizeTableSetLocal($databaseTables);

        if (!empty($versionSet) && !empty($databaseSet)) {
            return array_intersect_key($databaseSet, $versionSet);
        }
        if (!empty($databaseSet)) {
            return $databaseSet;
        }

        return $versionSet;
    }

    private function normalizeTableSetLocal($tables)
    {
        $rows = array();
        if (!is_array($tables)) {
            return $rows;
        }

        foreach ($tables as $tableName => $tableDef) {
            if (!is_int($tableName) && (is_scalar($tableName) || (is_object($tableName) && method_exists($tableName, '__toString')))) {
                $name = strtolower(trim((string) $tableName));
                if ($name !== '') {
                    $rows[$name] = true;
                }
                continue;
            }

            if (is_scalar($tableDef) || (is_object($tableDef) && method_exists($tableDef, '__toString'))) {
                $name = strtolower(trim((string) $tableDef));
                if ($name !== '') {
                    $rows[$name] = true;
                }
            }
        }

        return $rows;
    }

    private function normalizeConstructorSeriesLocal($version)
    {
        $value = trim((string) $version);
        if ($value === '') {
            return '';
        }

        if (preg_match('/(\d+\.\d+)/', $value, $matches) !== 1) {
            return '';
        }

        $series = $matches[1];
        if (strpos($series, '4.') === 0) {
            if (in_array($series, array('4.0', '4.1', '4.2', '4.3'), true)) {
                return $series;
            }

            return '4.3';
        }

        if (strpos($series, '3.') === 0) {
            return '3.0';
        }

        return $series;
    }

    private function getConstructorTablesForSeriesLocal($series)
    {
        $sets = $this->loadConstructorTableSetsLocal();

        return isset($sets[$series]) && is_array($sets[$series]) ? $sets[$series] : array();
    }

    private function loadConstructorTableSetsLocal()
    {
        static $cache = null;
        if (is_array($cache)) {
            return $cache;
        }

        // Generated from backups/constructor DB.csv.
        $cache = array(
            '4.3' => array(
                'dtb_authority_role',
                'dtb_base_info',
                'dtb_block',
                'dtb_block_position',
                'dtb_calendar',
                'dtb_cart',
                'dtb_cart_item',
                'dtb_category',
                'dtb_class_category',
                'dtb_class_name',
                'dtb_csv',
                'dtb_customer',
                'dtb_customer_address',
                'dtb_customer_favorite_product',
                'dtb_delivery',
                'dtb_delivery_duration',
                'dtb_delivery_fee',
                'dtb_delivery_time',
                'dtb_layout',
                'dtb_login_history',
                'dtb_mail_history',
                'dtb_mail_template',
                'dtb_member',
                'dtb_news',
                'dtb_order',
                'dtb_order_item',
                'dtb_order_pdf',
                'dtb_page',
                'dtb_page_layout',
                'dtb_payment',
                'dtb_payment_option',
                'dtb_plugin',
                'dtb_product',
                'dtb_product_category',
                'dtb_product_class',
                'dtb_product_image',
                'dtb_product_stock',
                'dtb_product_tag',
                'dtb_shipping',
                'dtb_tag',
                'dtb_tax_rule',
                'dtb_template',
                'dtb_tradelaw',
                'mtb_authority',
                'mtb_country',
                'mtb_csv_type',
                'mtb_customer_order_status',
                'mtb_customer_status',
                'mtb_device_type',
                'mtb_job',
                'mtb_login_history_status',
                'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
                'mtb_page_max',
                'mtb_pref',
                'mtb_product_list_max',
                'mtb_product_list_order_by',
                'mtb_product_status',
                'mtb_rounding_type',
                'mtb_sale_type',
                'mtb_sex',
                'mtb_tax_display_type',
                'mtb_tax_type',
                'mtb_work',
            ),
            '4.2' => array(
                'dtb_authority_role',
                'dtb_base_info',
                'dtb_block',
                'dtb_block_position',
                'dtb_calendar',
                'dtb_cart',
                'dtb_cart_item',
                'dtb_category',
                'dtb_class_category',
                'dtb_class_name',
                'dtb_csv',
                'dtb_customer',
                'dtb_customer_address',
                'dtb_customer_favorite_product',
                'dtb_delivery',
                'dtb_delivery_duration',
                'dtb_delivery_fee',
                'dtb_delivery_time',
                'dtb_layout',
                'dtb_login_history',
                'dtb_mail_history',
                'dtb_mail_template',
                'dtb_member',
                'dtb_news',
                'dtb_order',
                'dtb_order_item',
                'dtb_order_pdf',
                'dtb_page',
                'dtb_page_layout',
                'dtb_payment',
                'dtb_payment_option',
                'dtb_plugin',
                'dtb_product',
                'dtb_product_category',
                'dtb_product_class',
                'dtb_product_image',
                'dtb_product_stock',
                'dtb_product_tag',
                'dtb_shipping',
                'dtb_tag',
                'dtb_tax_rule',
                'dtb_template',
                'dtb_tradelaw',
                'mtb_authority',
                'mtb_country',
                'mtb_csv_type',
                'mtb_customer_order_status',
                'mtb_customer_status',
                'mtb_device_type',
                'mtb_job',
                'mtb_login_history_status',
                'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
                'mtb_page_max',
                'mtb_pref',
                'mtb_product_list_max',
                'mtb_product_list_order_by',
                'mtb_product_status',
                'mtb_rounding_type',
                'mtb_sale_type',
                'mtb_sex',
                'mtb_tax_display_type',
                'mtb_tax_type',
                'mtb_work',
            ),
            '4.1' => array(
                'dtb_authority_role',
                'dtb_base_info',
                'dtb_block',
                'dtb_block_position',
                'dtb_calendar',
                'dtb_cart',
                'dtb_cart_item',
                'dtb_category',
                'dtb_class_category',
                'dtb_class_name',
                'dtb_csv',
                'dtb_customer',
                'dtb_customer_address',
                'dtb_customer_favorite_product',
                'dtb_delivery',
                'dtb_delivery_duration',
                'dtb_delivery_fee',
                'dtb_delivery_time',
                'dtb_layout',
                'dtb_login_history',
                'dtb_mail_history',
                'dtb_mail_template',
                'dtb_member',
                'dtb_news',
                'dtb_order',
                'dtb_order_item',
                'dtb_order_pdf',
                'dtb_page',
                'dtb_page_layout',
                'dtb_payment',
                'dtb_payment_option',
                'dtb_plugin',
                'dtb_product',
                'dtb_product_category',
                'dtb_product_class',
                'dtb_product_image',
                'dtb_product_stock',
                'dtb_product_tag',
                'dtb_shipping',
                'dtb_tag',
                'dtb_tax_rule',
                'dtb_template',
                'mtb_authority',
                'mtb_country',
                'mtb_csv_type',
                'mtb_customer_order_status',
                'mtb_customer_status',
                'mtb_device_type',
                'mtb_job',
                'mtb_login_history_status',
                'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
                'mtb_page_max',
                'mtb_pref',
                'mtb_product_list_max',
                'mtb_product_list_order_by',
                'mtb_product_status',
                'mtb_rounding_type',
                'mtb_sale_type',
                'mtb_sex',
                'mtb_tax_display_type',
                'mtb_tax_type',
                'mtb_work',
            ),
            '4.0' => array(
                'dtb_authority_role',
                'dtb_base_info',
                'dtb_block',
                'dtb_block_position',
                'dtb_cart',
                'dtb_cart_item',
                'dtb_category',
                'dtb_class_category',
                'dtb_class_name',
                'dtb_csv',
                'dtb_customer',
                'dtb_customer_address',
                'dtb_customer_favorite_product',
                'dtb_delivery',
                'dtb_delivery_duration',
                'dtb_delivery_fee',
                'dtb_delivery_time',
                'dtb_layout',
                'dtb_mail_history',
                'dtb_mail_template',
                'dtb_member',
                'dtb_news',
                'dtb_order',
                'dtb_order_item',
                'dtb_order_pdf',
                'dtb_page',
                'dtb_page_layout',
                'dtb_payment',
                'dtb_payment_option',
                'dtb_plugin',
                'dtb_product',
                'dtb_product_category',
                'dtb_product_class',
                'dtb_product_image',
                'dtb_product_stock',
                'dtb_product_tag',
                'dtb_shipping',
                'dtb_tag',
                'dtb_tax_rule',
                'dtb_template',
                'mtb_authority',
                'mtb_country',
                'mtb_csv_type',
                'mtb_customer_order_status',
                'mtb_customer_status',
                'mtb_device_type',
                'mtb_job',
                'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
                'mtb_page_max',
                'mtb_pref',
                'mtb_product_list_max',
                'mtb_product_list_order_by',
                'mtb_product_status',
                'mtb_rounding_type',
                'mtb_sale_type',
                'mtb_sex',
                'mtb_tax_display_type',
                'mtb_tax_type',
                'mtb_work',
            ),
            '3.0' => array(
                'dtb_authority_role',
                'dtb_base_info',
                'dtb_block',
                'dtb_block_position',
                'dtb_category',
                'dtb_category_count',
                'dtb_category_total_count',
                'dtb_class_category',
                'dtb_class_name',
                'dtb_csv',
                'dtb_customer',
                'dtb_customer_address',
                'dtb_customer_favorite_product',
                'dtb_delivery',
                'dtb_delivery_date',
                'dtb_delivery_fee',
                'dtb_delivery_time',
                'dtb_help',
                'dtb_mail_history',
                'dtb_mail_template',
                'dtb_member',
                'dtb_news',
                'dtb_order',
                'dtb_order_detail',
                'dtb_page_layout',
                'dtb_payment',
                'dtb_payment_option',
                'dtb_plugin',
                'dtb_plugin_event_handler',
                'dtb_product',
                'dtb_product_category',
                'dtb_product_class',
                'dtb_product_image',
                'dtb_product_stock',
                'dtb_product_tag',
                'dtb_shipment_item',
                'dtb_shipping',
                'dtb_tax_rule',
                'dtb_template',
                'mtb_authority',
                'mtb_country',
                'mtb_csv_type',
                'mtb_customer_order_status',
                'mtb_customer_status',
                'mtb_db',
                'mtb_device_type',
                'mtb_disp',
                'mtb_job',
                'mtb_order_status',
                'mtb_order_status_color',
                'mtb_page_max',
                'mtb_pref',
                'mtb_product_list_max',
                'mtb_product_list_order_by',
                'mtb_product_type',
                'mtb_sex',
                'mtb_tag',
                'mtb_taxrule',
                'mtb_work',
                'mtb_zip',
            ),
        );

        return $cache;
    }

    private function countTableRows($table)
    {
        try {
            $conn = $this->app['orm.em']->getConnection();
            $stmt = $conn->executeQuery('SELECT COUNT(*) FROM '.$conn->getDatabasePlatform()->quoteIdentifier($table));
            if (method_exists($stmt, 'fetchColumn')) {
                return (int) $stmt->fetchColumn(0);
            }
            $row = $stmt->fetch();
            if (is_array($row)) {
                return (int) reset($row);
            }
        } catch (\Exception $e) {
        }

        return 0;
    }

    private function fetchPreviewRows($table, $limit)
    {
        $rows = array();

        try {
            $conn = $this->app['orm.em']->getConnection();
            $sql = 'SELECT * FROM '.$conn->getDatabasePlatform()->quoteIdentifier($table);
            if ((int) $limit > 0) {
                $sql .= ' LIMIT '.(int) $limit;
            }
            $stmt = $conn->executeQuery($sql);
            while (($row = $this->fetchAssocRow($stmt)) !== false) {
                $rows[] = $this->normalizePreviewRow($row);
            }
        } catch (\Exception $e) {
        }

        return $rows;
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

    private function normalizePreviewRow($row)
    {
        if (!is_array($row)) {
            return array();
        }

        foreach ($row as $key => $value) {
            if ($value === null || is_scalar($value)) {
                continue;
            }
            if (is_object($value) && method_exists($value, '__toString')) {
                $row[$key] = (string) $value;
                continue;
            }
            $row[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $row;
    }

    private function enrichThemeDependencyScanWithPluginNames($scan, $plugins)
    {
        $rows = is_array($scan) ? $scan : array();
        $references = isset($rows['references']) && is_array($rows['references']) ? $rows['references'] : array();
        if (empty($references)) {
            return $rows;
        }

        $pluginNameMap = $this->buildPluginCodeNameMapLocal($plugins);
        foreach ($references as $index => $reference) {
            if (!is_array($reference)) {
                continue;
            }
            $pluginCode = trim((string) (isset($reference['plugin_code']) ? $reference['plugin_code'] : ''));
            if ($pluginCode === '' || !empty($reference['plugin_name'])) {
                continue;
            }
            $normalizedCode = strtolower($pluginCode);
            if (!isset($pluginNameMap[$normalizedCode])) {
                continue;
            }
            $references[$index]['plugin_name'] = $pluginNameMap[$normalizedCode];
        }
        $rows['references'] = $references;

        return $rows;
    }

    private function buildPluginCodeNameMapLocal($plugins)
    {
        $rows = array();
        foreach ((array) $plugins as $plugin) {
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

    private function formatFileSize($bytes)
    {
        if ($bytes <= 0) {
            return '0 MB';
        }

        $mb = $bytes / 1024 / 1024;
        if ($mb > 1000) {
            return ceil($mb / 1024).' GB';
        }

        return ceil($mb).' MB';
    }

    /**
     * @param int $issueCount
     *
     * @return string
     */
    private function buildThemeDependencyWarningNote($issueCount)
    {
        return sprintf(
            "Twigブロックの依存警告があります。%d件のTwigファイルを確認してください。移行時に、必要なプラグインが移行先サイトにインストールされていない場合はエラーが発生する可能性があります。エラーを防ぐには、移行先サイトに必要なプラグインをインストールしてください。詳細は「詳細を見る」を確認してください。\n※ コードを修正するか、問題のブロックを削除した後、バックアップを再作成して再度スキャンしてください。",
            (int) $issueCount
        );
    }

    private function resolveThemeIssueEditUrl($file)
    {
        $path = str_replace('\\', '/', trim((string) $file));
        if ($path === '' || !preg_match('#(?:^|/)Block/([^/]+)\.twig$#i', $path, $matches)) {
            return '';
        }
        if (!is_object($this->app) || !method_exists($this->app, 'url') || !isset($this->app['eccube.repository.block'])) {
            return '';
        }

        $fileName = trim((string) $matches[1]);
        if ($fileName === '') {
            return '';
        }

        try {
            $criteria = array('file_name' => $fileName);
            if (isset($this->app['eccube.repository.master.device_type'])) {
                $DeviceType = $this->app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
                if ($DeviceType) {
                    $criteria['DeviceType'] = $DeviceType;
                }
            }
            $Block = $this->app['eccube.repository.block']->findOneBy($criteria);
            if (!is_object($Block) || !method_exists($Block, 'getId')) {
                unset($criteria['DeviceType']);
                $Block = $this->app['eccube.repository.block']->findOneBy($criteria);
            }
            if (!is_object($Block) || !method_exists($Block, 'getId')) {
                return '';
            }

            return (string) $this->app->url('admin_content_block_edit', array('id' => $Block->getId()));
        } catch (\Exception $e) {
            return '';
        }
    }
}
