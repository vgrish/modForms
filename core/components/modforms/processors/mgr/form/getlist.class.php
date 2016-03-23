<?php

/**
 * Get a list of MfForm
 */
class modMfFormGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'MfForm';
    public $classKey = 'MfForm';
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = array('default', 'modforms');
    public $permission = '';

    /** {@inheritDoc} */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $class = $this->getProperty('class');
        if ($class) {
            $c->where(array('class' => $class));
        }

        $id = $this->getProperty('id');
        if (!empty($id) AND $this->getProperty('combo')) {
            $q = $this->modx->newQuery($this->objectType);
            $q->where(array('id!=' => $id));
            $q->select('id');
            $q->limit($this->getProperty('limit') - 1);
            $q->prepare();
            $q->stmt->execute();
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $ids = array_merge_recursive(array($id), $ids);
            $c->where(array(
                "{$this->objectType}.id:IN" => $ids
            ));
        }

        $active = $this->getProperty('active');
        if ($active != '') {
            $c->where("{$this->objectType}.active={$active}");
        }

        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where(array(
                'name:LIKE'           => "%{$query}%",
                'OR:subject:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('modforms_all')
                )
            ), $array);
        }
        if ($this->getProperty('novalue')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('modforms_no')
                )
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['results'][] = $this->prepareArray($row);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), true));
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        $beforeQuery = $this->beforeQuery();
        if ($beforeQuery !== true) {
            return $this->failure($beforeQuery);
        }
        $data = $this->getData();

        return $this->outputArray($data['results'], $data['total']);
    }

    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareArray(array $array)
    {

        $options = $this->modx->fromJSON($this->getProperty('options', "{}"));
        foreach ($options as $key) {
            $rows = array();
            $c = $this->modx->newQuery('MfFormOption');
            $c->sortby('value', 'ASC');
            $c->select('value');
            $c->groupby('value');
            $c->where(array(
                'key'  => $key,
                'form' => $array['id']
            ));
            $c->limit(0);
            if ($c->prepare() && $c->stmt->execute()) {
                $rows = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            $array = array_merge($array, array($key => $rows));
        }


        $icon = 'icon';
        $array['actions'] = array();

        // Edit
        $array['actions'][] = array(
            'cls'    => '',
            'icon'   => "$icon $icon-edit green",
            'title'  => $this->modx->lexicon('modforms_action_update'),
            'action' => 'update',
            'button' => true,
            'menu'   => true,
        );

        // sep
        $array['actions'][] = array(
            'cls'    => '',
            'icon'   => '',
            'title'  => '',
            'action' => 'sep',
            'button' => false,
            'menu'   => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-toggle-off red",
                'title'  => $this->modx->lexicon('modforms_action_active'),
                'action' => 'active',
                'button' => true,
                'menu'   => true,
            );
        } else {
            $array['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-toggle-on green",
                'title'  => $this->modx->lexicon('modforms_action_inactive'),
                'action' => 'inactive',
                'button' => true,
                'menu'   => true,
            );
        }

        // sep
        $array['actions'][] = array(
            'cls'    => '',
            'icon'   => '',
            'title'  => '',
            'action' => 'sep',
            'button' => false,
            'menu'   => true,
        );
        // Remove
        $array['actions'][] = array(
            'cls'    => '',
            'icon'   => "$icon $icon-trash-o red",
            'title'  => $this->modx->lexicon('modforms_action_remove'),
            'action' => 'remove',
            'button' => true,
            'menu'   => true,
        );

        return $array;
    }

}

return 'modMfFormGetListProcessor';