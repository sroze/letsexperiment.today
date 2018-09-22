<?php


namespace App\Factory;


use App\Entity\ExpectedOutcome;
use Doctrine\Common\Collections\Criteria;

class ChartFactory
{
    public function createExpectedOutcomeChart(ExpectedOutcome $expectedOutcome): array
    {
        $points = [];

        // First point to chart is the starting value of the outcome
        $points[] = [
            $expectedOutcome->experiment->period->start->format('c'),
            floatval($expectedOutcome->currentValue)
        ];

        // Then add all the checked outcomes
        $checkedOutcomes = $expectedOutcome->checkedOutcomes->matching(
            Criteria::create()->orderBy(array('checkIn' => Criteria::ASC))
        );

        foreach($checkedOutcomes->toArray() as $checkedOutcome) {
            $points[] = [
                $checkedOutcome->checkIn->date->format('c'),
                floatval($checkedOutcome->currentValue)
            ];
        }

        // The last point is the target value
        $points[] = [
            $expectedOutcome->experiment->period->end->format('c'),
            floatval($expectedOutcome->expectedValue)
        ];

        return [
            'name' => $expectedOutcome->name,
            'points' => $points
        ];
    }
}
