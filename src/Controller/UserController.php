<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="app_profil")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function profil(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $error = null;
        $update = false;

        $form = $this->createForm(ProfilType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if (strlen($password) !== 0) {
                if ($password !== $confirmPassword) {
                    $error = "Mots de passe différents.";
                } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $password)) {
                    $error = "Mot de passe trop faible :<br>
                    - Au moins 6 caractères<br>
                    - Au moins une lettre et un chiffre";
                } else {
                    $user->setPassword($encoder->encodePassword($user, $password));
                }
            }

            $phonenumber = $form->get('phonenumber')->getData();

            if ((strlen($phonenumber) !== 0 && strlen($phonenumber) !== 10) || !preg_match("/^[0-9]{10}$/", $phonenumber)){
                $error = "Numéro de téléphone invalide.";
            }

            if (!$error) {
                $manager->persist($user);
                $manager->flush();
                $update = true;
            }
        }

        return $this->render('user/profil.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'update' => $update,
        ]);
    }

    /**
     * @Route("/user/{id}", name="app_user_show")
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        if($this->getUser() == $user){
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }
}
