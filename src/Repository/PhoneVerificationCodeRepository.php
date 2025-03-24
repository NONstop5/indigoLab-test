<?php

namespace App\Repository;

use App\Entity\PhoneVerificationCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhoneVerificationCode>
 */
class PhoneVerificationCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneVerificationCode::class);
    }

    /**
     * @throws Exception
     */
    public function getRecentCodesCount(
        string $phoneNumber,
        \DateTimeImmutable $recentDateTime,
    ): int {
        $sql = '
            SELECT COUNT(1)
            FROM phone_verification_code
            WHERE phone_number = :phoneNumber
              AND is_used  = FALSE
              AND created_at >= :recentDateTime;
        ';

        /** @var false|array<string, int> $result */
        $result = $this->getEntityManager()->getConnection()
            ->fetchAssociative(
                $sql,
                [
                    'phoneNumber' => $phoneNumber,
                    'recentDateTime' => $recentDateTime->format('Y-m-d H:i:s'),
                ],
                [
                    'phoneNumber' => Types::STRING,
                    'codesCount' => Types::STRING,
                ]
            );

        return $result === false ? 0 : $result['count'];
    }

    /**
     * @throws Exception
     *
     * @return false|array<string, mixed>
     */
    public function getLastCode(string $phoneNumber): false|array
    {
        $sql = '
            SELECT *
            FROM phone_verification_code
            WHERE phone_number = :phoneNumber
              AND is_used  = FALSE
              AND id = (
                SELECT MAX(id)
                FROM phone_verification_code
                WHERE phone_number = :phoneNumber
              );
        ';

        return $this->getEntityManager()->getConnection()
            ->fetchAssociative(
                $sql,
                [
                    'phoneNumber' => $phoneNumber,
                ],
                [
                    'phoneNumber' => Types::STRING,
                ]
            );
    }

    // TODO: Тоже самое только на DQL
    //
    //    public function getLastCode(string $phoneNumber): ?PhoneVerificationCode
    //    {
    //        $subQuery = $this->createQueryBuilder('c2')
    //            ->select('MAX(c2.createdAt)')
    //            ->where('c2.phoneNumber = :phoneNumber')
    //            ->getDQL();
    //
    //        return $this->createQueryBuilder('c1')
    //            ->where('c1.phoneNumber = :phoneNumber')
    //            ->andWhere('c1.createdAt = ('.$subQuery.')')
    //            ->setParameter('phoneNumber', $phoneNumber)
    //            ->getQuery()
    //            ->getOneOrNullResult();
    //    }

    //    /**
    //     * @return PhoneVerificationCode[] Returns an array of PhoneVerificationCode objects
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

    //    public function findOneBySomeField($value): ?PhoneVerificationCode
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
