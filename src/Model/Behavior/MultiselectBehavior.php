<?php
namespace Muffin\Multiselect\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
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
    protected $_fieldConfig = [
        'state' => true,
        'limit' => 1,
        'order' => [],
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
        foreach ($config as $field => $fieldConfig) {
            if (is_string($fieldConfig)) {
                unset($config[$field]);
                $field = $fieldConfig;
                $fieldConfig = [];
            }
            $config[$field] = array_merge($this->_fieldConfig, $fieldConfig);
        }
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
        $fields = $this->config();
        foreach ($fields as $field => $config) {

            if ($entity->get($field) !== $config['state']) {
                continue;
            }

            $conditions = $this->getConditions($entity, $field, $config);
            $count = $this->getCount($conditions, $entity);

            if ($count < $config['limit']) {
                continue;
            }

            $this->unselect($count, $conditions, $field, $config);
        }
        return true;
    }

    /**
     * Find the number of selected entries excluding currently saved entity
     *
     * @param array $conditions ORM conditions to be used
     * @param \Cake\ORM\Entity $entity the entity to be saved
     * @return int
     */
    public function getCount(array $conditions, Entity $entity)
    {
        $count = $this->_table->find()
            ->where($conditions)
            ->count();

        return $count;
    }

    /**
     * Find the number of entries matching the given expression excluding
     * current entity
     *
     * @param \Cake\ORM\Entity $entity the entity to match conditions against
     * @param string $field name of the field to be configured
     * @param array $config config array for the specified field
     * @return array
     */
    public function getConditions(Entity $entity, $field, $config)
    {
        $conditions = [$field => $config['state']];
        foreach ($config['scope'] as $field) {
            $conditions = array_merge($conditions, [
                $field => $entity->get($field)
            ]);
        }
        if (!$entity->isNew()) {
            $primaryKey = $this->_table->schema()->primaryKey();
            $primaryKey = is_string($primaryKey) ?: $primaryKey[0];
            $conditions[$primaryKey . ' NOT IN'] = $entity->get($primaryKey);
        }
        return $conditions;
    }

    /**
     * Unselects the rows that exceed the limit
     *
     * @param int $count The number of currently selected elements
     * @param array $conditions ORM conditions to be used
     * @param string $field name of the field to be configured
     * @param array $config config array for the specified field
     * @return void
     */
    public function unselect($count, $conditions, $field, $config)
    {
        $trimmedRows = $this->_table->find()
            ->select([$this->_table->primaryKey()])
            ->where($conditions)
            ->order($config['order'])
            ->limit($count - $config['limit'] + 1)
            ->hydrate(false);

        $ids = Hash::extract($trimmedRows->toArray(), '{n}.' . $this->_table->primaryKey());
        $this->_table->query()->update()
            ->set([$field => !$config['state']])
            ->where([$this->_table->primaryKey() . ' IN' => $ids])
            ->execute();
    }
}
