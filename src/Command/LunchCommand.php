<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 4:10 PM
 */

namespace App\Command;


use App\Entity;
use App\Service\SlackService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LunchCommand
 * @package App\Command
 */
class LunchCommand extends Command
{
    /** @var ContainerInterface  */
    private $container;

    /** @var SlackService  */
    private $slack;

    /** @var string  */
    protected static $defaultName = 'lunch-roulette:start';

    /**
     * LunchCommand constructor.
     * @param ContainerInterface $container
     * @param SlackService $slack
     */
    public function __construct(ContainerInterface $container, SlackService $slack)
    {
        $this->container = $container;
        $this->slack = $slack;

        parent::__construct(self::$defaultName);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->container->get('doctrine')->getManager()->getRepository(Entity\LunchOption::class)
            ->findRemaining()
        ;

        /** @var Entity\LunchOption $option */
        $option = $options[array_rand($options)];

        try {
            $this->slack->message($this->container->getParameter('slack_webhook_url'), sprintf(
                "Hello <!channel>! How does *<%s|%s>* sound for lunch today?",
                $option->getUrl(),
                $option->getName()
            ));

            $history = new Entity\History();
            $history->setLunchOption($option);

            /**
             * save the history.
             */
            $this->container->get('doctrine')->getManager()->persist($history);
            $this->container->get('doctrine')->getManager()->flush();

        } catch(\Exception $e) {
            throw new \Exception('Error');
        }
    }
}