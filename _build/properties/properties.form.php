<?php

$properties = array();

$tmp = array(
    'tplForm' => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'tplModal' => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'selector' => array(
        'type' => 'textfield',
        'value' => '.mfform',
    ),
    'objectName'    => array(
        'type'  => 'textfield',
        'value' => 'ModFormsForm',
    ),
    'action'     => array(
        'type'  => 'textfield',
        'value' => 'mf.send.email',
    ),
    'frontendCss'   => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]css/web/default.css',
    ),
    'frontendJs'    => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]js/web/default.js',
    ),
    'actionUrl'     => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]action.php',
    ),
    'validation' => array(
        'type'  => 'textarea',
        'value' => '{"rules":""}',
    ),
    'inputmask' => array(
        'type'  => 'textarea',
        'value' => '{}',
    ),
    'modal' => array(
        'type'  => 'textarea',
        'value' => '{"center":true}',
    ),

);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name'    => $k,
            'desc'    => PKG_NAME_LOWER . '_prop_' . $k,
            'lexicon' => PKG_NAME_LOWER . ':properties',
        ), $v
    );
}

return $properties;