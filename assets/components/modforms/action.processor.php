<?php
/*
ini_set('display_errors', 1);
ini_set('error_reporting', -1);*/

switch (true) {
    case empty($_REQUEST['modforms']):
    case empty($_SERVER['HTTP_X_REQUESTED_WITH']):
    case $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest':
        @session_write_close();
        die('Access denied');
        break;
    default:
        break;
}

define('MODX_API_MODE', true);
define('MODX_ACTION_MODE', true);

$productionIndex = dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$developmentIndex = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
if (file_exists($productionIndex)) {
    /** @noinspection PhpIncludeInspection */
    require_once $productionIndex;
} else {
    /** @noinspection PhpIncludeInspection */
    require_once $developmentIndex;
}
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;
$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {
    $modx->switchContext($ctx);
    $modx->user = null;
    $modx->getUser($ctx);
}

/** @var array $scriptProperties */
$corePath = $modx->getOption('modforms_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modforms/');
/** @var ModForms $ModForms */
$ModForms = $modx->getService(
    'ModForms',
    'ModForms',
    $corePath . 'model/modforms/',
    array(
        'core_path' => $corePath
    )
);
if ($modx->error->hasError() OR !($ModForms instanceof ModForms)) {
    @session_write_close();
    die('Error');
}

$ModForms->initialize($ctx);
$ModForms->config['processorsPath'] = $ModForms->config['processorsPath'] . 'web/';
if (!$response = $ModForms->runProcessor(strtolower($_REQUEST['modforms']), $_REQUEST)) {
    $response = $modx->toJSON(array(
        'success' => false,
        'code'    => 401,
    ));
}
@session_write_close();
echo $response;