<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use App\Service\Telegram;
use App\Service\Task;
use App\Entity\Statement;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramBotController extends AbstractController
{

    /**
     * @Route("/", name="endpoint")
     *
     * @param Telegram $telegram
     * @param Task $taskService
     * @return string
     * @throws TelegramException
     */
    public function indexAction(Telegram $telegram, Task $taskService)
    {
        try {
            $update = $telegram->getUpdates();
            $message = Telegram::parseMessage($update->message->text);
            $userId = $update->message->chat->id;

            $response = $taskService->process($userId, $message);

            $telegram->sendMessage($userId, $response);

        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            Longman\TelegramBot\TelegramLog::error($e);
        } catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
            error_log($e);
        }

        return new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );;
    }

}
