<?php

namespace App\Controller;

use App\Entity\Week;
use App\Form\CreateWeekFromType;
use App\Repository\WeekRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/week', name: 'week.')]
class WeekController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WeekRepository $weekRepository,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('week/index.html.twig', [
            'week' => $this->weekRepository->findLatest(),
            'weeklyTarget' => Week::WEEKLY_TARGET_HOURS,
            'dailyDefaults' => Week::DEFAULT_WORK_HOURS,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $latestWeek = $this->weekRepository->findLatest()->getCalenderWeek();
        $currentWeek = (int) date('W');

        if ($latestWeek === $currentWeek) {
            $this->addFlash(
                'danger',
                'Woche existiert bereits.'
            );

            return $this->redirectToRoute('week.index');
        }

        $week = new Week();
        $week->setCalenderWeek((int) date('W'));
        $week->setYear((int) date('Y'));

        $form = $this->createForm(CreateWeekFromType::class, $week);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $entityManager->persist($week);
            $entityManager->flush();

            return $this->redirectToRoute('week.index');
        }

        return $this->render('week/add.html.twig', [
            'form' => $form->createView(),
            'dailyDefaults' => Week::DEFAULT_WORK_HOURS,
        ]);
    }

    #[Route('/edit', name: 'edit')]
    public function edit(Request $request): Response
    {
        $latestWeek = $this->weekRepository->findLatest();
        $form = $this->createForm(CreateWeekFromType::class, $latestWeek);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $entityManager->flush();

            return $this->redirectToRoute('week.index');
        }

        return $this->render('week/edit.html.twig', [
            'form' => $form->createView(),
            'dailyDefaults' => Week::DEFAULT_WORK_HOURS,
        ]);
    }
}
