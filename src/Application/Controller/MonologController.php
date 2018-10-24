<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 10:04 AM
 */

namespace Application\Controller;

use Model\Entity\ResponseBootstrap;
use Model\Service\MonologService;
use Symfony\Component\HttpFoundation\Request;

class MonologController
{

    private $monologService;

    public function __construct(MonologService $monologService)
    {
        $this->monologService = $monologService;
    }


    /**
     * Get logs by MS
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getLogs(Request $request):ResponseBootstrap {
        // get url parametar
        $type = $request->get('ms');

        // create response object
        $response = new ResponseBootstrap();

        // check if data is set
        if(isset($type)){
            return $this->monologService->getLogsByMS($type);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return data
        return $response;
    }


    /**
     * Add log
     *
     * @param Request $request
     * @return ResponseBootstrap
     * @throws \Exception
     */
    public function postAdd(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $service = $data['microservice'];
        $record = $data['record'];
        $type = $data['type'];

        // create response object
        $response = new ResponseBootstrap();

        // ckeck if data is set
        if(isset($service) && isset($record) && isset($type)){
            return $this->monologService->addRow($service, $record, $type);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return data
        return $response;
    }


    /**
     * Notify developer
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function postEmail(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $to = $data['to'];
        $title = $data['title'];
        $body = $data['body'];

        // create response object
        $response = new ResponseBootstrap();

        // check if data i set
        if(isset($to) && isset($title) && isset($body)){
            return $this->monologService->notifyDeveloper($to, $title, $body);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return data
        return $response;
    }


    /**
     * Delete log
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function deleteLog(Request $request):ResponseBootstrap {
        // get url parametars
        $type = $request->get('type');
        $date = $request->get('date');

        // create response object
        $response = new ResponseBootstrap();

        // check if data is set
        if(isset($type) && isset($date)){
            return $this->monologService->deleteLog($type, $date);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response
        return $response;
    }


    /**
     * Get total number of loggs
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getTotal(Request $request):ResponseBootstrap {
        // call service for data
        return $this->monologService->total();
    }

}