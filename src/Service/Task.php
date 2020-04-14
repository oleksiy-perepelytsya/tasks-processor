<?php

namespace App\Service;

use App\Service\Telegram;
use GuzzleHttp\Client;

class Task
{
    private $baseUri  = 'http://178.128.75.144:8080';
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'defaults' => [
                'exceptions' => false
            ]
        ]);
    }

    public function process($userId, $message)
    {
        $response = false;


        switch($message['command']){
            case Telegram::COMMAND_START:
                $response = $this->client->get("/task/user/" . $userId);
                $responseArray = json_decode($response->getBody(true), true);

                $list = array();
                if(isset($responseArray['resource'])) foreach ($responseArray['resource'] as $task) {
                    if(count($responseArray['resource']) - count($list) == 1){
                        $list[] = PHP_EOL . "Your current task:" . $task['text'] . PHP_EOL;
                        continue;
                    }

                    $list[] = "- " . $task['text'];
                }

                if($list){
                    array_unshift($list, "Here is list of your tasks:");
                }

                $list[] = "Use /new {task} to add new task";
                $list[] = "Use /next mark first task complete";

                $response = implode(PHP_EOL, $list);

                return $response;

            case Telegram::COMMAND_NEW:
                $response = $this->client->post('/task/add/user/' . $userId, [
                    'form_params' => [
                        'text' => $message['text']
                    ]
                ]);

                return 'Task added to your list.' . PHP_EOL .  'Use /start to see your current task and task list';

            case Telegram::COMMAND_NEXT:
                $response = $this->client->get('/task/completed/user/' . $userId);

                return 'Task completed, move to the next one!' . PHP_EOL .  'Use /start to see your current task and task list';
        }
    }
}