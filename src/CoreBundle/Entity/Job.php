<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\JobRepository")
 */
class Job extends AbstractEntityTransaction
{

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_IMPOSSIBLE = 'impossible';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    const TRANSITION_TAKE_TO_WORK = 'take_to_work';
    const TRANSITION_IMPOSSIBLE = 'impossible';
    const TRANSITION_COMPLETE = 'complete';
    const TRANSITION_REJECT = 'reject';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

   /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     **/
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default": Job::STATUS_NEW})
     */
    private $status = Job::STATUS_NEW;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $rejectedAt;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $costWriter;

    /**
     * @var Directory
     *
     * @ORM\ManyToOne(targetEntity="Directory", cascade={"persist"})
     */
    private $directory;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Job
     */
    public function setUser(?User $user): Job
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Job
     */
    public function setStatus(?string $status): Job
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    /**
     * @param \DateTime $completedAt
     *
     * @return Job
     */
    public function setCompletedAt(?\DateTime $completedAt): Job
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRejectedAt(): ?\DateTime
    {
        return $this->rejectedAt;
    }

    /**
     * @param \DateTime $rejectedAt
     *
     * @return Job
     */
    public function setRejectedAt(?\DateTime $rejectedAt): Job
    {
        $this->rejectedAt = $rejectedAt;

        return $this;
    }

    /**
     * @return float
     */
    public function getCostWriter(): ?float
    {
        return $this->costWriter;
    }

    /**
     * @param float $costWriter
     *
     * @return Job
     */
    public function setCostWriter(?float $costWriter): Job
    {
        $this->costWriter = $costWriter;

        return $this;
    }

    /**
     * @return Directory
     */
    public function getDirectory(): ?Directory
    {
        return $this->directory;
    }

    /**
     * @param Directory $directory
     *
     * @return Job
     */
    public function setDirectory(?Directory $directory): Job
    {
        $this->directory = $directory;

        return $this;
    }
}
