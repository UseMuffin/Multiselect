<?php
namespace Multiselect\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Tooglable behavior
 */
class MultiselectBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'state' => true,
        'field' => null,
        'limit' => 1,
        'order' => ['modified' => 'ASC'],
        'matchingFields' => [],
    ];

    public function __construct(Table $table, array $config = [])
    {
        $config = $this->_resolveMethodAliases(
            'implementedFinders',
            $this->_defaultConfig,
            $config
        );
        $config = $this->_resolveMethodAliases(
            'implementedMethods',
            $this->_defaultConfig,
            $config
        );
        $this->_table = $table;
        $this->configShallow($config);
        $this->initialize($config);
    }

    public function beforeSave(Event $event, Entity $entity)
    {
        $conditions = $this->getConditions($entity);
        $count = $this->getCount($conditions);

        $config = $this->config();
        if ($count < $config['limit']) {
            return;
        }

        $this->unselect($count, $conditions);
    }

    public function getCount($conditions)
    {
        return $this->_table->find()
            ->where($conditions)
            ->count();
    }

    public function getConditions(Entity $entity)
    {
        $config = $this->config();
        $conditions = [$config['field'] => $config['state']];
        foreach ($config['matchingFields'] as $field) {
            $conditions = array_merge($conditions, [
                $field => $entity->get($field)
            ]);
        }
        return $conditions;
    }

    public function unselect($count, $conditions)
    {
        $config = $this->config();
        $trimmedRows = $this->_table->find()
            ->select([$this->_table->primaryKey()])
            ->where($conditions)
            ->order($config['order'])
            ->limit($count - $config['limit'] + 1)
            ->hydrate(false);

        $ids = Hash::extract($trimmedRows->toArray(), '{n}.' . $this->_table->primaryKey());
        $this->_table->query()->update()
            ->set([$config['field'] => !$config['state']])
            ->where([$this->_table->primaryKey() . ' IN' => $ids])
            ->execute();
    }
}
