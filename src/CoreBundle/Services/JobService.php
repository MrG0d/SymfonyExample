<?php

namespace CoreBundle\Services;

use CoreBundle\Entity\CopywritingArticleComment;
use CoreBundle\Entity\DirectoryBacklinks;
use CoreBundle\Entity\Job;
use CoreBundle\Entity\NetlinkingProjectComments;
use CoreBundle\Entity\ScheduleTask;
use CoreBundle\Entity\User;
use CoreBundle\Exceptions\WorkflowTransitionEntityException;
use CoreBundle\Model\TransactionDescriptionModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\StateMachine;
use UserBundle\Services\NetlinkingService;

/**
 * Class JobService
 *
 * @package CoreBundle\Services
 */
class JobService
{
    /** @var EntityManager $em */
    private $em;

    /** @var TransactionService $transactionService */
    private $transactionService;

    /** @var StateMachine $exchangePropositionWorkflow */
    private $jobWorkflow;

    /** @var Security */
    private $security;

    /**
     * ExchangePropositionService constructor.
     *
     * @param EntityManager $entityManager
     * @param TransactionService $transactionService
     * @param StateMachine $jobWorkflow
     * @param Security $security
     */
    public function __construct(
        EntityManager $entityManager,
        TransactionService $transactionService,
        StateMachine $jobWorkflow,
        Security $security
    ) {
        $this->em = $entityManager;
        $this->transactionService = $transactionService;
        $this->jobWorkflow = $jobWorkflow;
        $this->security = $security;
    }

    /**
     * @param Job $job
     * @param $transition
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function applyTransition(Job $job, $transition)
    {
        if ($this->jobWorkflow->can($job, $transition)) {
            $this->jobWorkflow->apply($job, $transition);
            $this->em->flush();
        } else {
            throw new WorkflowTransitionEntityException($job, $transition);
        }
    }

    /**
     * @param Job $job
     * @param User $writer
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function takeToWorkJob(Job $job, User $writer)
    {
        $job->setAffectedToUser($writer);
        $this->applyTransition($job, Job::TRANSITION_TAKE_TO_WORK);
    }

    /**
     * @param Job $job
     *
     * @throws \CoreBundle\Exceptions\UnknownTransactionTagNameException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function rewardWriter(Job $job)
    {
        $transactionReward = $this->transactionService->handling(
            $job->getUser(),
            new TransactionDescriptionModel(
                'job.writerReward',
                [
                    '%url%' => $job->getDirectory()->getUrl(),
                ]
            ),
            $job->getCostWriter(),
            0
        );

        $job->addTransaction($transactionReward);


        $this->em->flush();
    }
}
