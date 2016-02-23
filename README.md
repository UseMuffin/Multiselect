# Multiselect

[![Build Status](https://img.shields.io/travis/UseMuffin/multiselect/master.svg?style=flat-square)](https://travis-ci.org/UseMuffin/multiselect)
[![Coverage](https://img.shields.io/codecov/c/github/UseMuffin/Multiselect.svg?style=flat-square)](https://codecov.io/github/UseMuffin/Multiselect)
[![Total Downloads](https://img.shields.io/packagist/dt/muffin/multiselect.svg?style=flat-square)](https://packagist.org/packages/muffin/multiselect)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Toggle, select or multiselect a database field based on a set of conditions

## Install

Using [Composer][composer]:

```
composer require muffin/multiselect:1.0.x-dev
```

You then need to load the plugin. You can use the shell command:

```
bin/cake plugin load Muffin/Multiselect
```

or by manually adding statement shown below to your app's `config/bootstrap.php`:

```php
Plugin::load('Muffin/Multiselect');
```

## Usage

In a playlist, a single song can be play at the time. Whenever a song is marked
as playing, all of the other songs are marked as not playing. for this we use the
boolean field `playing`.

```
CREATE table songs(
    id int(10) unsigned NOT NULL auto_increment,
    title varchar(255) NOT NULL,
    playing tinyint(1) NOT NULL,
);
```

Load the behavior in your model ```SongsTable.php```:

```
$this->addBehavior('Multiselect.Multiselect', ['playing']);
```

More complex use cases can be covered like the following. Where only 2 articles can
be marked as `featured` at the time for the same author. When a third article is marked
as featured, one of the 3 articles gets unfeatured. In this case, ordered by the
least view count and by modification date.

```
$this->addBehavior('Multiselect.Multiselect', [
    'featured' => [
        'state' => true, 
        'scope' => ['author_id'],
        'limit' => 2,
        'order' => [
            'view_count' => 'ASC',
            'modified' => 'ASC',
        ],
    ],
]);
```

## Configuration

- `[field_name]` The name of the field used as select
- `state` Either mark the active field as `true` or `false`
- `scope` Selections can be grouped by fields defined here
- `limit` Maximum number of selected elements
- `order` Order in which to unselect fields when the limit is exceeded

## Patches & Features

* Fork
* Mod, fix
* Test - this is important, so it's not unintentionally broken
* Commit - do not mess with license, todo, version, etc. (if you do change any, bump them into commits of
their own that I can ignore when I pull)
* Pull request - bonus point for topic branches

To ensure your PRs are considered for upstream, you MUST follow the [CakePHP coding standards][standards].

## Bugs & Feedback

http://github.com/usemuffin/multiselect/issues

## License

Copyright (c) 2015, [Use Muffin][muffin] and licensed under [The MIT License][mit].

[cakephp]:http://cakephp.org
[composer]:http://getcomposer.org
[mit]:http://www.opensource.org/licenses/mit-license.php
[muffin]:http://usemuffin.com
[standards]:http://book.cakephp.org/3.0/en/contributing/cakephp-coding-conventions.html
