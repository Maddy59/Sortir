<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\GestionUtilisateurType;
use App\Form\SearchFormSortie;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/profilutilisateurs", name="admin_profilutilisateurs")
     */
    public function profilUtilisateurs(UserRepository $userRepository, Request $request): Response
    {
        $users = [];
        $user = new User();
        $gestionForm = $this->createForm(GestionUtilisateurType::class, $user);
        $gestionForm->handleRequest($request);

        if ($gestionForm->isSubmitted() && $gestionForm->isValid()) {
            $users =  $userRepository->getUserByFilter($user->getNom());
        } else {
            $users = $userRepository->findAll();
        }
        return $this->render('admin/adminProfilUtilisateurs.html.twig', [
            'users' => $users,
            'gestionForm' => $gestionForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/activeDesactive/{id}", name="admin_activedesactive")
     */
    public function activeDesactiveUtilisateur($id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $userRepository->find($id);

        $roles = $user->getRoles();
        if ($user->getActif()) {
            $user->setActif(false);
        } else {
            $user->setActif(true);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }


}
