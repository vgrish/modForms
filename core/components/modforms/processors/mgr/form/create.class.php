<?php

/**
 * Create an MfForm
 */
class modMfFormCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'MfForm';
    public $classKey = 'MfForm';
    public $languageTopics = array('modforms');
    public $permission = '';

    /** {@inheritDoc} */
    public function beforeSet()
    {
        /** @var $name */
        foreach (array('name') as $k) {
            ${$k} = trim($this->getProperty($k));
            if (empty(${$k})) {
                $this->modx->error->addField($k, $this->modx->lexicon('modforms_err_ae'));
            }
        }
        if ($this->modx->getCount($this->classKey, array(
            'name' => $name,
        ))
        ) {
            $this->modx->error->addField('name', $this->modx->lexicon('modforms_err_ae'));
        }

        return parent::beforeSet();
    }

    /** {@inheritDoc} */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'rank' => $this->modx->getCount($this->classKey)
        ));

        return parent::beforeSave();
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

return 'modMfFormCreateProcessor';