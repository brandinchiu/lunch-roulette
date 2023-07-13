<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 10:44 AM
 */

namespace App\Entity;


/**
 * Class History
 * @package App\Entity
 */
class History
{
    /** @var int */
    private $id;

    /** @var LunchOption */
    private $lunchOption;

    /** @var string */
    private $slackId;

    /** @var string */
    private $slackName;

    /** @var \DateTime */
    private $date;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return LunchOption
     */
    public function getLunchOption(): LunchOption
    {
        return $this->lunchOption;
    }

    /**
     * @param LunchOption $lunchOption
     * @return History
     */
    public function setLunchOption(LunchOption $lunchOption): self
    {
        $this->lunchOption = $lunchOption;

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
     * @return History
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
     * @return History
     */
    public function setSlackName(string $name): self
    {
        $this->slackName = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return new \DateTime($this->date->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
    }

    /**
     * @return $this
     */
    public function setDefaultDate()
    {
        $this->date = new \DateTime('now', new \DateTimeZone('UTC'));

        return $this;
    }
}