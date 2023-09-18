<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function countUsersForAllEmployees()
    {
        return $this->createQueryBuilder('u')
            ->select('IDENTITY(u.conseiller) as conseiller_id, COUNT(u) as user_count')
            ->where('u.conseiller IS NOT NULL')
            ->groupBy('u.conseiller')
            ->orderBy('user_count', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findUserById($id): ?User
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function countUsersForConseiller($conseillerId)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.conseiller = :conseillerId')
            ->setParameter('conseillerId', $conseillerId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    // src/Repository/UserRepository.php

    public function findEmployees()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_EMPLOYE"%')
            ->getQuery()
            ->getResult();
    }


    public function findUsersAndHotes()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roleUser OR u.roles LIKE :roleHote')
            ->setParameter('roleUser', '%"ROLE_USER"%')
            ->setParameter('roleHote', '%"ROLE_HOTE"%')
            ->getQuery()
            ->getResult();
    }
    public function findUsersAndHotesByTerm(string $term)
{
    return $this->createQueryBuilder('u')
        ->where('u.roles LIKE :userRole OR u.roles LIKE :hoteRole')
        ->andWhere('u.firstName LIKE :term OR u.lastName LIKE :term')
        ->setParameters([
            'userRole' => '%ROLE_USER%',
            'hoteRole' => '%ROLE_HOTE%',
            'term' => '%' . $term . '%'
        ])
        ->getQuery()
        ->getResult();
}
    












}