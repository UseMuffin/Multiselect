<?php
namespace Multiselect\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Multiselect\Model\Behavior\MultiselectBehavior;

/**
 * Multiselect\Model\Behavior\MultiselectBehavior Test Case
 */
class MultiselectBehaviorTest extends TestCase
{

    public $fixtures = [
        'plugin.Multiselect.Articles',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Articles = TableRegistry::get('Multiselect.Articles');
        $this->Articles->addBehavior('Multiselect.Multiselect', [
            'featured' => [
                'limit' => 2,
                'scope' => ['author_id'],
                'order' => [
                    'approved' => 'ASC',
                    'published' => 'ASC',
                ],
            ],
        ]);
        $this->Behavior = $this->Articles->behaviors()->Multiselect;
    }

    public function tearDown()
    {
        $this->Articles->removeBehavior('Multiselect');
        parent::tearDown();
    }

    public function testUnselectedSave()
    {
        $data = [
            'featured' => false,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-03 00:00:00',
        ];
        $article = $this->Articles->newEntity($data);
        $this->Articles->save($article);

        $result = $this->Articles
            ->find('list', ['valuefield' => 'id'])
            ->where(['featured' => true, 'author_id' => 1])
            ->toarray();

        $expected = [1 => 1, 5 => 5];
        $this->assertequals($expected, $result);
    }

    public function testSelectedSave()
    {
        $data = [
            'featured' => true,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-03 00:00:00',
        ];
        $article = $this->Articles->newEntity($data);
        $this->Articles->save($article);

        $result = $this->Articles
            ->find('list', ['valuefield' => 'id'])
            ->where(['featured' => true, 'author_id' => 1])
            ->toarray();

        $expected = [5 => 5, 6 => 6];
        $this->assertequals($expected, $result);
    }

    public function testCount()
    {
        $count = $this->Articles->find()->where(['featured' => true, 'author_id' => 1])->count();
        $this->assertequals($count, 2);

        $article = $this->Articles->get(2);
        $article->featured = true;
        $this->Articles->save($article);
        $count = $this->Articles->find()->where(['featured' => true, 'author_id' => 1])->count();
        $this->assertequals($count, 2);

        $article = $this->Articles->get(3);
        $article->featured = true;
        $this->Articles->save($article);
        $count = $this->Articles->find()->where(['featured' => true, 'author_id' => 1])->count();
        $this->assertequals($count, 2);
    }

    public function testScope()
    {
        $article = $this->Articles->get(4);
        $article->approved = false;
        $this->Articles->save($article);

        $data = [
            'featured' => true,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-03 00:00:00',
        ];
        $article = $this->Articles->newEntity($data);
        $this->Articles->save($article);


        $result = $this->Articles
            ->find('list', ['valuefield' => 'id'])
            ->where(['featured' => true, 'author_id' => 1])
            ->toarray();

        $expected = [5 => 5, 6 => 6];
        $this->assertequals($expected, $result);
    }

    public function testOrder()
    {
        $article = $this->Articles->get(5);
        $article->approved = false;
        $this->Articles->save($article);

        $data = [
            'featured' => true,
            'approved' => true,
            'author_id' => 1,
            'published' => '2015-09-03 00:00:00',
        ];
        $article = $this->Articles->newEntity($data);
        $this->Articles->save($article);

        $result = $this->Articles
            ->find('list', ['valuefield' => 'id'])
            ->where(['featured' => true, 'author_id' => 1])
            ->toarray();

        $expected = [1 => 1, 6 => 6];
        $this->assertequals($expected, $result);
    }
}
