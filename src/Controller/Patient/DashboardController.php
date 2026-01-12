<?php

namespace App\Controller\Patient;

use App\Repository\ConsultationRepository;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/patient')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_patient_dashboard')]
    public function index(
        UserRepository $userRepository,
        ConsultationRepository $consultationRepository,
        RendezVousRepository $rendezVousRepository
    ): Response {
        // Vérifier que l'utilisateur est connecté et est patient
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        
        // Utiliser le patient connecté
        $patient = $this->getUser();

        // Rendez-vous à venir
        $now = new \DateTime();
        $rendezVousAvenir = $rendezVousRepository->createQueryBuilder('r')
            ->where('r.patient = :patient')
            ->andWhere('r.date >= :now')
            ->andWhere('r.statut != :cancelled')
            ->setParameter('patient', $patient)
            ->setParameter('now', $now)
            ->setParameter('cancelled', 'cancelled')
            ->orderBy('r.date', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Consultations récentes
        $consultationsRecentes = $consultationRepository->createQueryBuilder('c')
            ->where('c.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('c.date', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // Récupérer le médecin
        $medecin = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_MEDECIN%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('patient/dashboard/index.html.twig', [
            'patient' => $patient,
            'medecin' => $medecin,
            'rendezVousAvenir' => $rendezVousAvenir,
            'consultationsRecentes' => $consultationsRecentes,
        ]);
    }
}

