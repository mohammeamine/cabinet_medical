<?php

namespace App\Controller\Admin;

use App\Entity\RendezVous;
use App\Entity\User;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/rendez-vous')]
class RendezVousController extends AbstractController
{
    #[Route('', name: 'app_admin_rendez_vous_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        // Vérifier que l'utilisateur est connecté et est médecin
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');
        $rendezVous = $rendezVousRepository->findBy([], ['date' => 'DESC', 'heure' => 'DESC']);

        return $this->render('admin/rendez_vous/index.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }

    #[Route('/new', name: 'app_admin_rendez_vous_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur est connecté et est médecin
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');
        
        $rendezVous = new RendezVous();
        
        // Utiliser le médecin connecté
        $medecin = $this->getUser();
        $rendezVous->setMedecin($medecin);

        // Définir le statut par défaut à "scheduled" lors de la création
        $rendezVous->setStatut('scheduled');

        // Créer le formulaire sans le champ statut (show_statut = false par défaut)
        $form = $this->createForm(RendezVousType::class, $rendezVous, [
            'show_statut' => false, // Cacher le champ statut lors de la création
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // S'assurer que le statut est toujours "scheduled" lors de la création
            $rendezVous->setStatut('scheduled');

            $entityManager->persist($rendezVous);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous créé avec succès.');

            return $this->redirectToRoute('app_admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendez_vous/new.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_rendez_vous_show', methods: ['GET'])]
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('admin/rendez_vous/show.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_rendez_vous_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        RendezVous $rendezVous,
        EntityManagerInterface $entityManager
    ): Response {
        // Afficher le champ statut lors de l'édition
        $form = $this->createForm(RendezVousType::class, $rendezVous, [
            'show_statut' => true, // Afficher le champ statut lors de l'édition
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous modifié avec succès.');

            return $this->redirectToRoute('app_admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendez_vous/edit.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_rendez_vous_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        RendezVous $rendezVous,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $entityManager->remove($rendezVous);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous supprimé avec succès.');

            return $this->redirectToRoute('app_admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendez_vous/delete.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }
}

