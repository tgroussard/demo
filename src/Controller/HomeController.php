<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Outing;
use App\Entity\State;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function index(Request $request, EntityManagerInterface $manager)
    {
        // Gestion du searchType campus
        //-------------------------------
        $formOption = ['campus' => []];
        $campus = $manager->getRepository(Campus::class)->findAll();
        foreach ($campus as $camp) {
            $formOption['campus'][$camp->getName()] = $camp->getId();
        }
        $formOption['defaultCampus'] = $this->getUser()->getCampus()->getName();
        //-------------------------------

        $form = $this->createForm(SearchType::class, null, $formOption);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCampus = $form->get('campus')->getData();
            $outings = $manager->getRepository(Outing::class)->findByCampus($selectedCampus);

            $selectedPast = $form->get('checkPast')->getData();
            if ($selectedPast) {
                $outings = array_filter($outings, function (Outing $o) {
                    return date('now') >= $o->getStartDate();
                });
            } else {
                $outings = array_filter($outings, function (Outing $o) {
                    return date('now') <= $o->getStartDate();
                });
            }

            $selectedName = $form->get('name')->getData();
            if ($selectedName) {
                $outings = array_filter($outings, function (Outing $o) use ($selectedName) {
                    return strpos(strtoupper($o->getName()), strtoupper($selectedName)) !== false;
                });
            }

            $selectedStartDate = $form->get('dateStart')->getData();
            if ($selectedStartDate) {
                $outings = array_filter($outings, function (Outing $o) use ($selectedStartDate) {
                    return $selectedStartDate <= $o->getStartDate();
                });
            }

            $selectedEndDate = $form->get('dateEnd')->getData();
            if ($selectedEndDate) {
                $outings = array_filter($outings, function (Outing $o) use ($selectedEndDate) {
                    return $selectedEndDate >= $o->getStartDate();
                });
            }

            $selectedOrganizer = $form->get('checkOrganizer')->getData();
            if (!$selectedOrganizer) {
                $outings = array_filter($outings, function (Outing $o) {
                    return $o->getOrganizerUser() != $this->getUser();
                });
            }

            $selectedRegister = $form->get('checkRegister')->getData();
            if (!$selectedRegister) {
                $outings = array_filter($outings, function (Outing $o) {
                    return !in_array($this->getUser(), $o->getRegisteredUsers()->getValues());
                });
            }

            $selectedNotRegister = $form->get('checkNotRegister')->getData();
            if (!$selectedNotRegister) {
                $outings = array_filter($outings, function (Outing $o) {
                    return in_array($this->getUser(), $o->getRegisteredUsers()->getValues());
                });
            }
        } else {
            $outings = $manager->getRepository(Outing::class)->findByCampus($this->getUser()->getCampus());
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'outings' => $outings,
            'campus' => $campus,
            'error' => null
        ]);
    }
}
