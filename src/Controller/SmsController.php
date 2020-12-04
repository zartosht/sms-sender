<?php

namespace App\Controller;

use App\Entity\Sms;
use App\Message\Sms as SmsMessage;
use App\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\SmsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sms", name="sms")
 */
class SmsController extends AbstractController
{
    /**
     * @Route("/send", name="send")
     */
    public function sendSms(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$request || !$request->get('number') || !$request->get('body')) {
            throw new \Exception();
        }

        $sms = new Sms();
        $sms->setNumber($request->get('number'));
        $sms->setBody($request->get('body'));
        $sms->setStatus(0);
        $sms->setTries(0);
        $entityManager->persist($sms);
        $entityManager->flush();

        $this->dispatchMessage(new SmsMessage($sms->getId()));

        return $this->json([
            'message' => "Message sent successfully to {$sms->getNumber()}",
        ]);
    }

    /**
     * @Route("/status/{id}", name="status", methods={"GET"})
     */
    public function smsStatus(SmsRepository $smsRepository, int $id): Response
    {
        $sms = $smsRepository->findOneBy(['id' => $id]);
        return $this->render('sms/status.html.twig', [
            'sms' => $sms
        ]);
    }

    /**
     * @Route("/status/", name="status_all", methods={"GET"})
     */
    public function allSmsStatus(Request $request, SmsRepository $smsRepository): Response
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $status = $request->get('status', null);
        $number = $request->get('number', null);
        $body = $request->get('body', null);
        
        $sms = $smsRepository
            ->findAllBeforeTime($to)
            ->findAllAfterTime($from)
            ->findAllByStatus($status)
            ->findAllByNumber($number)
            ->findAllByBody($body)
            ->execute();


        return $this->render('sms/all.html.twig', [
            'sms' => $sms
        ]);
    }

    /**
     * @Route("/rate", name="rate", methods={"GET"})
     */
    public function rate(Request $request, HistoryRepository $historyRepository)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $status = $request->get('status', null);
        
        $histories = $historyRepository
            ->findAllBeforeTime($to)
            ->findAllAfterTime($from)
            ->findAllByStatus($status)
            ->execute();

        $result = [];
        foreach ($histories as $history) {
            $result[$history->getUrl()] = isset($result[$history->getUrl()]) ? $result[$history->getUrl()] : ['success' => 0, 'failed' => 0, 'total' => 0];

            if ($history->getStatus() === 2) {
                $result[$history->getUrl()]['success']++;
            } else {
                $result[$history->getUrl()]['failed']++;
            }
            $result[$history->getUrl()]['total']++;
        }

        foreach ($result as $url => $counts) {
            $result[$url]['success_precent'] = number_format($result[$url]['success'] / $result[$url]['total'] * 100, 2);
            $result[$url]['failed_precent'] = number_format($result[$url]['failed'] / $result[$url]['total'] * 100, 2);
        }

        return $this->render('sms/rate.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * @Route("/top/{count}", name="rate", methods={"GET"})
     */
    public function top(Request $request, SmsRepository $smsRepository, $count = 10)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $status = $request->get('status', null);
        $body = $request->get('body', null);

        $numbers = $smsRepository
            ->findAllBeforeTime($to)
            ->findAllAfterTime($from)
            ->findAllByStatus($status)
            ->findAllByBody($body)
            ->getTopNumbers($count)
            ->execute();

        return $this->render('sms/top.html.twig', [
            'count' => $count,
            'numbers' => $numbers,
        ]);
    }
}
