<?php

namespace App\Controller;

use App\Form\ProfilForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MonProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil_profil")
     */
    public function profil(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoPath = $form->get('photo')->getData()->getlinkTarget();

            $photo = file_get_contents($photoPath);
            $base64 = base64_encode($photo);

            $user->setPhoto($base64);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

        }

        return $this->render('profil/profil.html.twig', [
            'ProfilForm' => $form->createView(),
        ]);
    }
}
