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
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser('fos_user.user_manager');
        $form = $this->createForm(ProfilForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoPath = $form->get('photo')->getData();
            if ($photoPath) {
                $photoPath->getlinkTarget();
                $photo = file_get_contents($photoPath);
                $base64 = base64_encode($photo);
                $user->setPhoto($base64);
            }
            $em->flush();
        }

        return $this->render('profil/profil.html.twig', [
            'ProfilForm' => $form->createView(),
        ]);
    }
}
