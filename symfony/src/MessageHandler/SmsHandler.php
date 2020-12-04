<?php

namespace App\MessageHandler;

use App\Entity\History;
use App\Message\Sms as SmsMessage;
use App\Repository\HistoryRepository;
use App\Repository\SmsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SmsHandler implements MessageHandlerInterface
{
    private $bus;
    private $smsRepository;
    private $historyRepository;
    private $entityManager;

    public function __construct(SmsRepository $smsRepository, HistoryRepository $historyRepository, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->smsRepository = $smsRepository;
        $this->historyRepository = $historyRepository;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    public function __invoke(SmsMessage $smsMessage)
    {
        $id = $smsMessage->getContent();
        $smsEntity = $this->smsRepository->findOneBy(["id" => $id]);
        $smsEntity->retry();

        $history = new History();
        $history->setUrl($smsEntity->getSenders()[$smsEntity->getTries() - 1]);
        $history->setStatus(1);
        $history->setSms($smsEntity);
        $this->entityManager->persist($history);
        $this->entityManager->flush();

        $sendMessageSuccess = rand(0,1) === 0 ? false : true; // mock api results

        $smsEntity->setStatus($sendMessageSuccess ? 2 : 3);
        $history->setStatus($sendMessageSuccess ? 2 : 3);

        $this->entityManager->persist($smsEntity);
        $this->entityManager->persist($history);
        $this->entityManager->flush();

        if ($sendMessageSuccess === false && $smsEntity->getTries() < 2) {
            $this->bus->dispatch(new SmsMessage($smsEntity->getId()));
        }
    }
}
