<?php

class modChunkGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'modChunk';
    public $classKey = 'modChunk';
    public $languageTopics = array('chunk');

    /** {@inheritDoc} */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($this->getProperty('combo')) {
            $c->select('id,name');
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

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                "{$this->objectType}.name:LIKE"           => "%{$query}%",
                "OR:{$this->objectType}.description:LIKE" => "%{$query}%",
            ));
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function prepareRow(xPDOObject $object)
    {
        if ($this->getProperty('combo')) {
            $array = array(
                'id'          => $object->get('id'),
                'name'        => $object->get('name'),
                'description' => $object->get('description'),
            );
        } else {
            $array = $object->toArray();
        }

        return $array;
    }
}

return 'modChunkGetListProcessor';