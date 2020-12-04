<?php

namespace App\Repository;

use App\Entity\Sms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sms|null         find($id, $lockMode = null, $lockVersion = null)
 * @method Sms|null         findOneBy(array $criteria, array $orderBy = null)
 * @method Sms[]            findAll()
 * @method Sms[]            findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method SmsRepository    findAllAfterTime(string $time = null)
 * @method SmsRepository    findAllBeforeTime(string $time = null)
 * @method SmsRepository    findAllByStatus(int $status = null)
 * @method SmsRepository    findAllByNumber(string $number = null)
 * @method SmsRepository    findAllByBody(string $body = null)
 * @method SmsRepository    getTopNumbers(int $count = 10)
 * @method Sms[]            execute()
 */
class SmsRepository extends ServiceEntityRepository
{
    private $query;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sms::class);

        $this->query = $this->createQueryBuilder('sms');
    }

    public function findAllAfterTime(string $time = null): self
    {
        if ($time === null) {
            return $this;
        }

        $this->query
            ->andWhere('sms.updated_at > :from')
            ->setParameter('from', $time);

        return $this;
    }

    public function findAllBeforeTime(string $time = null): self
    {
        if ($time === null) {
            return $this;
        }

        $this->query
            ->andWhere('sms.updated_at < :to')
            ->setParameter('to', $time);
   
        return $this;
    }

    public function findAllByStatus(int $status = null): self
    {
        if ($status === null) {
            return $this;
        }

        $this->query
            ->andWhere("sms.status = :status")
            ->setParameter('status', $status);

        return $this;
    }

    public function findAllByNumber(string $number = null): self
    {
        if ($number === null) {
            return $this;
        }
        
        $this->query
            ->andWhere("sms.number = :number")
            ->setParameter('number', $number);

        return $this;
    }

    public function findAllByBody(string $body = null): self
    {
        if ($body === null) {
            return $this;
        }
        
        $this->query
            ->andWhere("LOWER(sms.body) LIKE :body")
            ->setParameter('body', $body);

        return $this;
    }

    public function getTopNumbers(int $limit = 10): self
    {
        $this->query->select('sms.number, COUNT(sms.number) as times')->groupBy('sms.number')->orderBy('times', 'desc')->setMaxResults($limit);

        return $this;
    }

    public function execute()
    {
        return $this->query->getQuery()->execute();
    }
}
