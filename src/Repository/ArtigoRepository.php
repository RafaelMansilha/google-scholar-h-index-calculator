<?php

namespace App\Repository;

use App\Entity\Artigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Artigo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artigo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artigo[]    findAll()
 * @method Artigo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtigoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artigo::class);
    }
}
