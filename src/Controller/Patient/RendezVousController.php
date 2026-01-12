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
        RendezVousRepository $rendezVousRepository
    ): Response {
        // Vérifier que l'utilisateur est connecté et est patient
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        
        // Utiliser le patient connecté
        $patient = $this->getUser();

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

