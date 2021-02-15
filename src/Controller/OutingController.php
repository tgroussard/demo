<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Outing;
use App\Entity\Place;
use App\Entity\State;
use App\Form\AbortType;
use App\Form\OutingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingController extends AbstractController
{
    /**
     * @Route("/outing/create", name="app_outing_create")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $campus = $manager->getRepository(Campus::class)->findAll();
        $places = $manager->getRepository(Place::class)->findAll();
        $cities = $manager->getRepository(City::class)->findAll();

        $formOption = ['campus' => [], 'places' => [], 'cities' => []];
        foreach ($campus as $camp) {
            $formOption['campus'][$camp->getName()] = $camp;
        }

        foreach ($places as $place) {
            $formOption['places'][$place->getName()] = $place;
        }

        foreach ($cities as $city) {
            $formOption['cities'][$city->getName()] = $city;
        }

        $outing = new Outing();
        $form = $this->createForm(OutingType::class, $outing, $formOption);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $state = $manager->getRepository(State::class)->findOneBy(array('label' => "Créee"));

            $outing->setState($state);
            $outing->setOrganizerUser($this->getUser());
            $outing->addRegisteredUsers($this->getUser());

            $manager->persist($outing);
            $manager->flush();

            return $this->redirectToRoute('app_outing_edit', ['id' => $outing->getId()]);
        }

        return $this->render('outing/create.html.twig', [
            'form' => $form->createView(),
            'cities' => $cities,
            'places' => $places,
            'error' => null
        ]);
    }

    /**
     * @Route("/outing/edit/{id}", name="app_outing_edit")
     * @param Outing $outing
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Outing $outing, Request $request, EntityManagerInterface $manager)
    {
        if ($this->getUser() !== $outing->getOrganizerUser()) {
            return $this->redirectToRoute('app_home');
        }

        $campus = $manager->getRepository(Campus::class)->findAll();
        $places = $manager->getRepository(Place::class)->findAll();
        $cities = $manager->getRepository(City::class)->findAll();

        $formOption = ['campus' => [], 'places' => [], 'cities' => []];
        foreach ($campus as $camp) {
            $formOption['campus'][$camp->getName()] = $camp;
        }

        foreach ($places as $place) {
            $formOption['places'][$place->getName()] = $place;
        }

        foreach ($cities as $city) {
            $formOption['cities'][$city->getName()] = $city;
        }

        $form = $this->createForm(OutingType::class, $outing, $formOption);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($outing);
            $manager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('outing/edit.html.twig', [
            'form' => $form->createView(),
            'outing' => $outing,
            'cities' => $cities,
            'places' => $places,
            'error' => null
        ]);
    }

    /**
     * @Route("/outing/pub/{id}", name="app_outing_pub")
     * @param Outing $outing
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function pub(Outing $outing, EntityManagerInterface $manager)
    {
        if($outing->getOrganizerUser() == $this->getUser() && $outing->getState()->getLabel() == 'Créee'){
            $state = $manager->getRepository(State::class)->findOneBy(array('label' => "Ouverte"));

            $outing->setState($state);

            $manager->persist($outing);
            $manager->flush();
        }

        return $this->redirectToRoute('app_outing_edit', ['id' => $outing->getId()]);
    }

    /**
     * @Route("/outing/show/{id}", name="app_outing_show")
     * @param Outing $outing
     * @return Response
     */
    public function show(Outing $outing)
    {
        if($outing->getOrganizerUser() == $this->getUser()){
            return $this->redirectToRoute('app_outing_edit', ['id' => $outing->getId()]);
        }

        return $this->render('outing/show.html.twig', [
            'outing' => $outing
        ]);
    }

    /**
     * @Route("/outing/abort/{id}", name="app_outing_abort")
     * @param Outing $outing
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function abort(Outing $outing, Request $request, EntityManagerInterface $manager)
    {
        if($outing->getOrganizerUser() == $this->getUser() && ($outing->getState()->getLabel() == 'Créee' || $outing->getState()->getLabel() == 'Ouverte' || $outing->getState()->getLabel() == 'Clôturée')){

            $form = $this->createForm(AbortType::class, null);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $state = $manager->getRepository(State::class)->findOneBy(array('label' => "Annulée"));
                $outing->setState($state);
                $outing->setDescription($outing->getDescription()."\nMotif d'annulation : ".$form->get('abort_desc')->getData());

                $manager->persist($outing);
                $manager->flush();

                return $this->redirectToRoute('app_home');
            }

            return $this->render('outing/abort.html.twig', [
                'form' => $form->createView(),
                'outing' => $outing
            ]);
        }

        return $this->redirectToRoute('app_outing_edit', ['id' => $outing->getId()]);
    }

    /**
     * @Route("/outing/register/{id}", name="app_outing_register")
     * @param Outing $outing
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function register(Outing $outing, EntityManagerInterface $manager)
    {
        if($outing->getOrganizerUser() !== $this->getUser()){
            if(array_search($this->getUser(), $outing->getRegisteredUsers()->getValues()) !== false && ($outing->getState()->getLabel() == 'Ouverte' || $outing->getState()->getLabel() == 'Clôturée')){
                $outing->removeRegisteredUsers($this->getUser());
            } else if(sizeof($outing->getRegisteredUsers()->getValues()) < $outing->getMaxRegister() && date('now') < $outing->getEndRegisterDate()) {
                $outing->addRegisteredUsers($this->getUser());
            }

            $manager->persist($outing);
            $manager->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
