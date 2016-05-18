<?php
namespace NamelessCoder\Rpc;

use NamelessCoder\Rpc\ClientComponent\Report\ReportInterface;
use NamelessCoder\Rpc\ClientComponent\Report\SuccessReport;
use NamelessCoder\Rpc\ClientComponent\Sheet;
use NamelessCoder\Rpc\Security\Token;

/**
 * Class Response
 *
 * A Response generated by processing a Request.
 * Responses will always contain a token and a report
 * and will optionally contain one or more of the
 * following attributes:
 *
 * - "sheet" which is a collection of ClientComponent
 *   instances to define input fields presented to
 *   the client who fills the properties using a
 *   rendering of those fields. The rendering is up
 *   to the client.
 * - "payload" which is a mixed value that may be
 *   included if the performed action yielded some
 *   output that is *not* a report or a sheet, for
 *   example a file body.
 *
 * "report" is always included and contains an error
 * or success report with a title and content for
 * simple feedback to clients. A default success
 * report is automatically inserted; when errors
 * happen that report is replaced with an error report.
 * "token" is always included in a response and is
 * either a generated token or a simple echo of the
 * token used in the matching request.
 */
class Response {

    /**
     * Assigned in Response and delivered to client.
     * If client provided the token it is echoed here,
     * if client did not provide a token the generated
     * one is provided here.
     *
     * @var string|NULL
     */
    protected $token = NULL;

    /**
     * @var string
     */
    protected $task;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var Sheet
     */
    protected $sheet;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var boolean
     */
    protected $completed = FALSE;

    /**
     * Response constructor.
     */
    public function __construct() {
        $this->report = new SuccessReport('Success', 'The operation was successful');
        $this->sheet = new Sheet();
    }

    /**
     * @param Sheet $sheet
     * @return $this
     */
    public function setSheet(Sheet $sheet) {
        $this->sheet = $sheet;
        return $this;
    }

    /**
     * @return Sheet
     */
    public function getSheet() {
        return $this->sheet;
    }

    /**
     * @param ReportInterface $report
     * @return $this
     */
    public function setReport(ReportInterface $report) {
        $this->report = $report;
        return $this;
    }

    /**
     * @return ReportInterface
     */
    public function getReport() {
        return $this->report;
    }

    /**
     * @return string
     */
    public function getTask() {
        return $this->task;
    }

    /**
     * @param string $task
     * @return $this
     */
    public function setTask($task) {
        $this->task = $task;
        return $this;
    }

    /**
     * @return Token|NULL
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function setToken(Token $token) {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @return boolean
     */
    public function getCompleted() {
        return $this->completed;
    }

    /**
     * @return $this
     */
    public function complete() {
        $this->completed = TRUE;
        return $this;
    }

    /**
     * Sets the optional payload to be returned with
     * the Response. Mixed type is supported by any
     * value passed here *must* be possible to convert
     * to JSON, so be careful when passing objects.
     *
     * @param mixed $payload
     * @return $this
     */
    public function setPayload($payload) {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string) json_encode(array(
            'token' => (string) $this->token,
            'task' => $this->task,
            'sheet' => $this->sheet ? $this->sheet->compile() : NULL,
            'completed' => $this->completed,
            'report' => $this->report->compile(),
            'payload' => $this->payload
        ), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT);
    }

    /**
     * Fill from a JSON array
     *
     * @param array $jsonData
     * @return $this
     */
    public function fillFromJsonData(array $jsonData) {
        $reportClassName = 'NamelessCoder\\Rpc\\ClientComponent\\Report\\' . $jsonData['report']['type'];
        $this->token = new Token($jsonData['token']);
        $this->task = $jsonData['task'];
        $this->sheet->fillFromJsonData($jsonData['sheet']);
        $this->completed = $jsonData['completed'];
        $this->report = new $reportClassName();
        $this->report->fillFromJsonData($jsonData['report']);
        $this->payload = $jsonData['payload'];
        return $this;
    }

}
