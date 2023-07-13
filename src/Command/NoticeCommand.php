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
class NoticeCommand extends Command
{
    /** @var ContainerInterface  */
    private $container;

    /** @var SlackService  */
    private $slack;

    /** @var string  */
    protected static $defaultName = 'lunch-roulette:notice';

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
        try {
            $history = $this->container->get('doctrine')->getManager()->getRepository(Entity\History::class)
                ->findLastRoll()
            ;

            if($history == null) {
                throw new \Exception();
            }

            $this->slack->message($this->container->getParameter('slack_webhook_url'), sprintf(
                "Rolling has closed! The winner is *<%s|%s>*!",
                $history->getLunchOption()->getUrl(),
                $history->getLunchOption()->getName()
            ));

        } catch(\Exception $e) {
            throw new \Exception('Error');
        }
    }
}