<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 4:22 PM
 */

namespace App\Repository;


use App\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class LunchOptionRepository
 * @package App\Repository
 */
class LunchOptionRepository extends ServiceEntityRepository
{
    /**
     * LunchOptionRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Entity\LunchOption::class);
    }

    /**
     * @return array
     */
    public function findRemaining(): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\LunchOption', 'o')
            ->addFieldResult('o', 'id', 'id')
            ->addFieldResult('o', 'name', 'name')
            ->addFieldResult('o', 'url', 'url')
            ->addFieldResult('o', 'dateCreated', 'date_created')
        ;

        /**
         * get a list of options that haven't been rerolled yet today.
         */
        $query = $this->getEntityManager()->createNativeQuery("
            SELECT o.*
            FROM lunch_option o
            LEFT JOIN (
                SELECT *
                FROM history
                WHERE date=DATE(UTC_TIMESTAMP())
            ) h ON h.lunch_option_id = o.id
            WHERE h.id IS NULL
            ;
        ", $rsm);

        return $query->getResult();
    }
}