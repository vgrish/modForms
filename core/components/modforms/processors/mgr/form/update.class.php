<?php

/**
 * Update an MfForm
 */
class modMfFormUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'MfForm';
    public $classKey = 'MfForm';
    public $languageTopics = array('modforms');
    public $permission = '';

    /** {@inheritDoc} */
    public function beforeSet()
    {
        /** @var $id */
        /** @var $name */
        foreach (array('id', 'name') as $k) {
            ${$k} = trim($this->getProperty($k));
            if (empty(${$k})) {
                $this->modx->error->addField($k, $this->modx->lexicon('modforms_err_ae'));
            }
        }

        if ($this->modx->getCount($this->classKey, array(
            'name'  => $name,
            'id:!=' => $id
        ))
        ) {
            $this->modx->error->addField('name', $this->modx->lexicon('modforms_err_ae'));
        }

        return parent::beforeSet();
    }

    /**
     * @return bool
     */
    public function afterSave()
    {
        $this->modx->exec("UPDATE {$this->modx->getTableName('MfFormOption')}
            SET form = {$this->object->get('id')} WHERE
            form = 0
		");

        return true;
    }

}

return 'modMfFormUpdateProcessor';
