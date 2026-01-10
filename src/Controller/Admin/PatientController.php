<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\PatientType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/patients')]
class PatientController extends AbstractController
{
    #[Route('', name: 'app_admin_patient_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $patients = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PATIENT%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/patient/index.html.twig', [
            'patients' => $patients,
        ]);
    }

    #[Route('/new', name: 'app_admin_patient_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $patient = new User();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assigner ROLE_PATIENT
            $patient->setRoles(['ROLE_PATIENT']);
            // Générer un mot de passe par défaut (à changer plus tard)
            $patient->setPassword($passwordHasher->hashPassword($patient, 'patient123'));

            $entityManager->persist($patient);
            $entityManager->flush();

            $this->addFlash('success', 'Patient créé avec succès.');

            return $this->redirectToRoute('app_admin_patient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/patient/new.html.twig', [
            'patient' => $patient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_patient_show', methods: ['GET'])]
    public function show(User $patient): Response
    {
        return $this->render('admin/patient/show.html.twig', [
            'patient' => $patient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_patient_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $patient,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Patient modifié avec succès.');

            return $this->redirectToRoute('app_admin_patient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/patient/edit.html.twig', [
            'patient' => $patient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_patient_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        User $patient,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $entityManager->remove($patient);
            $entityManager->flush();

            $this->addFlash('success', 'Patient supprimé avec succès.');

            return $this->redirectToRoute('app_admin_patient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/patient/delete.html.twig', [
            'patient' => $patient,
        ]);
    }
}

