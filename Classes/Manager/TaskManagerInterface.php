<?php
namespace NamelessCoder\Rpc\Manager;

use NamelessCoder\Rpc\Security\Token;
use NamelessCoder\Rpc\Task\TaskInterface;

/**
 * Interface TaskManagerInterface
 */
interface TaskManagerInterface {

    /**
     * @return TaskManager
     */
    public static function getInstance();

    /**
     * @param TaskInterface $task
     * @return $this
     */
    public function addTask(TaskInterface $task);

    /**
     * @param $taskId
     * @return TaskInterface
     */
    public function getTaskById($taskId);

    /**
     * @return array
     */
    public function getTaskIds();

    /**
     * @param Token $token
     * @return array
     */
    public function getTaskIdsByToken(Token $token);

    /**
     * @param Token $token
     * @param TaskInterface $task
     * @return boolean
     */
    public function tokenHasAccessToTask(Token $token, TaskInterface $task);

}