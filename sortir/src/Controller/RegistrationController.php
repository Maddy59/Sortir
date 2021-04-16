<?php

namespace App\Controller;

use App\Entity\User;
use League\Csv\Reader;
use App\Form\RegistrationCsv;
use App\Form\RegistrationForm;
use App\Security\AppAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register/csv", name="register_registerCsv")
     */
    public function registerCsv(Request $request, UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer): Response
    {

        $form = $this->createForm(RegistrationCsv::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $csvPath = $form->get('csv_file')->getData()->getlinkTarget();
            $csv = Reader::createFromPath($csvPath);
            $csv->setHeaderOffset(0);

            $em = $this->getDoctrine()->getManager();

            foreach ($csv as $row) {

                $user = $this->get('serializer')->deserialize(json_encode($row), User::class, 'json', [
                    ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
                $password = $user->getPassword();
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
                $em->persist($user);
            }
            $em->flush();
            $this->addFlash('succes', 'Utilisateur(s) ajouté(s).');
        }
        return $this->render('registration/registerCsv.html.twig', [
            'registrationCsv' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/form", name="register_registerForm")
     */
    public function registerForm(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('accueil_accueil');
        }

        return $this->render('registration/registerForm.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
