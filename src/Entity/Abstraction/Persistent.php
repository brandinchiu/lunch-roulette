<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 5:32 PM
 */

namespace App\Entity\Abstraction;


/**
 * Trait Persistent
 * @package App\Entity\Abstraction
 */
trait Persistent
{
    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getManager()
    {
        /** \App\Kernel $kernel */
        global $kernel;

        return $kernel->getContainer()->get('doctrine')->getManager();
    }
}