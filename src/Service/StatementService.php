<?php

namespace App\Service;

use App\Entity\Statement;
use App\Service\Telegram;
use GuzzleHttp\Client;

class StatementService
{
    private $client;

    public function __construct()
    {
            }

    public function process($userId, $message)
    {
        $entityManager = $this->getDoctrine()->getManager();


        switch($message['command']){
            case Telegram::COMMAND_START:
                 return "The I Ching is a cornerstone of Chinese philosophy. It describes the basis elements of the way to enlightenment (happiness, inner healing, holiness, in God living). When using the oracle, every statement, every question should be interpreted with wisdom. We should consider our situation closely, and then ask ourselves what the selected bit of wisdom drawn means in our situation. 

Basically, the I Ching oracle is a game which helps us toward positive principles of life and strategies of wisdom. 

The I Ching Oracle knows 448 single oracle statements. 
Simply click 'Ask Oracle' or you can also intuitively think of a number and send this number to Oracle";

            case Telegram::COMMAND_RANDOM:

                $statement = $entityManager->getRepository(Statement::class)->findOneBy(['number' => rand(1, 445)]);

                return $statement->getNumber() . PHP_EOL .  $statement->getTitle() . PHP_EOL .  $statement->getText();

            case Telegram::COMMAND_NUMBER:
                $statement = $entityManager->getRepository(Statement::class)->findOneBy(['number' => $message['number']]);

                return $statement->getNumber() . PHP_EOL .  $statement->getTitle() . PHP_EOL .  $statement->getText();
        }
    }
}