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
        ConsultationRepository $consultationRepository,
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

