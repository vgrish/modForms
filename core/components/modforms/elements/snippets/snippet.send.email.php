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

$propkey = $modx->getOption('propkey', $scriptProperties);

$properties = $ModForms->getProperties($propkey);
$properties = (is_string($properties) AND strpos($properties, '{') === 0)
    ? $ModForms->Tools->fromJson($properties)
    : $properties;

$selector = $modx->getOption('selector', $properties);

$rows = array();
$q = $modx->newQuery('MfForm');
$q->innerJoin('MfFormOption', 'Selectors', 'Selectors.form = MfForm.id');
$q->innerJoin('MfFormOption', 'Emails', 'Emails.form = MfForm.id');
$q->where(array(
    'Selectors.key'   => 'selector',
    'Selectors.value' => $selector,
    'Emails.key'      => 'email',
    'MfForm.active'   => true
));

$q->sortby('MfForm.rank', 'ASC');
$q->select("MfForm.subject, MfForm.body, Emails.value as email");
$q->limit(0);
if ($q->prepare() && $q->stmt->execute()) {
    $rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (function_exists('fastcgi_finish_request')) {
    echo $ModForms->success();
    session_write_close();
    fastcgi_finish_request();
}

foreach ($rows as $row) {
    $body = $ModForms->getOption('body', $row);
    $email = $ModForms->getOption('email', $row);
    if (!$body OR !$email) {
        continue;
    }

    $subject = $ModForms->getOption('subject', $row, $modx->getOption('site_name'), true);
    if ($modChunk = $modx->getObject('modChunk', $body)) {
        $body = $ModForms->getChunk($modChunk->get('name'), $scriptProperties);
    }

    $ModForms->sendEmail($email, $subject, $body);
}

return $ModForms->success();



