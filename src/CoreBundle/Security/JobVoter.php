<?php

namespace UserBundle\Security;

use CoreBundle\Entity\Job;
use CoreBundle\Services\AccessManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use CoreBundle\Entity\User;

/**
 * Class NetlinkingProjectVoter
 *
 * @package UserBundle\Voter
 */
class JobVoter extends AbstractVoter
{
    public const ACTION_REJECT = 'reject';
    public const ACTION_DO = 'do';

    /**
     * @param string $attribute
     * @param Job $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::ACTION_DO:
                return $user->isWebmaster() && $this->accessManager->canManageJob();
            case self::ACTION_REJECT:
                return !($user->isModerator() && !$this->accessManager->canManageJob());
        }

        throw new \LogicException('This code should not be reached!');
    }
}