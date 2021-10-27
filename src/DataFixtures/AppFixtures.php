<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface  $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $statuts = ['En création', 'Fermé', 'Annulé', 'En cours'];

        foreach ($statuts as $statut){
            $etat = new Etat();
            $etat->setEtat($statut);
            $manager->persist($etat);
        }

        $etatOuvert = new Etat();
        $etatOuvert->setEtat('Ouvert');
        $manager->persist($etatOuvert);


        for ($i = 1; $i < 6; $i++) {

            $ville = new Ville();
            $ville->setNom($faker->city())
                ->setCodePostal(mt_rand(10000,95000));

            $manager->persist($ville);

            $utilisateur = new Utilisateur();
            $utilisateur
                ->setPseudo('user'.$i)
                ->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setTelephone('0102030405')
                ->setVille($ville)
                ->setEmail($faker->email());
            $utilisateur->setIsExpired(0);

            $password = $this->encoder->encodePassword($utilisateur, 'user'.$i);
            $utilisateur->setPassword($password);

            $manager->persist($utilisateur);

            for ($l=0; $l < 2; $l++){
                $lieu = new Lieu();

                $lieu->setNom($faker->streetAddress())
                    ->setVille($ville)
                    ->setLatitude($faker->latitude())
                    ->setLongitude($faker->longitude())
                    ->setRue($faker->streetName());

                $manager->persist($lieu);

                $sortie = new Sortie();

                $sortie->setNom($faker->sentence(3,true))
                    ->setDuree($faker->numberBetween(10,60))
                    ->setDate(new \DateTime('+3 month'))
                    ->setDateCloture(new \DateTime('+84 day'))
                    ->setNombrePlaces($faker->numberBetween(3,100))
                    ->setDescription($faker->text(100))
                    ->setArchive(0)
                    ->setCreateur($utilisateur)
                    ->setLieu($lieu)
                    ->setEtat($etatOuvert);

                $manager->persist($sortie);
            }
        }

        $manager->flush();
    }
}
