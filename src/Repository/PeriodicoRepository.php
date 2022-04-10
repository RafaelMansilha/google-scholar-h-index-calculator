<?php

namespace App\Repository;

use App\Entity\Periodico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Periodico|null find($id, $lockMode = null, $lockVersion = null)
 * @method Periodico|null findOneBy(array $criteria, array $orderBy = null)
 * @method Periodico[]    findAll()
 * @method Periodico[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodicoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Periodico::class);
    }
}
