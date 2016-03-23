<?php

/**
 * Class ModFormsMainController
 */
abstract class ModFormsMainController extends modExtraManagerController
{
    /** @var ModForms $ModForms */
    public $ModForms;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('modforms_core_path', null,
            $this->modx->getOption('core_path') . 'components/modforms/');
        require_once $corePath . 'model/modforms/modforms.class.php';

        $this->ModForms = new ModForms($this->modx);
        $this->ModForms->initialize($this->modx->context->key);

        $this->addCss($this->ModForms->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->ModForms->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->ModForms->config['jsUrl'] . 'mgr/modforms.js');

        $config = $this->ModForms->config;
        $config['connector_url'] = $this->ModForms->config['connectorUrl'];
        $config['fields_grid_forms'] = $this->ModForms->Tools->getFieldsGridForms();

        $this->addHtml("<script type='text/javascript'>modforms.config={$this->modx->toJSON($config)}</script>");

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('modforms:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends ModFormsMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'all';
    }
}