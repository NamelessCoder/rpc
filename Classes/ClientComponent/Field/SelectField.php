<?php
namespace NamelessCoder\Rpc\ClientComponent\Field;

/**
 * Class SelectField
 */
class SelectField extends AbstractField {

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptionsForFluid() {
        return array_combine($this->options, $this->options);
    }

    /**
     * @return array
     */
    public function compile() {
        return array_replace(
            parent::compile(),
            array(
                'options' => $this->options
            )
        );
    }

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        parent::fillFromJsonData($jsonData);
        $this->setOptions($jsonData['options']);
        return $this;
    }

}
