<?php
namespace NamelessCoder\Rpc;

use NamelessCoder\Rpc\Controller\RequestController;
use NamelessCoder\Rpc\Manager\ClientManager;
use NamelessCoder\Rpc\Manager\TaskManager;

/**
 * Class RequestDispatcher
 */
class RequestDispatcher {

    /**
     * @return Response
     */
    public function handleIncomingRequest() {
        $controller = new RequestController(ClientManager::getInstance(), TaskManager::getInstance());
        return $controller->autoProcessRequest();
    }

    /**
     * @param string $hostname
     * @param boolean $useHttps
     * @param Request $request
     * @return Response
     */
    public function dispatchOutgoingRequest($hostname, $useHttps, Request $request) {
        $compiledRequest = $request->compile();
        $targetUrl = 'http' . (!$useHttps ? '' : 's') . '://' . $hostname . '/?type=991';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $targetUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $compiledRequest);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
        $responseBody = curl_exec($curl);
        if (empty($responseBody)) {
            throw new \RuntimeException('Received an empty response from the server. Is the server RPC-enabled?');
        }
        $decodedJson = json_decode($responseBody, JSON_OBJECT_AS_ARRAY);
        if (empty($decodedJson)) {
            throw new \RuntimeException('The server yielded a response but the response was not JSON. Is the server RPC-enabled?');
        }
        $response = new Response();
        $response->fillFromJsonData($decodedJson);
        return $response;
    }

}
