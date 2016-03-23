<?php

class modMfFormOptionUpdateProcessor extends modObjectProcessor
{

    public $classKey = 'MfFormOption';

    const MODE_REMOVE = 'remove';
    const MODE_ADD = 'add';

    /** {@inheritDoc} */
    public function process()
    {
        $form = intval($this->getProperty('form'));
        $value = trim($this->getProperty('value'));
        $key = trim($this->getProperty('key'));

        $mode = trim($this->getProperty('mode'));

        if (!$key OR !$mode) {
            return $this->failure('');
        }

        switch ($mode) {
            case self::MODE_ADD:
                $sql = "INSERT INTO {$this->modx->getTableName($this->classKey)}
                    (`form`,`key`,`value`) VALUES ('$form','$key','$value')
                    ON DUPLICATE KEY UPDATE `form` = '$form';";
                $this->modx->exec($sql);
                break;
            case self::MODE_REMOVE:
                $q = $this->modx->newQuery('MfFormOption');
                $q->command('DELETE');
                $q->where(array('form' => $form, 'key' => $key, 'value' => $value));
                $q->prepare();
                $q->stmt->execute();
                break;
        }
 
        return $this->success('');
    }

}

return 'modMfFormOptionUpdateProcessor';