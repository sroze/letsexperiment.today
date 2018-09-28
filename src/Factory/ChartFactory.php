<?php


namespace App\Factory;


use App\Entity\ExpectedOutcome;

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
        foreach($expectedOutcome->checkedOutcomes->toArray() as $checkedOutcome) {
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

        usort(
            $points,
            function(array $point1, array $point2): int
            {
                return strcmp($point1[0], $point2[0]);
            }
        );

        return [
            'name' => $expectedOutcome->name,
            'points' => $points
        ];
    }
}
