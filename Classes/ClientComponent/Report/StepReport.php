<?php
namespace NamelessCoder\Rpc\ClientComponent\Report;

/**
 * Class StepReport
 */
class StepReport extends AbstractReport {

    /**
     * @var string
     */
    protected $title = 'Step 1';

    /**
     * @var string
     */
    protected $content = NULL;

    /**
     * @var integer
     */
    protected $step = 1;

    /**
     * @var integer
     */
    protected $steps = 1;

    /**
     * @return integer
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * @param integer $step
     */
    public function setStep($step) {
        $this->step = $step;
    }

    /**
     * @return integer
     */
    public function getSteps() {
        return $this->steps;
    }

    /**
     * @param integer $steps
     */
    public function setSteps($steps) {
        $this->steps = $steps;
    }

    /**
     * @return float
     */
    public function getRatioComplete() {
        return ($this->step / $this->steps);
    }

    /**
     * @return integer
     */
    public function getPercentComplete() {
        return ceil($this->getRatioComplete() * 100);
    }

    /**
     * StepReport constructor.
     * @param string $title
     * @param string $content
     * @param integer $step
     * @param integer $steps
     */
    public function __construct($title, $content, $step = 1, $steps = 1) {
        parent::__construct($title, $content);
        $this->step = $step;
        $this->steps = $steps;
    }


    /**
     * @return array
     */
    public function compile() {
        return array_replace(
            array(
                'step' => $this->step,
                'steps' => $this->steps
            ),
            parent::compile()
        );
    }

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        $this->step = $jsonData['step'];
        $this->steps = $jsonData['steps'];
        return parent::fillFromJsonData($jsonData);
    }


}
