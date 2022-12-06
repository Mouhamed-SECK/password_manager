<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getExperimentationByUser($id)
    {
        $em = $this->getEntityManager();

        $query = $em
            ->createQuery(
                "SELECT e " .
                    "FROM AppBundle:Experimentation e " .
                    "JOIN e.experimentationUsers eu " .
                    "JOIN eu.user u " .
                    "WHERE u.id = :id "
            )
            ->setParameter("id", $id);

        return $query->getResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    public function findGroupUsers($value): array
    {
        return $this->createQueryBuilder("u")
            ->andWhere("u.groupe = :val")
            ->setParameter("val", $value)
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
