<?php

namespace App\DataFixtures;

use App\Entity\Consultation;
use App\Entity\RendezVous;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1) Médecin (admin)
        $medecin = new User();
        $medecin->setEmail('medecin@cabinet.local');
        $medecin->setNom('Dupont');
        $medecin->setPrenom('Jean');
        $medecin->setTele('0102030405');
        $medecin->setRoles(['ROLE_MEDECIN']);
        $medecin->setPassword(
            $this->passwordHasher->hashPassword($medecin, 'medecin123')
        );
        $manager->persist($medecin);

        // 2) Patients
        $patients = [];
        for ($i = 0; $i < 12; $i++) {
            $patient = new User();
            $patient->setEmail($faker->unique()->email());
            $patient->setNom($faker->lastName());
            $patient->setPrenom($faker->firstName());
            $patient->setTele($faker->phoneNumber());
            $patient->setRoles(['ROLE_PATIENT']);
            $patient->setPassword(
                $this->passwordHasher->hashPassword($patient, 'patient123')
            );
            $patients[] = $patient;
            $manager->persist($patient);
        }

        // 3) Rendez-vous (prochains et passés)
        $statuts = ['scheduled', 'completed', 'cancelled'];
        $rendezVous = [];
        for ($i = 0; $i < 20; $i++) {
            $rdv = new RendezVous();
            $rdv->setMedecin($medecin);
            $rdv->setPatient($faker->randomElement($patients));
            $rdvDate = $faker->dateTimeBetween('-1 month', '+2 months');
            $rdv->setDate($rdvDate);
            $rdv->setHeure($faker->dateTimeBetween('today 08:00', 'today 18:00'));
            $rdv->setStatut($faker->randomElement($statuts));
            $rendezVous[] = $rdv;
            $manager->persist($rdv);
        }

        // 4) Consultations (passées)
        for ($i = 0; $i < 15; $i++) {
            $consultation = new Consultation();
            $consultation->setMedecin($medecin);
            $consultation->setPatient($faker->randomElement($patients));
            $consultation->setDate($faker->dateTimeBetween('-6 months', 'now'));
            $consultation->setNote($faker->realText(350));
            $consultation->setOrdonnance($faker->realText(200));
            $manager->persist($consultation);
        }

        $manager->flush();
    }
}
