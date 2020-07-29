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
        'bool',
        'boolean',
        'int',
        'integer',
        'float',
        'double',
        'string',
        'array',
        'object',
        'resource',
        'mixed',
        'callable',
    ],

    /*
     |-------------------------------------------------------------------------
     | Strict
     |-------------------------------------------------------------------------
     | This option indicates whether filling items from array or json should
     | only be possible when the corresponding values match to what is defined
     | in the doc block
     |
     | for example: if you have set @var array in your doc block and you try to
     | pass a string, with:
     | strict: true --> it will not be possible
     | strict: false --> it will be possible
     */

    'strict' => false
];