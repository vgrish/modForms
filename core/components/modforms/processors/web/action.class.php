<?php

class modFormsActionProcessor extends modProcessor
{
    public $languageTopics = array('modforms');
    public $permission = '';

    /** @var ModForms $ModForms */
    public $ModForms;

    /** @var array $prop */
    public $prop;
    /** @var array $data */
    public $data;

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        /** @var array $scriptProperties */
        $corePath = $this->modx->getOption('modforms_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modforms/');
        /** @var ModForms $ModForms */
        $this->ModForms = $this->modx->getService(
            'ModForms',
            'ModForms',
            $corePath . 'model/modforms/',
            array(
                'core_path' => $corePath
            )
        );
        if (!$this->ModForms) {
            return 'Could not load ModForms class!';
        }
        $this->ModForms->initialize($this->modx->context->key);

        $propKey = $this->getProperty('propkey');
        if (empty($propKey)) {
            return $this->ModForms->lexicon('err_propkey_ns');
        }

        $this->prop = $this->getProperty('properties', $this->ModForms->getProperties($propKey));
        $this->prop = (is_string($this->prop) AND strpos($this->prop, '{') === 0)
            ? $this->modx->fromJSON($this->prop)
            : $this->prop;
        if (empty($this->prop)) {
            return $this->ModForms->lexicon('err_properties_ns');
        }

        $this->data = array();

        return true;
    }

    public function process()
    {
        return $this->success('', $this->data);
    }

}

return 'modFormsActionProcessor';