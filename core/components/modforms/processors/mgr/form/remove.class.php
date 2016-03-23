<?php

/**
 * Remove a MfForm
 */
class modMfFormRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'MfForm';
    public $languageTopics = array('modforms');
    public $permission = '';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeRemove()
    {

        return parent::beforeRemove();
    }

    /** {@inheritDoc} */
    public function afterRemove()
    {
        $form = $this->object->get('id');

        $q = $this->modx->newQuery('MfFormOption');
        $q->command('DELETE');
        $q->where(array('form' => $form));
        $q->prepare();
        $q->stmt->execute();

        return true;
    }
}

return 'modMfFormRemoveProcessor';