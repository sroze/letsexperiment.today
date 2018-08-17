<?php

namespace App\Controller;

use App\Entity\CheckedOutcome;
use App\Entity\CheckIn;
use App\Entity\Collaborator;
use App\Entity\ExpectedOutcome;
use App\Entity\Experiment;
use App\Entity\ExperimentPeriod;
use App\Events\AddedCollaborator;
use App\Events\Events;
use App\SeamlessSecurity\Bridge\CollaboratorAsUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/e/{id}")
 */
class ExperimentController extends Controller
{
    private $entityManager;
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("", name="experiment")
     * @Template
     */
    public function view(Experiment $experiment, UserInterface $user)
    {
        // If the 1st to arrive of the experiment... user is collaborator.
        if (0 === count($experiment->collaborators)) {
            if (!$user instanceof CollaboratorAsUser) {
                throw new \InvalidArgumentException('User needs to be a collaborator');
            }

            $experiment->collaborators[] = $user->getCollaborator();

            $this->entityManager->persist($experiment);
            $this->entityManager->flush();
        }

        return [
            'experiment' => $experiment,
        ];
    }

    /**
     * @Route("/add-outcome", name="experiment_add_outcome", methods={"POST"})
     */
    public function addOutcome(Experiment $experiment, Request $request)
    {
        $expectedOutcome = new ExpectedOutcome();
        $expectedOutcome->experiment = $experiment;
        $expectedOutcome->name = $request->get('name');
        $expectedOutcome->currentValue = $request->get('currentValue');
        $expectedOutcome->expectedValue = $request->get('expectedValue');

        $this->entityManager->persist($expectedOutcome);
        $this->entityManager->flush();

        return $this->redirectToExperiment($experiment);
    }

    /**
     * @Route("/start", name="experiment_start", methods={"POST"})
     */
    public function start(Experiment $experiment, Request $request)
    {
        if (empty($duration = $request->request->get('duration'))) {
            throw new BadRequestHttpException('Duration is not valid');
        }

        $period = new ExperimentPeriod();
        $period->start = new \DateTime();
        $period->end = \DateTime::createFromFormat('U', strtotime($duration));

        $experiment->period = $period;

        $this->entityManager->persist($experiment);
        $this->entityManager->flush();

        return $this->redirectToExperiment($experiment);
    }

    /**
     * @Route("/check-in/new", name="experiment_check_in")
     * @Template
     */
    public function checkIn(Experiment $experiment, Request $request, UserInterface $user)
    {
        if ($request->isMethod('post')) {
            $outcomeValues = $request->request->get('outcome');

            if (empty($outcomeValues) || !is_array($outcomeValues)) {
                throw new BadRequestHttpException('Outcomes are not valid');
            }

            $checkIn = new CheckIn();
            $checkIn->experiment = $experiment;
            $checkIn->date = new \DateTime();
            $checkIn->collaborator = $user->getCollaborator();

            foreach ($outcomeValues as $outcomeUuid => $value) {
                $checkedOutcome = new CheckedOutcome();
                $checkedOutcome->checkIn = $checkIn;
                $checkedOutcome->expectedOutcome = $this->entityManager->find(ExpectedOutcome::class, $outcomeUuid);
                $checkedOutcome->currentValue = $value;

                $this->entityManager->persist($checkedOutcome);
            }

            $this->entityManager->persist($checkIn);
            $this->entityManager->flush();

            return $this->redirectToExperiment($experiment);
        }

        return [
            'experiment' => $experiment,
        ];
    }

    /**
     * @Route("/collaborators/add", name="experiment_add_collaborator")
     */
    public function addCollaborator(Experiment $experiment, Request $request)
    {
        $email = $request->request->get('email');
        if (empty($email)) {
            throw new BadRequestHttpException('Collaborator\'s email is not valid');
        }

        $collaborator = $this->entityManager->getRepository(Collaborator::class)->findOneBy([
            'email' => $email,
        ]);

        if (null === $collaborator) {
            $collaborator = new Collaborator();
            $collaborator->email = $email;

            $this->entityManager->persist($collaborator);
        }

        $experiment->collaborators[] = $collaborator;

        $this->eventDispatcher->dispatch(Events::ADD_COLLABORATOR, new AddedCollaborator($experiment, $collaborator));

        $this->entityManager->persist($experiment);
        $this->entityManager->flush();

        return $this->redirectToExperiment($experiment);
    }

    /**
     * @Route("/collaborators/remove", name="experiment_remove_collaborator")
     */
    public function removeCollaborator(Experiment $experiment, Request $request)
    {
        $email = $request->request->get('email');
        if (empty($email)) {
            throw new BadRequestHttpException('Collaborator\'s email is not valid');
        }

        $experiment->collaborators = $experiment->collaborators->filter(function(Collaborator $collaborator) use ($email) {
            return $collaborator->email !== $email;
        });

        $this->entityManager->persist($experiment);
        $this->entityManager->flush();

        return $this->redirectToExperiment($experiment);
    }

    private function redirectToExperiment(Experiment $experiment): RedirectResponse
    {
        return $this->redirectToRoute('experiment', [
            'id' => $experiment->uuid,
        ]);
    }
}
