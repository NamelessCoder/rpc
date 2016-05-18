<?php
namespace NamelessCoder\Rpc\ClientComponent\Field;

/**
 * Interface FieldInterface
 */
interface FieldInterface {

    /**
     * @return array
     */
    public function compile();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getError();

    /**
     * @param string $error
     * @return $this
     */
    public function setError($error);

    /**
     * @return boolean
     */
    public function getRendersOwnLabel();

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData);
}