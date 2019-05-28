<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Job;
use CoreBundle\Entity\User;

use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\EntityNotFoundException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class JobController
 *
 * @package CoreBundle\Controller
 */
class JobController extends Controller
{



    /**
     * @param Request $request
     * @param $jobId
     *
     * @return JsonResponse
     *
     * @throws OptimisticLockException
     */
    public function rejectAction(Request $request, $jobId)
    {
        /** @var JobRepository $jobRepository */
        $jobRepository = $this->getDoctrine()->getRepository(Job::class);
        /** @var Job $job */
        $job = $jobRepository->find($jobId);

        $this->denyAccessUnlessGranted(JobVoter::ACTION_REJECT, $job);


        if (is_null($job)) {
            throw new NotFoundHttpException();
        }

        $this->get('core.service.job')->rejectJob($job, $request->get('comment'));

        return $this->json([
            'status' => true,
            'message' => $this->container->get('translator')->trans('job_rejected', [], 'job'),
        ]);
    }

    /**
     * @param int $scheduleTaskId
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function doAction($jobId)
    {
        /** @var JobRepository $jobRepository */
        $jobRepository = $this->getDoctrine()->getRepository(Job::class);
        /** @var Job $job */
        $job = $jobRepository->find($jobId);

        $this->denyAccessUnlessGranted(JobVoter::ACTION_DO, $job);

        $this->get('core.service.job')->takeToWorkJob($job, $this->getUser());

        return $this->json([
            'status' => true,
            'message' => $this->get('translator')->trans('modal.take_to_work_success', [], 'job'),
        ]);
    }

}
