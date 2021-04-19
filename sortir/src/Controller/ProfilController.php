<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfilForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("profil/", name="profil_")
 */
class ProfilController extends AbstractController
{

    /**
     * @Route("edit", name="edit")
     */
    public function edit(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser('fos_user.user_manager');
        $form = $this->createForm(EditProfilForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoData = $form->get('photo')->getData();
            if ($photoData) {
                $photo = file_get_contents($photoData);
                $base64 = base64_encode($photo);
                $user->setPhoto($base64);
            }
            $em->flush();
        }

        return $this->render('profil/edit.html.twig', [
            'EditProfilForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("show/{id}", name="show")
     */
    public function show(User $user): Response
    {

        $photo = 'data:image/png;base64,' . $user->getPhoto();

        return $this->render('profil/show.html.twig', [
            'user' => $user,
            'photo' => $photo,
        ]);
    }

}
