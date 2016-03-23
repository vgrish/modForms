<?php

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

if (!$ModForms) {
    return 'Could not load ModForms class!';
}
$ModForms->initialize($modx->context->key, $scriptProperties);

/* clear json $scriptProperties */
foreach (array('validation', 'inputmask', 'modal') as $k) {
    $scriptProperties[$k] = $ModForms->Tools->fromJson($scriptProperties[$k]);
}

$action = $scriptProperties['action'] = $modx->getOption('action', $scriptProperties, 'mf.send.email', true);
$selector = $scriptProperties['selector'] = $modx->getOption('selector', $scriptProperties, '#form', true);
$tplForm = $scriptProperties['tplForm'] = $modx->getOption('tplForm', $scriptProperties, '', true);
$tplModal = $scriptProperties['tplModal'] = $modx->getOption('tplModal', $scriptProperties, '', true);
$objectName = $scriptProperties['objectName'] = $modx->getOption('objectName', $scriptProperties, 'ModFormsForm', true);
$validation = $scriptProperties['validation'] = $modx->getOption('validation', $scriptProperties, array(), true);
$inputmask = $scriptProperties['inputmask'] = $modx->getOption('inputmask', $scriptProperties, array(), true);
$modal = $scriptProperties['modal'] = $modx->getOption('modal', $scriptProperties, array(), true);

$_selector = $scriptProperties['_selector'] = ltrim($selector, '#.');
$propkey = $scriptProperties['propkey'] = $modx->getOption('propkey', $scriptProperties,
    sha1(serialize($scriptProperties)), true);

$ModForms->saveProperties($scriptProperties);
$ModForms->Tools->loadResourceJsCss($scriptProperties);

$output[] = empty($tplForm) ? '' : $ModForms->getChunk($tplForm, $scriptProperties);
$output[] = empty($tplModal) ? '' : $ModForms->getChunk($tplModal, $scriptProperties);
$output = implode("\n", $output);

if (!empty($tplWrapper) AND (!empty($wrapIfEmpty) OR !empty($output))) {
    $output = $ModForms->getChunk($tplWrapper, array('output' => $output));
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
