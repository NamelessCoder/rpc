<?php
namespace NamelessCoder\Rpc\Task;

/**
 * Class TaskConfiguration
 */
class TaskConfiguration {

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var boolean
     */
    protected $available = TRUE;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function isAvailable() {
        return $this->available;
    }

    /**
     * @param boolean $available
     * @return $this
     */
    public function setAvailable($available) {
        $this->available = $available;
        return $this;
    }


}
