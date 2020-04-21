<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use App\Service\Telegram;
use App\Service\StatementService;
use App\Entity\Statement;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramBotController extends AbstractController
{

    /**
     * @Route("/", name="endpoint")
     *
     * @param Telegram $telegram
     * @param StatementService $statementService
     * @return string
     * @throws TelegramException
     */
    public function indexAction(Telegram $telegram, StatementService $statementService)
    {
        try {
            $update = $telegram->getUpdates();
            $message = Telegram::parseMessage($update->message->text);
            $userId = $update->message->chat->id;

            $response = $statementService->process($userId, $message);

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Start from the beginning', 'callback_data' => 'start'],
                        ['text' => 'Ask Oracle again', 'callback_data' => 'random']
                    ]
                ]
            ];

            $telegram->sendMessage($userId, $response, $keyboard);

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
