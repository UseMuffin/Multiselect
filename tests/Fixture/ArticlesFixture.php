<?php
namespace Muffin\Multiselect\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class ArticlesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'featured' => ['type' => 'boolean', 'null' => false, 'default' => 0, 'comment' => '', 'precision' => null, 'fixed' => null],
        'approved' => ['type' => 'boolean', 'null' => false, 'default' => 0, 'comment' => '', 'precision' => null, 'fixed' => null],
        'author_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'published' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            // 1
            'featured' => true,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-01 00:00:00',
        ],
        [
            // 2
            'featured' => false,
            'approved' => false,
            'author_id' => 1,
            'published' => '2015-09-02 00:00:00',
        ],
        [
            // 3
            'featured' => false,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-03 00:00:00',
        ],
        [
            // 4
            'featured' => true,
            'approved' => true,
            'author_id' => 2,
            'published' => '2015-09-03 00:00:00',
        ],
        [
            // 5
            'featured' => true,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-04 00:00:00',
        ],
        [
            // 6
            'featured' => true,
            'approved' => true,
            'author_id' => 3,
            'published' => '2015-09-04 00:00:00',
        ],
        [
            // 7
            'featured' => true,
            'approved' => true,
            'author_id' => 3,
            'published' => '2015-09-05 00:00:00',
        ],
        [
            // 8
            'featured' => true,
            'approved' => true,
            'author_id' => 3,
            'published' => '2015-09-06 00:00:00',
        ],
    ];
}
