<?php

/**
 * The home manager controller for ModForms.
 *
 */
class ModFormsAllManagerController extends ModFormsMainController
{
    /* @var ModForms $ModForms */
    public $ModForms;


    /**
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array())
    {
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('modforms');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');

        $this->addJavascript($this->ModForms->config['jsUrl'] . 'mgr/misc/tools.js');
        $this->addJavascript($this->ModForms->config['jsUrl'] . 'mgr/misc/combo.js');

        $this->addJavascript($this->ModForms->config['jsUrl'] . 'mgr/all/all.panel.js');

        $this->addJavascript($this->ModForms->config['jsUrl'] . 'mgr/form/form.grid.js');
        $this->addJavascript($this->ModForms->config['jsUrl'] . 'mgr/form/form.window.js');

        $script = 'Ext.onReady(function() {
			MODx.add({ xtype: "modforms-panel-all"});
		});';
        $this->addHtml("<script type='text/javascript'>{$script}</script>");

    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->ModForms->config['templatesPath'] . 'all.tpl';
    }
}