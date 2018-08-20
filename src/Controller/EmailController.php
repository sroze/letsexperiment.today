<?php

namespace App\Controller;

use App\Entity\Experiment;
use App\Repository\ExperimentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    private $experimentRepository;

    public function __construct(ExperimentRepository $experimentRepository)
    {
        $this->experimentRepository = $experimentRepository;
    }

    /**
     * @Route("/emails/has-started")
     */
    public function emailStart(Request $request)
    {
        return $this->render('experiment/emails/has_started.html.twig', [
            'experiment' => $this->experimentRepository->find(
                $request->get('experiment')
            ),
        ]);
    }

    /**
     * @Route("/emails/has-ended")
     */
    public function emailEnd(Request $request)
    {
        return $this->render('experiment/emails/has_ended.html.twig', [
            'experiment' => $this->experimentRepository->find(
                $request->get('experiment')
            ),
        ]);
    }

    /**
     * @Route("/emails/check-in-reminder")
     */
    public function emailCheckInReminder(Request $request)
    {
        return $this->render('experiment/emails/check_in_reminder.html.twig', [
            'experiments' => array_map(function(string $uuid) {
                return $this->experimentRepository->find($uuid);
            }, $request->get('experiment')),
        ]);
    }
}
