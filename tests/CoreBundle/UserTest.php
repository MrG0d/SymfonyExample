<?php

namespace Tests\CoreBundle;

use CoreBundle\DataFixtures\ORM as ORM;
use CoreBundle\DataFixtures\Test\UserData;
use CoreBundle\Entity\Job;
use CoreBundle\Entity\User;
use CoreBundle\Exceptions\UnknownNotificationName;
use CoreBundle\Services\JobService;
use Tests\AbstractTest;
use Tests\ParamWrapper;

class UserTest extends AbstractTest
{
    /**
     * @dataProvider notificationArticleReadyProvider
     *
     * @param Job $job
     * @param $notificationEnabled
     * @throws \Exception
     */
    public function testNotificationArticleReady($job, $notificationEnabled)
    {
        $fixtures = [
            UserData::class,
            ORM\LoadSettings::class,
        ];

        $this->loadFixtures($fixtures);

        $this->setUser($job->getUser());

        $job->getUser()->setNotificationEnabled(User::NOTIFICATION_JOB_COMPLETED, $notificationEnabled);

        $this->enableMessageLogger();

        /** @var JobService $jobService */
        $jobService = $this->container()->get('core.service.job');
        $jobService->applyTransition($job, Job::TRANSITION_COMPLETE);

        if ($notificationEnabled === User::NOTIFICATION_ON) {
            $this->assertSame(1, $this->messageLogger()->countMessages(), 'The letter has not been sent');

            $message = $this->getMessage();
            $this->assertArrayHasKey($job->getUser()->getEmail(), $message->getTo());
        } else {
            $this->assertSame(0, $this->messageLogger()->countMessages(), 'Message sent with notification disabled');
        }
    }

    public function notificationArticleReadyProvider()
    {
        return [
            [['title' => 'P#1-O#1'], User::NOTIFICATION_ON],
            [['title' => 'P#1-O#1'], User::NOTIFICATION_OFF],
        ];
    }
}
