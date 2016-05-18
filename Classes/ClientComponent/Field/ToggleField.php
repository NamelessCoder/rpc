<?php
namespace NamelessCoder\Rpc\ClientComponent\Field;

/**
 * Class ToggleField
 */
class ToggleField extends AbstractField {

    /**
     * @var boolean
     */
    protected $value = TRUE;

    /**
     * @var boolean
     */
    protected $on = TRUE;

    /**
     * Toggle fields (checkbox) renders it's own label by default
     * as a text label that can be clicked to toggle the box.
     *
     * @var boolean
     */
    protected $rendersOwnLabel = TRUE;

    /**
     * @return boolean
     */
    public function getOn() {
        return $this->on;
    }

    /**
     * @param boolean $on
     * @return $this
     */
    public function setOn($on) {
        $this->on = $on;
        return $this;
    }

    /**
     * @return array
     */
    public function compile() {
        return array_replace(
            parent::compile(),
            array(
                'on' => $this->on
            )
        );

    }

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        parent::fillFromJsonData($jsonData);
        $this->setOn((boolean) $jsonData['on']);
        return $this;
    }

}
