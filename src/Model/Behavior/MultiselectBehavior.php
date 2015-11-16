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
        'scope' => [],
    ];

    /**
     * Constructor
     *
     * The default class constructor is only overrided because this behavior
     * relies on the order in the config array to stay the same. This is why
     * configShallow is used.
     *
     * @param \Cake\ORM\Table $table The table this behavior is attached to.
     * @param array $config The config for this behavior.
     */
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

    /**
     * BeforeSave
     *
     * @param \Cake\Event\Event $event Event instance.
     * @param \Cake\ORM\Entity $entity Entity instance.
     * @return true (irrespective of the behavior logic, the save will not be prevented)
     */
    public function beforeSave(Event $event, Entity $entity)
    {
        $conditions = $this->getConditions($entity);
        $count = $this->getCount($conditions, $entity);

        $config = $this->config();
        if ($count < $config['limit']) {
            return true;
        }
        if ($entity->get($config['field']) !== $config['state']) {
            return true;
        }

        $this->unselect($count, $conditions);
        return true;
    }

    /**
     * Find the number of entries matching the given expression
     * We don't want to take into account the currently saved record
     *
     * @param array $conditions ORM conditions to be used
     * @param \Cake\ORM\Entity $entity the entity to be saved
     * @return int
     */
    public function getCount(array $conditions, Entity $entity)
    {
        if (!$entity->isNew()) {
            $primaryKey = $this->_table->schema()->primaryKey();
            $primaryKey = is_string($primaryKey) ?: $primaryKey[0];
            $conditions[$primaryKey . ' NOT IN'] = $entity->get($primaryKey);
        }
        return $this->_table->find()
            ->where($conditions)
            ->count();
    }

    /**
     * Find the number of entries matching the given expression
     *
     * @param \Cake\ORM\Entity $entity the entity to match conditions against
     * @return array
     */
    public function getConditions(Entity $entity)
    {
        $config = $this->config();
        $conditions = [$config['field'] => $config['state']];
        foreach ($config['scope'] as $field) {
            $conditions = array_merge($conditions, [
                $field => $entity->get($field)
            ]);
        }
        return $conditions;
    }

    /**
     * Set the 'select' field to unselect for the entries
     * that can't be active.
     *
     * @param int $count The number of currently selected elements
     * @param array $conditions ORM conditions to be used
     * @return void
     */
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
