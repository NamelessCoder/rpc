<?php
namespace NamelessCoder\Rpc\Task;

/**
 * Class AbstractTask
 */
abstract class AbstractTask implements TaskInterface {

    /**
     * @var TaskConfiguration
     */
    protected $configuration;

    /**
     * AbstractTask constructor.
     *
     * @param string $id
     */
    public function __construct($id) {
        $this->configuration = new TaskConfiguration();
        $this->configuration->setId($id);
    }

    /**
     * @return TaskConfiguration
     */
    public function getTaskConfiguration() {
        return $this->configuration;
    }


}
