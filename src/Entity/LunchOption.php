<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 10:40 AM
 */

namespace App\Entity;


use App\Entity\Abstraction\Persistent;

/**
 * Class LunchOption
 * @package App\Entity
 */
class LunchOption
{
    use Persistent;

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var string */
    private $slackId;

    /** @var string */
    private $slackName;

    /** @var \DateTime */
    private $dateCreated;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return LunchOption
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     * @return LunchOption
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlackId(): string
    {
        return $this->slackId;
    }

    /**
     * @param string $id
     * @return LunchOption
     */
    public function setSlackId(string $id): self
    {
        $this->slackId = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlackName(): string
    {
        return $this->slackName;
    }

    /**
     * @param string $name
     * @return LunchOption
     */
    public function setSlackName(string $name): self
    {
        $this->slackName = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return new \DateTime($this->dateCreated->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
    }

    /**
     * @return $this
     */
    public function setDefaultDateCreated()
    {
        $this->dateCreated = new \DateTime('now', new \DateTimeZone('UTC'));

        return $this;
    }

    /**
     * @return LunchOptionTag[]
     */
    public function getTags()
    {
        /** @var LunchOptionTag[] $tags */
        $tags = $this->getManager()->getRepository(LunchOptionTag::class)->findBy([
            'lunchOption' => $this->getId()
        ]);

        return $tags;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function hasTag(string $tag): bool
    {
        $hasTag = false;

        foreach($this->getTags() as $optionTag) {
            if($optionTag->getTag()->getName() == $tag) {
                $hasTag = true;

                break;
            }
        }

        return $hasTag;
    }
}