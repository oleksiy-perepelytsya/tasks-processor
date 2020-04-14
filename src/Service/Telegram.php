<?php

namespace App\Service;

use Longman\TelegramBot\Request;

class Telegram
{
    private $botApiKey  = '1017992094:AAHM9DigJGvZmGyLP2RJZu1pP5seIV12UEw';
    private $botUsername = 'testdocler_bot';
    private $mysqlCredentials = [
        'host'     => '172.17.0.2',
        'port'     => 3306, // optional
        'user'     => 'alex',
        'password' => 'password',
        'database' => 'tasks_processor',
    ];
    private $api;

    const COMMAND_START = 'start';
    const COMMAND_NEXT = 'next';
    const COMMAND_NEW = 'new';

    public static $allowedCommands = [self::COMMAND_START, self::COMMAND_NEXT, self::COMMAND_NEW];

    public function __construct()
    {
        $this->api = new \Longman\TelegramBot\Telegram($this->botApiKey, $this->botUsername);
        $this->api->enableMySql($this->mysqlCredentials);
    }

    public function getUpdates()
    {
        return $this->api->handleGetUpdates()->getResult();
    }

    public function sendMessage($chatId, $message)
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $message
        ]);
    }

    /**
     * @param string $message
     * @return array
     */
    public static function parseMessage($message)
    {
        if($message[0] != '/'){
            return false;
        }
        $message = substr($message, 1);

        $command = strtok(trim($message), ' ');

        if(!in_array($command, self::$allowedCommands)){
            return false;
        }

        $message = str_replace($command, "", $message);

        return [
            'command' => $command,
            'text' => $message
        ];
    }

}