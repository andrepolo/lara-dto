# laravel-easy-dto
Add data transfer objects to your Laravel app to transfer structured data

## Installation

To install the package to your laravel app, just require it using composer:
```
composer require andrepolo/laravel-easy-dto
```

To modify the config file, for example to change the default namespace of your 
data transfer item classes, you can eighter use the artisan command:
```
php artisan vendor:publish --tag=datatransfer-config
```
or use the command that comes with the package:
```
php artisan datatransfer:publish:config
```
After executing this you will find a new config file in your `app/config` directory: `datatransfer.php`

## Creating Items and Collections
#### Creating a DataTransferItem class
The recommended way is to use the artisan command that ships with this package:
```
php artisan datatransfer:make:item
```
This command will create a new `DataTransferItem` with the given name and store it in the 
namespace which is defined in the config file `datatransfer.php` in the namespace section.
If you pass a name like this `Subfolder\\NestedItem`, the class `NestedItem` will be 
stored in the folder `Subfolder` under the namespace.
By default the namespace is `App\DataTransfer`. 
The command takes care if a class with the same name already exists in your directory. 
To force the command to overwrite this existing class (which is most unlikely in most cases) 
you can add the `-f` option in the make command.
This is how this class would look like if you used the name `MyFirstDatatransferItem`:
```php
<?php

namespace App\DataTransfer;

use AndrePolo\DataTransfer\DataTransferItem;

class MyFirstDatatransferItem extends DataTransferItem
{
    // add your properties here
}
```

Another way is to simply create a new class by coppying the above example. This class has to extend 
`Andrepolo\DataTransfer\DataTransferItem`

Create a new Instance of a datatransfer class
```php
// simly with new up a class
$item = new ExampleItem();

// using laravels service container
$item = app()->make(ExampleItem::class);

// or if you registered your class with an alias
$item = app()->make('class_alias');
```
#### Creating a DataTransferCollection class
Also, use the command from this package:
```
php artisan datatransfer:make:collection
```
This command is creating a `DataTransferCollection` with the given name. Here you 
have the option `-f` again to force the command to create the new class. 
Every `DataTransferCollection` contains a list of `DataTransferItems`.
You can automatically create the corresponding `DataTransferItem` by using 
the `-i` or `--item` option. With this option set the command creates an item 
with the same name as the collection but with the suffix `Item`. 

For example if you pass the name ``MyFirstDatatransfer``, a item with the name
``MyFirstDatatransferItem`` is created. Same if you pass ``MyFirstDatatransferCollection`` as the name   
 
This is how the class would look like:
```php
<?php

namespace App\DataTransfer;

use AndrePolo\DataTransfer\DataTransferCollection;

class MyFirstDatatransferCollection extends DataTransferCollection
{
    protected $itemClass = MyFirstDatatransferItem::class;
}
```

## Usage
You can fill it by passing the data directly to the constructor:
```php
$item = new ExampleItem($data);
```
or just new it up and then call the `fromArray` method:
```php
$item = new ExampleItem();
$item->fromArray($data);
```
Using this method fills all matching properties with the given values. 
The most important feature of this method is it automatically fills all nested 
instances of `DataTransferItem` or `DataTransferCollection`. 

### Setter and Getter
If you have a setter method defined for your property it will be automatically 
used in the `fromArray` method. You can overwrite this behavior globally  
by set the config var `use_setter` to false or for a specific class by overwriting
the `useSetter` method. 
```php
protected function useSetter()
{
    return false;
}
```
For getter methods it is working the same way as described above.

### Strict mode
This is disabled by default. You can enable it globally by setting it to `true` 
in the config file.
This option indicates whether filling items from array or json should
only be possible when the corresponding values match to what is defined
in the doc block.
See config file for more information. 

### Schema
Mostly your data-transfer-objects will be nested structures. Therefore it might 
be usefull to quickly share a schema of your DTO to somebody you work with.
You can call the `schema` method on your object to recieve the properties as an 
array. When you use the `jsonSchema` method, it will be a json object.
