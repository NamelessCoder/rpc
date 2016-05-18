<?php
namespace NamelessCoder\Rpc\ClientComponent;

use NamelessCoder\Rpc\ClientComponent\Field\FieldInterface;

/**
 * Class Sheet
 */
class Sheet {

    /**
     * @var FieldInterface[]
     */
    protected $fields = array();

    /**
     * @var string
     */
    protected $submitButtonLabel = 'Submit';

    /**
     * @param string $submitButtonLabel
     * @return $this
     */
    public function setSubmitButtonLabel($submitButtonLabel) {
        $this->submitButtonLabel = $submitButtonLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubmitButtonLabel() {
        return $this->submitButtonLabel;
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function addField(FieldInterface $field) {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return array
     */
    public function compile() {
        $fields = array();
        foreach ($this->fields as $field) {
            $fields[] = $field->compile();
        }
        return array(
            'submitButtonLabel' => $this->submitButtonLabel,
            'fields' => $fields
        );
    }

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        $this->submitButtonLabel = $jsonData['submitButtonLabel']; // always set in a compiled sheet array
        foreach ((array) $jsonData['fields'] as $fieldConfiguration) {
            $fieldClassName = $fieldConfiguration['class'];
            /** @var FieldInterface $field */
            $field = new $fieldClassName();
            $field->fillFromJsonData($fieldConfiguration);
            $this->addField($field);
        }
        return $this;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields() {
        return $this->fields;
    }

}

