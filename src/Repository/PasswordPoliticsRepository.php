<?php

namespace App\Repository;

use App\Entity\PasswordPolitics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordPolitics>
 *
 * @method PasswordPolitics|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordPolitics|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordPolitics[]    findAll()
 * @method PasswordPolitics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordPoliticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordPolitics::class);
    }

    public function save(PasswordPolitics $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PasswordPolitics $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PasswordPolitics[] Returns an array of PasswordPolitics objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PasswordPolitics
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
