<?php
$xpdo_meta_map['MfFormOption']= array (
  'package' => 'modforms',
  'version' => '1.1',
  'table' => 'modforms_form_options',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'form' => NULL,
    'key' => NULL,
    'value' => NULL,
  ),
  'fieldMeta' => 
  array (
    'form' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'unique_key' => 
    array (
      'alias' => 'unique_key',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'form' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'value' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
