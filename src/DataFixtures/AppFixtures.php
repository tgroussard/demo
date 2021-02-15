<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encode;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encode = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        /**
         * Create campus
         */
        $campusNames = ["Niort", "Nantes", "Rennes"];
        for($i = 0; $i < sizeof($campusNames); $i++){
            $campus = new Campus();
            $campus->setName($campusNames[$i]);
            $manager->persist($campus);
        }

        $manager->flush();

        /**
         * Create users
         */

        $user = new User();
        $user->setPseudo("Alexandre M");
        $user->setName("MAUL");
        $user->setFirstname("Alexandre");
        $user->setPhonenumber("0505050505");
        $user->setEmail("am@mail.com");
        $user->setPassword(
            $this->encode->encodePassword($user, "password")
        );
        $user->setCampus($manager->getRepository(Campus::class)->findOneBy(array('name' => "Niort")));
        $manager->persist($user);

        $user = new User();
        $user->setPseudo("Wilfreed A");
        $user->setName("AUDOUIN");
        $user->setFirstname("Wilfreed");
        $user->setPhonenumber("0505050505");
        $user->setEmail("wa@mail.com");
        $user->setPassword(
            $this->encode->encodePassword($user, "password")
        );
        $user->setCampus($manager->getRepository(Campus::class)->findOneBy(array('name' => "Niort")));
        $manager->persist($user);

        $manager->flush();

        /**
         * Create cities
         */
        $city = new City();
        $city->setName("Niort");
        $city->setPostalCode(79000);
        $manager->persist($city);

        $manager->flush();

        /**
         * Create places
         */
        $place = new Place();
        $place->setName("Temple Bar");
        $place->setStreet("4 Esplanade de la République");
        $place->setCity($manager->getRepository(Place::class)->find(0));
        $place->setLatitude("46.324076");
        $place->setLongitude("0.4591839");
        $manager->persist($place);

        $manager->flush();

        /**
         * Create states
         */
        $stateLabel = ["Créee", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        for($i = 0; $i < sizeof($stateLabel); $i++){
            $state = new State();
            $state->setLabel($stateLabel[$i]);
            $manager->persist($state);
        }

        $manager->flush();
    }
}
