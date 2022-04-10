<?php

namespace App\Repository;

use App\Entity\Citacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Citacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Citacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Citacao[]    findAll()
 * @method Citacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CitacaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Citacao::class);
    }
}
