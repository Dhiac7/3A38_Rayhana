<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function saveMessage(User $sender, User $receiver, string $content): void
    {
        $message = new Message();
        $message->setSender($sender);
        $message->setRecipient($receiver);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTime());

        $this->_em->persist($message);
        $this->_em->flush();
    }

    /**
     * Retrieve messages for a specific user.
     */
    public function getMessages(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findConversation(int $userId1, int $userId2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :userId1 AND m.recipient = :userId2) OR (m.sender = :userId2 AND m.recipient = :userId1)')
            ->setParameter('userId1', $userId1)
            ->setParameter('userId2', $userId2)
            ->orderBy('m.createdAt', 'ASC') // Order by timestamp to display messages in chronological order
            ->getQuery()
            ->getResult();
    }


    public function findMessagesBetweenUsers(int $senderId, int $receiverId): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :senderId AND m.receiver = :receiverId) OR (m.sender = :receiverId AND m.receiver = :senderId)')
            ->setParameter('senderId', $senderId)
            ->setParameter('receiverId', $receiverId)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
