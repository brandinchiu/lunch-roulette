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
 * Class HistoryRepository
 * @package App\Repository
 */
class HistoryRepository extends ServiceEntityRepository
{
    /**
     * LunchOptionRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Entity\History::class);
    }

    public function findLastRoll()
    {
        return $this->findOneBy([
            'date' => (new \DateTime())
        ],[
            'id' => 'desc'
        ]);
    } 
}