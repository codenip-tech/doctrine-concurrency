<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function sprintf;
use function uniqid;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 3000; $i++) {
            $user = new Customer();
            $name = uniqid();
            $user->setName($name);
            $user->setEmail(sprintf('%s@api.com', $name));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
