# Multiselect plugin for CakePHP

Toggle, select or multiselect a database field based on a set of conditions

## Installation

Using [composer](http://getcomposer.org).

```
composer require gintonicweb/multiselect:dev-master
```

And load the plugin in ```bootstrap.php``` like:

```
Plugin::load('Multiselect');
```

## Usage

### Example 1

A single song can be playing. Whenever a song is marked as playing, all of 
the other songs are marked as not playing.

```
CREATE table song(
    id int(10) unsigned NOT NULL auto_increment,
    name varchar(255) NOT NULL,
    playing tinyint(1) NOT NULL,
);
```

Load the behavior in your model ```SongsTable.php```:

```
$this->addBehavior('Multiselect', ['field' => 'playing']);
```

The field must be a boolean.


### Example 2

Each author can have 3 featured articles. 

```
CREATE table articles (
    id int(10) unsigned NOT NULL auto_increment,
    author_id int(10) unsigned NOT NULL,
    content text NOT NULL,
    featured tinyint(1) NOT NULL,
    modified datetime DEFAULT NULL
);
```

We load the behavior in ```ArticlesTable.php```:

```
$this->addBehavior('Multiselect', [
    'field' => 'featured',
    'limit' => 3,
    'matchingFields' => ['author_id'],
    'order' => [ 
        'modified' => 'ASC',
    ],
]);
```

When the same author marks a fourth article as featured, the article that we
have modified the least-recently will loose it's 'featured' status.

### Config options


```
$this->addBehavior('Multiselect', [

    // The field that is used as a select, must be a boolean
    'field' => 'featured',

    // The active field is marked as true and the inactive fields as false
    // you can do the opposite by making this option false
    'state' => true, 

    // If you want the selects to be restricted to sub-groups of your talbe,
    // use this field to mark the grouping fields
    'matchingFields' => ['author_id'],

    // If you want to allow more than one option to be selected at the time,
    // then you also need to define the order in which to unselect them when
    // we reach the maximum limit of selected fields
    'limit' => 2,
    'order' => [
        'approved' => 'ASC',
        'modified' => 'ASC',
    ],
]);
```
