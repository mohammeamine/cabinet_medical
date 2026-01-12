<?php

namespace App\Controller\Patient;

use App\Repository\ConsultationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/patient/consultations')]
class ConsultationController extends AbstractController
{
    #[Route('', name: 'app_patient_consultation_index', methods: ['GET'])]
    public function index(
        ConsultationRepository $consultationRepository
    ): Response {
        // Vérifier que l'utilisateur est connecté et est patient
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        
        // Utiliser le patient connecté
        $patient = $this->getUser();

        $consultations = $consultationRepository->createQueryBuilder('c')
            ->where('c.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('patient/consultation/index.html.twig', [
            'consultations' => $consultations,
        ]);
    }
}

