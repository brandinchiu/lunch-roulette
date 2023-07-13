<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 9:07 AM
 */

namespace App\Controller;


use App\Entity;
use App\Service\SlackService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController
 * @package App\Controller
 */
class ApiController
{
    const MAX_SPINS = 6;

    /** @var ContainerInterface  */
    private $container;

    /** @var EntityManagerInterface  */
    private $manager;

    /** @var SlackService  */
    private $slack;

    /**
     * ApiController constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $manager
     * @param SlackService $slack
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $manager, SlackService $slack)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->slack = $slack;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addItem(Request $request)
    {
        /**
         * prepare details.
         */
        $userId = $request->request->get('user_id');
        $userName = $request->request->get('user_name');
        $text = $request->request->get('text');
        $responseUrl = $this->container->getParameter('slack_webhook_url');

        /**
         * break pararms
         */
        if(false === $params = preg_split('/".*?"(*SKIP)(*FAIL)|\s+/', $text)) {
            $name = $params;
            $url = null;

        } else {
            $name = $params[0];
            $url = (isset($params[1]) && filter_var($params[1], FILTER_VALIDATE_URL)) ? $params[1] : null;
        }

        $name = trim($name, "\"");

        /**
         * create the option.
         */
        $option = new Entity\LunchOption();
        $option->setName($name)
            ->setUrl($url)
            ->setSlackId($userId)
            ->setSlackName($userName)
        ;

        try {
            /**
             * save the option.
             */
            $this->manager->persist($option);
            $this->manager->flush();

            $this->slack->message($responseUrl, sprintf(
                '%s added *<%s|%s>* to the lunch roulette!',
                $userName,
                $option->getUrl(),
                $option->getName()
            ));

        } catch(\Exception $e) {
            return new Response('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function removeItem(Request $request)
    {
        /**
         * prepare details.
         */
        $userName = $request->request->get('user_name');
        $text = $request->request->get('text');
        $responseUrl = $this->container->getParameter('slack_webhook_url');
        $privateResponseUrl = $request->request->get('response_url');

        $option = $this->manager->getRepository(Entity\LunchOption::class)->find((int)$text);

        try {
            if($option !== null) {
                $this->manager->remove($option);
                $this->manager->flush();

                $this->slack->message($responseUrl, sprintf(
                    '%s removed *<%s|%s>* from the lunch roulette!',
                    $userName,
                    $option->getUrl(),
                    $option->getName()
                ));
            } else {
                $this->slack->message($privateResponseUrl, 'Sorry, I couldn\'t find a lunch option with that ID.');
            }

        } catch(\Exception $e) {
            return new Response('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function reroll(Request $request)
    {
        /**
         * prepare details.
         */
        $userId = $request->request->get('user_id');
        $userName = $request->request->get('user_name');
        $text = $request->request->get('text');
        $responseUrl = $this->container->getParameter('slack_webhook_url');
        $privateResponseUrl = $request->request->get('response_url');

        /**
         * get available unrolled options.
         */

        /** @var Entity\LunchOption[] $options */
        $options = $this->manager->getRepository(Entity\LunchOption::class)->findRemaining();

        /**
         * restrict reroll to tag
         */
        if($text != null) {
            foreach($options as $key => $option) {
                if(!$option->hasTag($text)) {
                    unset($options[$key]);
                }
            }
        }

        $history = $this->manager->getRepository(Entity\History::class)->findBy([
            'date' => new \DateTime('now', new \DateTimeZone('UTC'))
        ]);

        /**
         * stop reroll at 10:30am
         */
        $currentTime = new \DateTime();
        if((int)$currentTime->format('H') > 10 || ((int)$currentTime->format('H') == 10 && (int)$currentTime->format('i') >= 30)) {
            $this->slack->message($privateResponseUrl, 'Uh oh! Looks like you tried to roll past the cut-off time.');
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        try {
            /**
             * don't allow rerolling on the last option.
             */
            if(sizeof($options) === 0) {
                $this->slack->message($privateResponseUrl, 'Uh oh! There aren\'t any lunch options. Did you forget to add some?');

            } elseif(sizeof($options) === 1) {
                $this->slack->message($responseUrl, sprintf(
                    'Uh oh! Looks like you\'re out of options. *<%s|%s>* it is! _(maybe you should add some more options to the roulette)_',
                    $options[0]->getUrl(),
                    $options[0]->getName()
                ));

            } else {
                /**
                 * make sure we have spins left.
                 */
                if(sizeof($history) < self::MAX_SPINS) {
                    /** @var Entity\LunchOption $option */
                    $option = $options[array_rand($options)];

                    $this->slack->message($responseUrl, sprintf(
                        'Someone spun the wheel! How does *<%s|%s>* sound instead? _(You have %d spins left today)_',
                        $option->getUrl(),
                        $option->getName(),
                        (self::MAX_SPINS - (sizeof($history) + 1))
                    ));

                    $history = new Entity\History();
                    $history->setLunchOption($option)
                        ->setSlackId($userId)
                        ->setSlackName($userName)
                    ;

                    /**
                     * save the history.
                     */
                    $this->manager->persist($history);
                    $this->manager->flush();

                } else {
                    $this->slack->message($privateResponseUrl, 'Whoops! You\'re out of spins for today.');
                }
            }

        } catch(\Exception $e) {
            return new Response('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function options(Request $request)
    {
        $responseUrl = $request->request->get('response_url');

        $names = "";
        $options = $this->manager->getRepository(Entity\LunchOption::class)->findAll();

        /** @var Entity\LunchOption $option */
        foreach($options as $option) {
            $tagNames = [];

            foreach($option->getTags() as $tag) {
                $tagNames[] = $tag->getTag()->getName();
            }

            $names .= sprintf("%s. %s _(%s)_\n",
                $option->getId(),
                $option->getName(),
                (sizeof($tagNames) > 0) ? implode(', ', $tagNames) : '*no tags*'
            );
        }

        try {
            $this->slack->message($responseUrl, sprintf(
                "Here are all the options currently in the lunch roulette: \n%s",
                $names
            ));

        } catch(\Exception $e) {
            return new Response('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function tags(Request $request)
    {
        $responseUrl = $request->request->get('response_url');

        $names = [];
        $tags = $this->manager->getRepository(Entity\Tag::class)->findAll();

        /** @var Entity\LunchOption $tag */
        foreach($tags as $tag) {
            $names[] = $tag->getName();
        }

        try {
            $this->slack->message($responseUrl, sprintf(
                "Here are all the available tags in the lunch roulette: \n_%s_",
                implode(', ', $names)
            ));

        } catch(\Exception $e) {
            return new Response('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addTag(Request $request)
    {
        $text = $request->request->get('text');
        $privateResponseUrl = $request->request->get('response_url');

        $params = explode(" ", $text);

        try {
            if(sizeof($params) < 2) {
                $this->slack->message($privateResponseUrl, 'There was something wrong with your input, please check and try again.');
            }

            /** @var Entity\LunchOption $option */
            $option = $this->manager->getRepository(Entity\LunchOption::class)->find((int)$params[0]);

            if($option === null) {
                $this->slack->message($privateResponseUrl, 'Sorry, I couldn\'t find a lunch option with that ID');

            } else {
                $tag = $this->manager->getRepository(Entity\Tag::class)->findOneBy([
                    'name' => strtolower($params[1])
                ]);

                if($tag === null) {
                    $tag = new Entity\Tag();
                    $tag->setName(strtolower($params[1]));

                    $this->manager->persist($tag);
                }

                $lunchTag = new Entity\LunchOptionTag();
                $lunchTag->setLunchOption($option)
                    ->setTag($tag)
                ;

                $this->manager->persist($lunchTag);

                /**
                 * save the option.
                 */

                $this->manager->flush();

                $this->slack->message($privateResponseUrl, sprintf(
                    'Added the _%s_ tag to *<%s|%s>*',
                    $tag->getName(),
                    $option->getUrl(),
                    $option->getName()
                ));
            }

        } catch(\Exception $e) {
            return new Response('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}