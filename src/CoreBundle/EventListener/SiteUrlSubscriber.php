<?php

namespace CoreBundle\EventListener;

use CoreBundle\Entity\Interfaces\SiteUrlInterface;
use CoreBundle\Entity\Site;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events as DoctrineEvent;

/**
 * Class SiteUrlSubscriber
 *
 * @package CoreBundle\EventListener
 */
class SiteUrlSubscriber implements EventSubscriber
{

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            DoctrineEvent::prePersist,
            DoctrineEvent::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if (!$args->getEntity() instanceof SiteUrlInterface) {
            return;
        }
        $this->updateSite($args->getEntity(), $args);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if (!$args->getEntity() instanceof SiteUrlInterface || !$args->hasChangedField('url')) {
            return;
        }

        $this->updateSite($args->getEntity(), $args);
    }


    /**
     * @param object|SiteUrlInterface $entity
     * @param LifecycleEventArgs $args
     */
    private function updateSite(SiteUrlInterface $entity, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $siteRepository = $em->getRepository(Site::class);
        $site = $siteRepository->findOrCreateByUrl($entity->getUrl());

        $entity->setSite($site);
    }
}
