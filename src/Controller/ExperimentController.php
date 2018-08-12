<?php

namespace App\Controller;

use App\Entity\Experiment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/e/{id}")
 */
class ExperimentController
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
}
