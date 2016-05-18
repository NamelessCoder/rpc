<?php
namespace NamelessCoder\Rpc\ClientComponent\Report;

/**
 * Interface ReportInterface
 */
interface ReportInterface {

    /**
     * @return array
     */
    public function compile();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData);
}

