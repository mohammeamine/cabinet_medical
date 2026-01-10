<?php

namespace App\Controller\Admin;

use App\Repository\ConsultationRepository;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        ConsultationRepository $consultationRepository,
        RendezVousRepository $rendezVousRepository
    ): Response {
        // Statistiques
        $totalPatients = $userRepository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PATIENT%')
            ->getQuery()
            ->getSingleScalarResult();

        $totalConsultations = $consultationRepository->count([]);

        // Rendez-vous du jour
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        $rendezVousAujourdhui = $rendezVousRepository->createQueryBuilder('r')
            ->where('r.date >= :today')
            ->andWhere('r.date < :tomorrow')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->getQuery()
            ->getResult();

        return $this->render('admin/dashboard/index.html.twig', [
            'totalPatients' => $totalPatients,
            'totalConsultations' => $totalConsultations,
            'rendezVousAujourdhui' => count($rendezVousAujourdhui),
        ]);
    }
}

