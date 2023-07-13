<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 10:50 AM
 */

namespace App\Entity;


/**
 * Class LunchOptionTag
 * @package App\Entity
 */
class LunchOptionTag
{
    /** @var int */
    private $id;

    /** @var LunchOption */
    private $lunchOption;

    /** @var Tag */
    private $tag;

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
     * @return LunchOptionTag
     */
    public function setLunchOption(LunchOption $lunchOption): self
    {
        $this->lunchOption = $lunchOption;

        return $this;
    }

    /**
     * @return Tag
     */
    public function getTag(): Tag
    {
        return $this->tag;
    }

    /**
     * @param Tag $tag
     * @return LunchOptionTag
     */
    public function setTag(Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}