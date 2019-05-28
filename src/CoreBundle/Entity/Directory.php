<?php

namespace CoreBundle\Entity;

use CoreBundle\Entity\Interfaces\SiteUrlInterface;
use CoreBundle\Entity\Traits\SiteTrait;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Entity\Constant\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * Directory
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\DirectoryRepository")
 */
class Directory implements SiteUrlInterface
{
    use SiteTrait;
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $pricePerDay;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", option={"default": 0})
     */
    private $dailyLimit = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default": Language::EN})
     *
     * @Assert\Choice(callback={"CoreBundle\Entity\Constant\Language", "getAll"})
     */
    protected $language = Language::EN;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Directory
     */
    public function setUrl(?string $url): Directory
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return float
     */
    public function getPricePerDay(): ?float
    {
        return $this->pricePerDay;
    }

    /**
     * @param float $pricePerDay
     *
     * @return Directory
     */
    public function setPricePerDay(?float $pricePerDay): Directory
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }

    /**
     * @return int
     */
    public function getDailyLimit(): ?int
    {
        return $this->dailyLimit;
    }

    /**
     * @param int $dailyLimit
     *
     * @return Directory
     */
    public function setDailyLimit(?int $dailyLimit): Directory
    {
        $this->dailyLimit = $dailyLimit;

        return $this;
    }
}
