<?php

namespace CoreBundle\EventListener\Workflow;

use CoreBundle\Entity\Job;
use CoreBundle\Model\TransactionDescriptionModel;
use CoreBundle\Services\JobService;
use CoreBundle\Services\TransactionService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\Event\Event as WorkflowEvent;

/**
 * Class JobListener
 *
 * @package UserBundle\EventListener
 */
class JobListener
{

    /** @var EntityManager */
    private $em;

    /** @var TransactionService */
    private $transactionService;

    /** @var JobService */
    private $jobService;

    /**
     * ExchangePropositionListener constructor.
     *
     * @param EntityManager $entityManager
     * @param TransactionService $transactionService
     * @param JobService $jobService
     */
    public function __construct(
        EntityManager $entityManager,
        TransactionService $transactionService,
        JobService $jobService
    ) {
        $this->em = $entityManager;
        $this->transactionService = $transactionService;
        $this->jobService = $jobService;
    }

    /**
     * @param WorkflowEvent $event
     *
     * @throws \Exception
     */
    public function onTakeToWork(WorkflowEvent $event)
    {

    }

    /**
     * @param WorkflowEvent $event
     *
     * @throws \Exception
     */
    public function onImpossible(WorkflowEvent $event)
    {

    }

    /**
     * @param WorkflowEvent $event
     *
     * @throws \CoreBundle\Exceptions\UnknownTransactionTagNameException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onComplete(WorkflowEvent $event)
    {
        /** @var Job $job */
        $job = $event->getSubject();

        $this->jobService->rewardWriter($job);
    }

    /**
     * @param WorkflowEvent $event
     *
     * @throws \Exception
     */
    public function onReject(WorkflowEvent $event)
    {

    }
}
