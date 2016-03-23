<?php

/**
 * Get an MfForm
 */
class modMfFormGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'MfForm';
    public $classKey = 'MfForm';
    public $languageTopics = array('modforms');
    public $permission = '';

    /** {@inheritDoc} */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }

    /**
     * @return array|string
     */
    public function cleanup()
    {
        $array = $this->object->toArray();

        $options = $this->modx->fromJSON($this->getProperty('options', "{}"));
        foreach ($options as $key) {
            $rows = array();
            $c = $this->modx->newQuery('MfFormOption');
            $c->sortby('value', 'ASC');
            $c->select('value');
            $c->groupby('value');
            $c->where(array(
                'key'  => $key,
                'form' => $this->object->get('id')
            ));
            $c->limit(0);
            if ($c->prepare() && $c->stmt->execute()) {
                $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $array = array_merge($array, array($key => $rows));
        }

        return $this->success('', $array);
    }

}

return 'modMfFormGetProcessor';