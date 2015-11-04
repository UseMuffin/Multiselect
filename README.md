[![Build Status](https://travis-ci.org/gintonicweb/multiselect.svg)](https://travis-ci.org/gintonicweb/multiselect)
[![Coverage Status](https://coveralls.io/repos/gintonicweb/multiselect/badge.svg?branch=master&service=github)](https://coveralls.io/github/gintonicweb/multiselect?branch=master)
[![Packagist](https://img.shields.io/packagist/dt/gintonicweb/multiselect.svg)]()
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

# Multiselect plugin for CakePHP

Toggle, select or multiselect a database field based on a set of conditions

## Installation

Using [composer](http://getcomposer.org).

```
composer require gintonicweb/multiselect:dev-master
```

Load the plugin in ```bootstrap.php``` like:

```
Plugin::load('Multiselect');
```

## Usage

### Example 1

In a playlist, a single song can be play at the time. Whenever a song is marked
as playing, all of the other songs are marked as not playing.

```
CREATE table songs(
    id int(10) unsigned NOT NULL auto_increment,
    title varchar(255) NOT NULL,
    playing tinyint(1) NOT NULL,
);
```

Load the behavior in your model ```SongsTable.php```:

```
$this->addBehavior('Multiselect.Multiselect', ['field' => 'playing']);
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

Load the behavior in ```ArticlesTable.php```:

```
$this->addBehavior('Multiselect.Multiselect', [
    'field' => 'featured',
    'limit' => 3,
    'matchingFields' => ['author_id'],
    'order' => [ 
        'modified' => 'ASC',
    ],
]);
```

When the same author marks a fourth article as featured, one other article must 
be un-fetured. In this case we choose the the least-recently modified.

### Config options


```
$this->addBehavior('Multiselect.Multiselect', [

    // The field that is used as a select, must be a boolean
    'field' => 'featured',

    // By default, the active field is marked as true and the inactive fields as 
    // are marked false. Make it the opposite by setting this option to false
    'state' => true, 

    // If you want the selects to be restricted to sub-groups within your talbe,
    // use this field to mark the grouping fields
    'matchingFields' => ['author_id'],

    // You can allow more than one option to be selected simultaneously,
    // then you also need to define the order in which we unselect the items when
    // the maximum limit of selected items is reached.
    'limit' => 2,
    'order' => [
        'approved' => 'ASC',
        'modified' => 'ASC',
    ],
]);
```
