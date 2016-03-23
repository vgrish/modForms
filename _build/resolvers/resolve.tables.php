<?php


/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modelPath = $modx->getOption('modforms_core_path', null,
                $modx->getOption('core_path') . 'components/modforms/') . 'model/';
        $modx->addPackage('modforms', $modelPath);

        $manager = $modx->getManager();
        $objects = array(
            'MfForm',
            'MfFormOption'
        );
        foreach ($objects as $tmp) {
            $manager->createObjectContainer($tmp);
        }
        break;


    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;
