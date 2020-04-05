<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root namespace for datatransfer classes in
    | your application.
    |
    */

    'class_namespace' => 'App\\DataTransfer',

    /*
    |--------------------------------------------------------------------------
    | Usage of getter/setter
    |--------------------------------------------------------------------------
    |
    | This value specifies whether or not the corresponding getter/setter
    | shall be used when calling from... or to... methods.
    | You can overwrite the usage in each item class
    |
    */

    'use_setter' => true,

    'use_getter' => true,

    /*
     |-------------------------------------------------------------------------
     | Primitives
     |-------------------------------------------------------------------------
     |
     | Those are the property types that will be considered as 'not to be
     | handled as DataTransferItem or DatatransferCollection classes'.
     |
     | @see property declaration with @var in PHP-Doc-Block or checkout the
     | \AndrePolo\DataTransfer\Tests\ExampleTransferItem.php
     |
     */

    'primitives' => [
        '',
        'bool',
        'boolean',
        'int',
        'integer',
        'number',
        'float',
        'double',
        'string',
        'array',
        'object',
        'resource',
        'mixed',
        'callable',
    ],
];