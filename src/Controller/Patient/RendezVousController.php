<?php

namespace App\Controller\Patient;

use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/patient/rendez-vous')]
class RendezVousController extends AbstractController
{
    #[Route('', name: 'app_patient_rendez_vous_index', methods: ['GET'])]
    public function index(
        RendezVousRepository $rendezVousRepository,
        UserRepository $userRepository
    ): Response {
        // Pour l'instant, récupérer le premier patient (sans auth)
        $patient = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PATIENT%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$patient) {
            throw $this->createNotFoundException('Aucun patient trouvé');
        }

        $rendezVous = $rendezVousRepository->createQueryBuilder('r')
            ->where('r.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('patient/rendez_vous/index.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }
}

