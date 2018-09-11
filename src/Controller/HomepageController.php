<?php

namespace App\Controller;

use App\Entity\Experiment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\User\UserInterface;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template
     */
    public function index(UserInterface $user = null)
    {
        /** @var ArrayCollection|Experiment $experiments */
        $experiments = $user !== null ? $user->getCollaborator()->experiments : [];

        return [
            'endedExperiments' => $experiments->filter(function(Experiment $experiment) {
                return $experiment->hasEnded();
            }),
            'ongoingExperiments' => $experiments->filter(function(Experiment $experiment) {
                return $experiment->isStarted() && !$experiment->hasEnded();
            }),
            'pendingExperiments' => $experiments->filter(function(Experiment $experiment) {
                return !$experiment->isStarted();
            })
        ];
    }

    /**
     * @Route("/create-experiment", name="create_experiment", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $experiment = new Experiment();
        $experiment->name = $request->request->get('name');
        $experiment->createdAt = new \DateTime();

        if (empty($experiment->name)) {
            throw new BadRequestHttpException('Name of the experiment should not be blank');
        }

        $entityManager->persist($experiment);
        $entityManager->flush();

        return $this->redirectToRoute('experiment', [
            'id' => $experiment->uuid,
        ]);
    }
}
