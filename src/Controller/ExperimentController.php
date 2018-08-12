<?php

namespace App\Controller;

use App\Entity\CheckedOutcome;
use App\Entity\CheckIn;
use App\Entity\ExpectedOutcome;
use App\Entity\Experiment;
use App\Entity\ExperimentPeriod;
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

        return $this->redirectToExperiment($experiment);
    }

    /**
     * @Route("/start", name="experiment_start", methods={"POST"})
     */
    public function start(Experiment $experiment, Request $request, EntityManagerInterface $entityManager)
    {
        if (empty($duration = $request->request->get('duration'))) {
            throw new BadRequestHttpException('Duration is not valid');
        }

        $period = new ExperimentPeriod();
        $period->start = new \DateTime();
        $period->end = \DateTime::createFromFormat('U', strtotime($duration));

        $experiment->period = $period;

        $entityManager->persist($experiment);
        $entityManager->flush();

        return $this->redirectToExperiment($experiment);
    }

    /**
     * @Route("/check-in/new", name="experiment_check_in")
     * @Template
     */
    public function checkIn(Experiment $experiment, Request $request, EntityManagerInterface $entityManager)
    {
        if ($request->isMethod('post')) {
            $outcomeValues = $request->request->get('outcome');

            if (empty($outcomeValues) || !is_array($outcomeValues)) {
                throw new BadRequestHttpException('Outcomes are not valid');
            }

            $checkIn = new CheckIn();
            $checkIn->experiment = $experiment;
            $checkIn->date = new \DateTime();

            foreach ($outcomeValues as $outcomeUuid => $value) {
                $checkedOutcome = new CheckedOutcome();
                $checkedOutcome->checkIn = $checkIn;
                $checkedOutcome->expectedOutcome = $entityManager->find(ExpectedOutcome::class, $outcomeUuid);
                $checkedOutcome->currentValue = $value;

                $entityManager->persist($checkedOutcome);
            }

            $entityManager->persist($checkIn);
            $entityManager->flush();

            return $this->redirectToExperiment($experiment);
        }

        return [
            'experiment' => $experiment,
        ];
    }

    private function redirectToExperiment(Experiment $experiment): RedirectResponse
    {
        return $this->redirectToRoute('experiment', [
            'id' => $experiment->uuid,
        ]);
    }
}
