<?php
namespace Muffin\Multiselect\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Muffin\Multiselect\Model\Behavior\MultiselectBehavior;

/**
 * Muffin\Multiselect\Model\Behavior\MultiselectBehavior Test Case
 */
class MultiselectBehaviorTest extends TestCase
{

    public $fixtures = [
        'plugin.Muffin/Multiselect.Articles',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Articles = TableRegistry::get('Muffin/Multiselect.Articles');
        $this->Articles->addBehavior('Muffin/Multiselect.Multiselect', [
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
        parent::tearDown();
        TableRegistry::clear();
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
            ->find()
            ->where(['featured' => true, 'author_id' => 1])
            ->extract('id')
            ->toarray();

        $expected = [1, 5];
        $this->assertequals($expected, $result);
    }

    /**
     * 1 and 5 are selected, insert new selected record
     */
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
            ->find()
            ->where(['featured' => true, 'author_id' => 1])
            ->extract('id')
            ->toarray();

        $expected = [5, 9];
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
            ->find()
            ->where(['featured' => true, 'author_id' => 1])
            ->extract('id')
            ->toarray();

        $expected = [5, 9];
        $this->assertequals($expected, $result);
    }

    /**
     * Articles 1 and 5 featured
     * Change article 5 for unapproved so that it's the first to be unselected
     */
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
            ->find()
            ->where(['featured' => true, 'author_id' => 1])
            ->extract('id')
            ->toarray();

        $expected = [1, 9];
        $this->assertequals($expected, $result);
    }

    public function testCorrection()
    {
        $article = $this->Articles->get(6);
        $article->dirty('id', true);
        $this->Articles->save($article);

        $result = $this->Articles
            ->find()
            ->where(['featured' => true, 'author_id' => 3])
            ->extract('id')
            ->toarray();

        $expected = [6, 8];
        $this->assertequals($expected, $result);
    }
}
