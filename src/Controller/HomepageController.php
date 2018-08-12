<?php

namespace App\Controller;

use App\Entity\Experiment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomepageController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function index()
    {
        return [];
    }

    /**
     * @Route("/create-experiment", name="create_experiment", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $experiment = new Experiment();
        $experiment->name = $request->request->get('name');

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
