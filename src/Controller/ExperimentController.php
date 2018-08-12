<?php

namespace App\Controller;

use App\Entity\ExpectedOutcome;
use App\Entity\Experiment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/e/{id}")
 */
class ExperimentController extends Controller
{
    /**
     * @Route("", name="experiment")
     * @Template
     */
    public function view(Experiment $experiment)
    {
        return [
            'experiment' => $experiment,
        ];
    }

    /**
     * @Route("/add-outcome", name="experiment_add_outcome", methods={"POST"})
     */
    public function addOutcome(Experiment $experiment, Request $request, EntityManagerInterface $entityManager)
    {
        $expectedOutcome = new ExpectedOutcome();
        $expectedOutcome->experiment = $experiment;
        $expectedOutcome->name = $request->get('name');
        $expectedOutcome->currentValue = $request->get('currentValue');
        $expectedOutcome->expectedValue = $request->get('expectedValue');

        $entityManager->persist($expectedOutcome);
        $entityManager->flush();

        return $this->redirectToRoute('experiment', [
            'id' => $experiment->uuid,
        ]);
    }
}
