<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $entityManager = $this->getDoctrine()->getManager();

        $updates = $telegram->getUpdates();

        if($updates) foreach($updates as $update) {
            $message = Telegram::parseMessage($update->getMessage()->getText());
            $userId = $update->getMessage()->getChat()->getId();

            $response = $taskService->process($userId, $message);

            $telegram->sendMessage($userId, $response);
        }

        return $this->json([]);
    }

}
