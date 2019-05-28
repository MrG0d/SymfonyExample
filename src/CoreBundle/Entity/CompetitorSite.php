<?php

namespace CoreBundle\Entity;

use CoreBundle\Entity\Interfaces\SiteUrlInterface;
use CoreBundle\Entity\Traits\ExternalIdTrait;
use CoreBundle\Entity\Traits\SiteTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CompetitorSite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\CompetitorSiteRepository")
 * @UniqueEntity(
 *     fields={"url"},
 *     repositoryMethod="constraintSiteDuplicate",
 *     message="exchange.duplicate_site"
 * )
 */
class CompetitorSite implements SiteUrlInterface
{
    use SiteTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(max="255")
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hideUrl;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotBlank()
     */
    private $credits = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;


    /**
     * @return int
     */
    public function getId()
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
     * @return CompetitorSite
     */
    public function setUrl(?string $url): CompetitorSite
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHideUrl(): ?bool
    {
        return $this->hideUrl;
    }

    /**
     * @param bool $hideUrl
     *
     * @return CompetitorSite
     */
    public function setHideUrl(?bool $hideUrl): CompetitorSite
    {
        $this->hideUrl = $hideUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getCredits(): ?int
    {
        return $this->credits;
    }

    /**
     * @param int $credits
     *
     * @return CompetitorSite
     */
    public function setCredits(?int $credits): CompetitorSite
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return CompetitorSite
     */
    public function setActive(?bool $active): CompetitorSite
    {
        $this->active = $active;

        return $this;
    }
}
