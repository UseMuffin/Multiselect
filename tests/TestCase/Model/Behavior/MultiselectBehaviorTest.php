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
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Articles = TableRegistry::get('Multiselect.Articles');
        $this->Articles->addBehavior('Multiselect.Multiselect', [
            'field' => 'featured',
            'limit' => 2,
            'order' => [
                'approved' => 'ASC',
                'published' => 'ASC',
            ],
        ]);
        $this->Behavior = $this->Articles->behaviors()->Multiselect;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Multiselect);
        parent::tearDown();
    }

    /**
     * Test beforeSave method
     *
     * @return void
     */
    public function testBeforeSave()
    {
        $result = $this->Articles
            ->find('list', ['valuefield' => 'id'])
            ->where(['featured' => true])
            ->toarray();

        $expected = [1 => 1, 2 => 2];
        $this->assertequals($expected, $result);

        $data = [
            'featured' => true,
            'approved' => true,
            'published' => '2015-09-03 00:00:00',
        ];
        $article = $this->Articles->newEntity($data);
        $this->Articles->save($article);

        $result = $this->Articles
            ->find('list', ['valuefield' => 'id'])
            ->where(['featured' => true])
            ->toarray();

        $expected = [4 => 4, 1 => 1];
        $this->assertequals($expected, $result);
    }

    /**
     * Test getCount method
     *
     * @return void
     */
    public function testGetCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getConditions method
     *
     * @return void
     */
    public function testGetConditions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test unselect method
     *
     * @return void
     */
    public function testUnselect()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
