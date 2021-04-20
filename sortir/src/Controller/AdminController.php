<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\GestionUtilisateurType;
use App\Form\ModifierRoleType;
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
    public function profilUtilisateurs(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
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
     * @Route("/admin/modifierRoles/{id}", name="admin_modifier_role")
     */
    public function activeDesactiveUtilisateur($id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {

        $user = $userRepository->find($id);

        $roleForm = $this->createForm(ModifierRoleType::class, $user);
        $roleForm->handleRequest($request);

        if($roleForm->isSubmitted() && $roleForm->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin_profilutilisateurs');
        }
        return $this->render('admin/modifierRoles.html.twig', [
            'user' => $user,
            'rolesForm' => $roleForm->createView(),
        ]);
    }


}
