<?php

namespace CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CoreBundle\Entity\Settings;

/**
 * Class LoadSettings
 *
 * @package CoreBundle\DataFixtures\ORM
 */
class LoadSettings extends AbstractFixture implements FixtureInterface
{

    private $settings = [
        ['key' => Settings::BONUS_AFTER_REGISTRATION, 'value' => 100],
        ['key' => Settings::WITHDRAWAL_COMISSION, 'value' => 5],
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->settings as $setting) {
            $entity = $this->findOrCreateEntity($setting['key'], $manager);

            if (!$manager->contains($entity)) {
                $entity
                    ->setName($setting['name'])
                    ->setKey($setting['key'])
                    ->setValue($setting['value']);

                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * @param string        $identificator
     * @param ObjectManager $manager
     *
     * @return Settings
     */
    protected function findOrCreateEntity($identificator, ObjectManager $manager)
    {
        return $manager->getRepository(Settings::class)->findOneBy(['identificator' => $identificator]) ?: new Settings();
    }
}
