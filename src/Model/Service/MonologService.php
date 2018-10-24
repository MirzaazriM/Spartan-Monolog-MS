<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 10:05 AM
 */

namespace Model\Service;


use Dubture\Monolog\Reader\LogReader;
use Model\Entity\ResponseBootstrap;
use Model\Mapper\MonologMapper;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonologService
{

    private $monologMapper;
    private $monolog;

    public function __construct(MonologMapper $monologMapper)
    {
        $this->monologMapper = $monologMapper;
        $this->monolog = new Logger('monolog');
    }


    /**
     * Read log file
     *
     * @param string $type
     * @return ResponseBootstrap
     */
    public function getLogsByMS(string $type):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // set responses variable and read file
            $responses = [];
            $reader = new LogReader('../resources/loggs/' . strtolower($type) . '.txt');

            // Get logs
            foreach ($reader as $key=>$log) {
                if(!empty($log['date'])){
                    array_push($responses,
                        [
                            'id' => $key,
                            'date' => $log['date']->format('Y-m-d H:i:s'),
                            'logger'=> $log['logger'],
                            'level' => $log['level'],
                            'message' => $log['message']
                        ]
                    );
                }
            }

            // reverse responses data and return max 50 logs
            $responses = array_reverse($responses);
            if(count($responses) > 50){
                $responses = array_slice($responses, 0, 50);
            }

            // set response
            if(!empty($responses)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData(
                    $responses
                );
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return data
            return $response;

        }catch (\Exception $e){
            $response->setStatus(404);
            $response->setMessage($e->getMessage());
            return $response;
        }
    }


    /**
     * Write log to appropriate txt file
     *
     * @param string $service
     * @param string $record
     * @param string $type
     * @return ResponseBootstrap
     * @throws \Exception
     */
    public function addRow(string $service, string $record, string $type){
        // create response object
        $response = new ResponseBootstrap();

        // set file to write in
        $this->monolog->pushHandler(new StreamHandler('../resources/loggs/' . strtolower($service) . '.txt'));

        // check type and set appropriate logger type
        if($type == 'WARNING'){
            $this->monolog->addWarning($record);
        }else if($type == 'ERROR'){
            $this->monolog->addError($record);
        }else if($type == 'INFO') {
            $this->monolog->addInfo($record);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
            return $response;
        }

        // set response
        $response->setStatus(200);
        $response->setMessage('Success');

        // return response
        return $response;
    }


    /**
     * Delete log
     *
     * @param string $type
     * @param string $date
     * @return ResponseBootstrap
     */
    public function deleteLog(string $type, string $date):ResponseBootstrap {

        try{
            // create response object
            $response = new ResponseBootstrap();

            // open file
            $file = strtolower($type);
            $file_out = file("../resources/loggs/" . $file . ".txt");

            // loop through file lines and find logg to delete
            foreach($file_out as $key=>$line){
                if(strpos($line, $date) !== false){
                    //Delete the recorded line
                    unset($file_out[$key]);
                }
            }

            // write new data into file
            file_put_contents("../resources/loggs/" . $file . ".txt", $file_out);

            // set response
            $response->setStatus(200);
            $response->setMessage('Success');

        }catch(\Exception $e){
            $response->setStatus(404);
            $response->setMessage('Invalid data');
        }

        // return response
        return $response;
    }


    /**
     * Send email to developer
     *
     * @param string $to
     * @param string $title
     * @param string $body
     * @return ResponseBootstrap
     */
    public function notifyDeveloper(string $to, string $title, string $body) {

        // create Mailgun service and send email
        // DEMO

//        $mgClient = new Mailgun('key-bc53423c7edafac99a3c9c4078c01d4b');
//        $domain = "wobbl.io";
//
//        # Make the call to the client.
//        $result = $mgClient->sendMessage("$domain",
//            array('from'    => 'Wobbl <noreply@wobbl.io>',
//                'to'      => $to,
//                'subject' => $title,
//                'html'    => $body));
//
//        return ['status' => 200,'message'=>'Successfully sent'];
    }


    /**
     * Get total number of bugs
     *
     * @return ResponseBootstrap
     */
    public function total():ResponseBootstrap {
        // set total variable
        $total = 0;

        // log files
        $files = ['admin.txt', 'apps.txt', 'auth.txt', 'exercises.txt', 'mobile.txt', 'notifications.txt',
                    'nutrition_plans.txt', 'packages.txt', 'recepies.txt', 'system.txt', 'tags.txt',
                        'users.txt', 'workout_plans.txt', 'workouts.txt'];

        // read all files
        foreach($files as $file){
            $reader = new LogReader('../resources/loggs/' . $file);
            $total += $reader->count();
        }

        // set response
        $response = new ResponseBootstrap();
        $response->setStatus(200);
        $response->setMessage('Success');
        $response->setData([
            $total
        ]);

        // return response
        return $response;
    }

}