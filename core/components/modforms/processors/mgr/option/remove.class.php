<?php

class modMfFormOptionRemoveProcessor extends modObjectProcessor
{

    public $classKey = 'MfFormOption';

    /** {@inheritDoc} */
    public function process()
    {
        $form = intval($this->getProperty('form'));
        $key = trim($this->getProperty('key'));

        if (!$key OR !$form) {
            return $this->failure('');
        }

        $q = $this->modx->newQuery($this->classKey);
        $q->command('DELETE');
        $q->where(array('form' => $form, 'key' => $key));
        $q->prepare();
        $q->stmt->execute();
 
        return $this->success('');
    }

}

return 'modMfFormOptionRemoveProcessor';