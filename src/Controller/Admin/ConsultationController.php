<?php

namespace App\Controller\Admin;

use App\Entity\Consultation;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/consultations')]
class ConsultationController extends AbstractController
{
    #[Route('', name: 'app_admin_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        // Vérifier que l'utilisateur est connecté et est médecin
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');
        $consultations = $consultationRepository->findBy([], ['date' => 'DESC']);

        return $this->render('admin/consultation/index.html.twig', [
            'consultations' => $consultations,
        ]);
    }

    #[Route('/new', name: 'app_admin_consultation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur est connecté et est médecin
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');
        
        $consultation = new Consultation();
        
        // Utiliser le médecin connecté
        $medecin = $this->getUser();
        $consultation->setMedecin($medecin);

        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            $this->addFlash('success', 'Consultation créée avec succès.');

            return $this->redirectToRoute('app_admin_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/consultation/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        return $this->render('admin/consultation/show.html.twig', [
            'consultation' => $consultation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Consultation $consultation,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Consultation modifiée avec succès.');

            return $this->redirectToRoute('app_admin_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_consultation_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Consultation $consultation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $entityManager->remove($consultation);
            $entityManager->flush();

            $this->addFlash('success', 'Consultation supprimée avec succès.');

            return $this->redirectToRoute('app_admin_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/consultation/delete.html.twig', [
            'consultation' => $consultation,
        ]);
    }
}

