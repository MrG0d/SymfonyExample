<?php

namespace CoreBundle\Entity\Interfaces;

use CoreBundle\Entity\Site;

interface SiteUrlInterface
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return Site
     */
    public function getSite();

    /**
     * @param Site|null $site
     *
     * @return mixed
     */
    public function setSite(?Site $site);
}
