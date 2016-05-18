<?php
namespace NamelessCoder\Rpc\Manager;

use NamelessCoder\Rpc\Implementation\Task\NoopTask;
use NamelessCoder\Rpc\Security\Token;
use NamelessCoder\Rpc\Task\TaskInterface;

/**
 * Class TaskManager
 */
class TaskManager {

    /**
     * @var TaskManagerInterface
     */
    protected static $instance;

    /**
     * @var TaskInterface[]
     */
    protected $tasks = [];

    /**
     * @var OptionValueProviderInterface[]
     */
    protected $optionValueProviders = [];

    /**
     * @return TaskManagerInterface
     */
    public static function getInstance() {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param TaskManagerInterface $taskManager
     */
    public static function setInstance(TaskManagerInterface $taskManager) {
        static::$instance = $taskManager;
    }

    /**
     * @param TaskInterface $task
     * @return $this
     */
    public function addTask(TaskInterface $task) {
        $this->tasks[$task->getTaskConfiguration()->getId()] = $task;
        ksort($this->tasks);
        return $this;
    }

    /**
     * @param $taskId
     * @return TaskInterface
     */
    public function getTaskById($taskId) {
        return isset($this->tasks[$taskId]) ? $this->tasks[$taskId] : new NoopTask('noop');
    }

    /**
     * @return array
     */
    public function getTaskIds() {
        return array_keys($this->tasks);
    }

    /**
     * @param Token $token
     * @return array
     */
    public function getTaskIdsByToken(Token $token) {
        $taskIds = array();
        foreach ($this->tasks as $task) {
            if ($this->tokenHasAccessToTask($token, $task)) {
                $taskIds[] = $task->getTaskConfiguration()->getId();
            }
        }
        return $taskIds;
    }

    /**
     * @param Token $token
     * @param TaskInterface $task
     * @return boolean
     */
    public function tokenHasAccessToTask(Token $token, TaskInterface $task) {
        return TRUE;
    }

}

