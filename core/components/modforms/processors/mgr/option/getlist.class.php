<?php

/**
 * Get a list of MfFormOption
 */
class modMfFormOptionGetListProcessor extends modObjectProcessor
{
    public $classKey = 'MfFormOption';
    public $defaultSortField = 'form';

    /** {@inheritDoc} */
    public function process()
    {
        $query = trim($this->getProperty('query'));
        $limit = trim($this->getProperty('limit', 0));

        $key = trim($this->getProperty('key'));
        $form = trim($this->getProperty('form'));
        $all = $this->getProperty('all');

        $c = $this->modx->newQuery($this->classKey);
        $c->sortby('value', 'ASC');
        $c->select('value');
        $c->groupby('value');

        if (!$all OR $form) {
            $c->where(array(
                'key'  => $key,
                'form' => $form
            ));
        } else {
            $c->where(array(
                'key' => $key,
            ));
        }

        $c->limit($limit);
        if (!empty($query)) {
            $c->where(array('value:LIKE' => "%{$query}%"));
        }
        $found = false;
        if ($c->prepare() AND $c->stmt->execute()) {
            $array = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($array as $v) {
                if ($v['value'] == $query) {
                    $found = true;
                }
            }
        } else {
            $array = array();
        }
        if (!$found AND !empty($query)) {
            $array = array_merge_recursive(array(array('value' => $query)), $array);
        }

        return $this->outputArray($array);
    }

}

return 'modMfFormOptionGetListProcessor';