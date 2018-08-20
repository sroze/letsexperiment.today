<?php

namespace App\Command;

use App\Emails\RenderAndSendEmails;
use App\Entity\Experiment;
use App\Entity\SentEmailRecord;
use App\Repository\ExperimentRepository;
use App\Repository\SentEmailRecordRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use When\When;

class SendEmailsCommand extends Command
{
    private $experimentRepository;
    private $sentEmailRecordRepository;
    private $renderAndSendEmails;
    private $urlGenerator;

    public function __construct(
        ExperimentRepository $experimentRepository,
        SentEmailRecordRepository $sentEmailRecordRepository,
        RenderAndSendEmails $renderAndSendEmails,
        UrlGeneratorInterface $urlGenerator
    )
    {
        parent::__construct();

        $this->experimentRepository = $experimentRepository;
        $this->sentEmailRecordRepository = $sentEmailRecordRepository;
        $this->renderAndSendEmails = $renderAndSendEmails;
        $this->urlGenerator = $urlGenerator;
    }


    protected function configure()
    {
        $this->setName('send-emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $twoDaysAgo = \DateTime::createFromFormat('U', strtotime('-2 days'));

        $experimentForWhichWeNeedToSendCheckInReminders = [];

        foreach ($this->experimentRepository->findAll() as $experiment) {
            if (!$experiment->isStarted()) {
                $shouldHaveSentStartReminder = $experiment->createdAt === null || $experiment->createdAt < $twoDaysAgo;

                if ($shouldHaveSentStartReminder &&
                    !$this->sentEmailRecordRepository->hasSent($experiment, $experiment->createdAt ?: $twoDaysAgo, 'experiment-not-started')) {
                    $this->sendExperimentNotStarted($experiment, $output);
                }

                continue;
            }

            if ($experiment->period->end < $now) {
                if (!$this->sentEmailRecordRepository->hasSent($experiment, $experiment->period->start, 'experiment-ended')) {
                    $this->sendEndedExperiment($experiment, $output);
                }

                continue;
            }

            $nextReminder = (new When())
                ->startDate($experiment->period->start)
                ->freq('weekly')
                ->getPrevOccurrence($now);

            // If the reminder is before or equal the start date, we don't send it,
            // it's the first one.
            if ($nextReminder <= $experiment->period->start) {
                continue;
            }

            // Remove 2 hours so that it can be "prepared"
            // (if users have weekly ceremonies, it's quite convinient)
            $nextReminder = $nextReminder->sub(new \DateInterval('PT2H'));

            if ($this->sentEmailRecordRepository->hasSent($experiment, $nextReminder, 'check-in-reminder')) {
                continue;
            }

            $experimentForWhichWeNeedToSendCheckInReminders[] = $experiment;
        }

        // Group reminders per collaborator
        $experimentsToRemindPerCollaborator = [];

        foreach ($experimentForWhichWeNeedToSendCheckInReminders as $experiment) {
            foreach ($experiment->collaborators as $collaborator) {
                if (!array_key_exists($collaborator->email, $experimentsToRemindPerCollaborator)) {
                    $experimentsToRemindPerCollaborator[$collaborator->email] = [];
                }

                $experimentsToRemindPerCollaborator[$collaborator->email][] = $experiment;
            }
        }

        // Send grouped emails
        foreach ($experimentsToRemindPerCollaborator as $collaboratorEmail => $experiments) {
            $this->sendCheckInReminder($collaboratorEmail, $experiments, $output);
        }
    }

    private function sendEndedExperiment(Experiment $experiment, OutputInterface $output)
    {
        foreach ($experiment->collaborators as $collaborator) {
            $output->writeln('Sent ended reminder for experiment #'.$experiment->uuid.' to '.$collaborator->email);
            $this->renderAndSendEmails->renderAndSend(
                $collaborator->email,
                sprintf('Experiment "%s" has ended', $experiment->name),
                'experiment/emails/has_ended.html.twig',
                [
                    'experiment' => $experiment,
                ]
            );

            $this->sentEmailRecordRepository->record(new SentEmailRecord($experiment, 'experiment-ended', $collaborator->email));
        }
    }

    private function sendCheckInReminder($collaboratorEmail, $experiments, OutputInterface $output)
    {
        $subject = 'Check-in reminder for ';
        $subject .= current($experiments)->name;

        $numberOfExperiments = count($experiments);
        if ($numberOfExperiments > 1) {
            $subject .= ' and '.($numberOfExperiments - 1).' other(s)';
        }

        $output->writeln('Sent check-in reminder for '.$numberOfExperiments.' experiments to '.$collaboratorEmail);
        $this->renderAndSendEmails->renderAndSend(
            $collaboratorEmail,
            $subject,
            'experiment/emails/check_in_reminder.html.twig',
            [
                'experiments' => $experiments,
            ]
        );

        foreach ($experiments as $experiment) {
            $this->sentEmailRecordRepository->record(new SentEmailRecord($experiment, 'check-in-reminder', $collaboratorEmail));
        }
    }

    private function sendExperimentNotStarted(Experiment $experiment, OutputInterface $output)
    {
        foreach ($experiment->collaborators as $collaborator) {
            $output->writeln('Sent not started reminder for experiment #'.$experiment->uuid.' to '.$collaborator->email);
            $this->renderAndSendEmails->renderAndSend(
                $collaborator->email,
                sprintf('Experiment "%s" is not started yet', $experiment->name),
                'experiment/emails/not_started.html.twig',
                [
                    'experiment' => $experiment,
                ]
            );

            $this->sentEmailRecordRepository->record(new SentEmailRecord($experiment, 'experiment-not-started', $collaborator->email));
        }
    }
}
