<?php

namespace App\Service;

use App\Service\Telegram;
use GuzzleHttp\Client;

class Task
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $_ENV['TASK_SERVICE_URI'],
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
                        $list[] = PHP_EOL . "Your current task: " . $task['text'] . PHP_EOL;
                        continue;
                    }

                    $list[] = "- " . $task['text'];
                }

                if($list){
                    array_unshift($list, "Your tasks:");
                }

                $list[] = "Use /new task_description to add new one";
                $list[] = "Use /next to move to the next task";

                $response = implode(PHP_EOL, $list);

                return $response;

            case Telegram::COMMAND_NEW:
                if(empty($message['text'])){
                    return 'Please provide task description';
                }

                $this->client->post('/task/add/user/' . $userId, [
                    'form_params' => [
                        'text' => $message['text']
                    ]
                ]);

                return 'Task added to your list.' . PHP_EOL .  'Use /start to see your tasks list';

            case Telegram::COMMAND_NEXT:
                $this->client->get('/task/completed/user/' . $userId);

                return 'Task completed, move to the next one!' . PHP_EOL .  'Use /start to see your tasks list';
        }
    }
}