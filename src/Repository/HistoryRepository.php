<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method History[]    selectDistinctBy(string $column)
 * @method HistoryRepository    findAllAfterTime(string $time = null)
 * @method HistoryRepository    findAllBeforeTime(string $time = null)
 * @method HistoryRepository    findAllByStatus(int $status = null)
 * @method History[]            execute()
 */
class HistoryRepository extends ServiceEntityRepository
{
    private $query;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);

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

    public function execute()
    {
        return $this->query->getQuery()->execute();
    }
}
