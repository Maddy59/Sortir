<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationCsv;
use App\Form\RegistrationForm;
use App\Security\AppAuthenticator;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("register/", name="register_")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("csv", name="csv")
     */
    public function csv(Request $request, UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer): Response
    {


        return $this->render('registration/csv.html.twig', [

        ]);
    }

    /**
     * @Route("form", name="form")
     */
    public function form(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $formUnitaire = $this->createForm(RegistrationForm::class, $user);
        $formUnitaire->handleRequest($request);

        if ($formUnitaire->isSubmitted() && $formUnitaire->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $formUnitaire->get('password')->getData()
                )
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('accueil_accueil');
        }

        $formCSV = $this->createForm(RegistrationCsv::class);
        $formCSV->handleRequest($request);

        if ($formCSV->isSubmitted() && $formCSV->isValid()) {

            $csvPath = $formCSV->get('csv_file')->getData()->getlinkTarget();
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
        return $this->render('registration/form.html.twig', [
            'registrationForm' => $formUnitaire->createView(),
            'registrationCsv' => $formCSV->createView(),
        ]);
    }
}
