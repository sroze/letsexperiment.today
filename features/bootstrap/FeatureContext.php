<?php

use Behat\Behat\Context\Context;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo 
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 * 
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context
{
    private $application;
    private $experimentRepository;
    private $mailer;

    public function __construct(
        KernelInterface $kernel,
        \App\Tests\Emails\InMemoryMailer $mailer,
        \App\Tests\Repository\InMemoryExperimentRepository $experimentRepository,
        \App\Tests\Repository\InMemorySentEmailRecordRepository $sentEmailRecordRepository,
        \App\Emails\RenderAndSendEmails $renderAndSendEmails,
        \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
    )
    {
        $this->experimentRepository = $experimentRepository;
        $this->mailer = $mailer;
        $this->application = new Application();
        $this->application->add(new \App\Command\SendEmailsCommand(
            $experimentRepository,
            $sentEmailRecordRepository,
            $renderAndSendEmails,
            $urlGenerator
        ));
    }

    /**
     * @Given the experiment :uuid has been started :startDate
     */
    public function theExperimentHasBeenStarted($uuid, $startDate)
    {
        $start = DateTime::createFromFormat('U', strtotime($startDate));
        $end = (clone $start)->add(new \DateInterval('P30D'));

        $experiment = new \App\Entity\Experiment();
        $experiment->uuid = $uuid;
        $experiment->period = new \App\Entity\ExperimentPeriod($start, $end);

        $this->experimentRepository->save($experiment);
    }

    /**
     * @Given the experiment :uuid has the collaborator :email
     */
    public function theExperimentHasTheCollaborator($uuid, $email)
    {
        $this->experimentRepository->find($uuid)->collaborators->add(new \App\Entity\Collaborator($email));
    }

    /**
     * @When I run the command :name
     */
    public function iRunTheCommand($name)
    {
        $command = $this->application->find($name);

        $tester = new \Symfony\Component\Console\Tester\CommandTester($command);
        $tester->execute(array('command' => $command->getName()));
    }

    /**
     * @Then a check-in reminder should have been sent to :email
     */
    public function aCheckInReminderShouldHaveBeenSentTo($email)
    {
        foreach ($this->mailer->getSent() as $sent) {
            if (in_array($email, array_keys($sent->getTo()))) {
                return;
            }
        }


        throw new \RuntimeException(sprintf('No email sent to %s sent.', $email));
    }

    /**
     * @Then a check-in reminder should NOT have been sent to :email
     */
    public function aCheckInReminderShouldNotHaveBeenSentTo($email)
    {
        foreach ($this->mailer->getSent() as $sent) {
            if (in_array($email, array_keys($sent->getTo()))) {
                throw new \RuntimeException(sprintf('Email sent to %s sent.', $email));
            }
        }
    }
}
