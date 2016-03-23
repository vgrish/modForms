<?php

$settings = array();

$tmp = array(


    //временные
/*
    'assets_path' => array(
        'value' => '{base_path}modforms/assets/components/modforms/',
        'xtype' => 'textfield',
        'area'  => 'modforms_temp',
    ),
    'assets_url'  => array(
        'value' => '/modforms/assets/components/modforms/',
        'xtype' => 'textfield',
        'area'  => 'modforms_temp',
    ),
    'core_path'   => array(
        'value' => '{base_path}modforms/core/components/modforms/',
        'xtype' => 'textfield',
        'area'  => 'modforms_temp',
    ),
       */
    //временные


);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'modforms_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
