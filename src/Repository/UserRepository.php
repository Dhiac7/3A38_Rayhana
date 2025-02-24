<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByPhoneNumber($tel)
    {
        return $this->createQueryBuilder('u')
            ->where('u.tel = :tel')
            ->setParameter('tel', $tel)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.role LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
    }
    public function findByEmail(string $email)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')  
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();  
    }
    
     public function getUserStatistics()
     {
         return $this->createQueryBuilder('u')
             ->select('u.genre AS genre, COUNT(u.id) AS user_count')
             ->groupBy('u.genre')
             ->getQuery()
             ->getResult();
     }
     
    public function getAgeStatistics()
    {
        $users = $this->findAll(); 
        $ageGroups = [
            ['age_range' => '0-18', 'user_count' => 0],
            ['age_range' => '19-35', 'user_count' => 0],
            ['age_range' => '36-50', 'user_count' => 0],
            ['age_range' => '50+', 'user_count' => 0]
        ];
    
        foreach ($users as $user) {
            $age = $user->getAge(); 
    
            if ($age <= 18) {
                $ageGroups[0]['user_count']++;
            } elseif ($age <= 35) {
                $ageGroups[1]['user_count']++;
            } elseif ($age <= 50) {
                $ageGroups[2]['user_count']++;
            } else {
                $ageGroups[3]['user_count']++;
            }
        }
    
        return $ageGroups;
    }
    


}
