<?php

namespace App\Service;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Exception\TelegramException;

class Telegram
{
    private $api;

    const COMMAND_START = 'start';
    const COMMAND_NEXT = 'next';
    const COMMAND_NEW = 'new';

    public static $allowedCommands = [self::COMMAND_START, self::COMMAND_NEXT, self::COMMAND_NEW];

    /**
     * @return void
     * @throws TelegramException
     */
    public function __construct()
    {
        $this->api = new \Longman\TelegramBot\Telegram($_ENV['BOT_API_KEY'], $_ENV['BOT_USERNAME']);
        $this->api->useGetUpdatesWithoutDatabase();
        $this->api->handle();

    }

    /**
     * @return mixed
     */
    public function getUpdates()
    {
        return json_decode($this->api->getCustomInput());
    }

    /**
     * @param int $chatId
     * @param array $message
     * @return void
     * @throws TelegramException
     */
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
