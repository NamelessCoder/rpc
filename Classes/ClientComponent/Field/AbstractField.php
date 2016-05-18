<?php
namespace NamelessCoder\Rpc\ClientComponent\Field;

/**
 * Class AbstractField
 */
abstract class AbstractField implements FieldInterface {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $error;

    /**
     * Overridden to TRUE if field's label is part of the component itself,
     * i.e. if the field is a checkbox with clickable label text
     *
     * @var boolean
     */
    protected $rendersOwnLabel = FALSE;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType() {
        $class = get_class($this);
        return substr($class, strrpos($class, '\\') + 1);
    }

    /**
     * @return string
     */
    public function getClass() {
        return get_class($this);
    }

    /**
     * @return array
     */
    public function compile() {
        return array(
            'type' => $this->getType(),
            'class' => $this->getClass(),
            'name' => $this->name,
            'label' => $this->label,
            'value' => $this->value,
            'error' => $this->error
        );
    }

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        $this->setName($jsonData['name']);
        $this->setLabel($jsonData['label']);
        $this->setValue($jsonData['value']);
        $this->setError($jsonData['error']);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
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
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError($error) {
        $this->error = $error;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRendersOwnLabel() {
        return $this->rendersOwnLabel;
    }

    /**
     * @param boolean $rendersOwnLabel
     * @return $this
     */
    public function setRendersOwnLabel($rendersOwnLabel) {
        $this->rendersOwnLabel = $rendersOwnLabel;
        return $this;
    }

}
