# pdoque
PDO CRUD library

## Usage
Create a class that extends pdoque.

Specify the tablename as first argument if model name not the same in database.

Specify the primary key field name if it is different from 'id'.
```php
class Person extends pdoque{
    public function __construct() {
        parent::__construct($tableName,$primaryKey);
    }
}
```
## Select
Takes 6 arguments.
- fields as array
- conditions as array
- condition operator as string ('AND' or 'OR'. Default 'AND')
- field names to order
- order type as string ('ASC' or 'DESC'. Default 'ASC')
- limit as integer

```php
$personObj=new Person();
//get all people with no condition
$people=$personObj->getAll();
//get people whose lastname is Doe
$people=$personObj->getAll([],["lastname"=>"Doe"]);
//get first 5 firstnames of people with lastname Doe and order them by their lastnames in descending order
$people=$personObj->getAll(["firstname"],["lastname"=>"Doe"],"AND","lastname","DESC",5);
```

## Select By Id
Takes 2 arguments.
- id value as integer
- fields as array

```php
$personObj=new Person();
$person=$personObj->getById(23,["firstname"]);
```

## Insert
Takes 1 argument.
- fields as array

```php
$personObj=new Person();
$people=$personObj->insert(["firstname"=>"John","lastname"=>"Doe"]);
```

## Update
Takes 3 arguments.
- fields as array
- conditions as array
- condition operator as string ('AND' or 'OR'. Default 'AND')

```php
$personObj=new Person();
$updatePeople=$personObj->update(["firstname"=>"Jane","lastname"=>"Doe"],["lastname"=>"Doe"]);
```

## Update By Id
Takes 2 arguments.
- id value as integer
- fields as array

```php
$personObj=new Person();
$updatePeopleById=$personObj->updateById(23,["lastname"=>"Doe"]);
```
## Delete
Takes 2 arguments.
- conditions as array
- condition operator as string ('AND' or 'OR'. Default 'AND')

```php
$personObj=new Person();
$deletePeople=$personObj->delete(["lastname"=>"Doe"]);
```
## Delete By Id
Takes 1 argument.
- id value as integer

```php
$personObj=new Person();
$deletePersonById=$personObj->deleteById(23);
```
## Custom Query
Takes 3 arguments.
- query as string
- parameters as array
- rows expected flag as boolean (Default true)

```php
$personObj=new Person();
$query="SELECT * FROM person WHERE lastname=:lastname AND age=:age";
$params=[
    "lastname"=>"Doe",
    "age"=>23
];
$customQuery=$personObj->customQuery($query,$params);
```
