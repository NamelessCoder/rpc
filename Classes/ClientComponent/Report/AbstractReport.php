<?php
namespace NamelessCoder\Rpc\ClientComponent\Report;

/**
 * Class AbstractReport
 */
abstract class AbstractReport implements ReportInterface {

    const CODE_OK = 'success';
    const CODE_INFO = 'info';
    const CODE_WARNING = 'warning';
    const CODE_ERROR = 'danger';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $code = self::CODE_OK;

    /**
     * @var bool
     */
    protected $suppressed = FALSE;

    /**
     * AbstractReport constructor.
     * @param string|NULL $title
     * @param string|NULL $content
     */
    public function __construct($title = NULL, $content = NULL) {
        $this->title = (string) $title;
        $this->content = (string) $content;
    }

    /**
     * @return string
     */
    public function getType() {
        return substr(get_class($this), strrpos(get_class($this), '\\') + 1);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @return boolean
     */
    public function isSuppressed() {
        return $this->suppressed;
    }

    /**
     * @param boolean $suppressed
     * @return $this
     */
    public function setSuppressed($suppressed) {
        $this->suppressed = $suppressed;
        return $this;
    }

    /**
     * @return array
     */
    public function compile() {
        return array(
            'title' => $this->title,
            'content' => $this->content,
            'code' => $this->code,
            'suppressed' => $this->suppressed,
            'type' =>   $this->getType()
        );
    }

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        $this->title = $jsonData['title'];
        $this->content = $jsonData['content'];
        $this->suppressed = $jsonData['suppressed'];
        $this->code = $jsonData['code'];
        return $this;
    }


}

