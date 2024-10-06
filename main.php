<?php

// Include the ExpressionEvaluator class
require_once 'ExpressionEvaluator.php';

// Set error logging to stderr
ini_set('error_log', 'stderr'); 

/**
 * Creates framed evaluation charts for a list of expressions.
 *
 * @param array $expressions An array of expressions to evaluate.
 * @param array $variables  An array of variables with their initial values.
 * @return string The formatted evaluation charts.
 */
function createFramedCharts($expressions, $variables = []) {
    // Create an instance of the ExpressionEvaluator
    $evaluator = new ExpressionEvaluator($variables);
    $allCharts = [];

    // Iterate through the expressions
    foreach ($expressions as $expression) {
        try {
            // Generate the evaluation chart
            $chart = $evaluator->createFixedWidthChart($expression);
            $framedChart = [];

            // Add top frame
            $framedChart[] = '|' . str_repeat('-', 88) . '|';

            // Add side frames to each line of the chart
            foreach (explode("\n", $chart) as $line) {
                $framedChart[] = '|' . str_pad($line, 88) . '|';
            }

            // Add bottom frame
            $framedChart[] = '|' . str_repeat('-', 88) . '|';

            // Merge the framed chart with the collection of all charts
            $allCharts = array_merge($allCharts, $framedChart);
            $allCharts[] = ""; // Add an empty line between charts
        } catch (Exception $e) {
            // Handle exceptions during evaluation
            $allCharts[] = "Error evaluating '$expression': " . $e->getMessage();
            $allCharts[] = "";
        }
    }
    return implode("\n", $allCharts);
}

// Example usage:

// Define initial variables
$variables = [
    "cats" => 12,
    "score1" => 75,
    "score2" => 100,
    "score3" => 88,
    "score4" => 95,
    "cornflakes" => 75,
    "x" => 100,
    "r2d2" => 88,
    "c3po" => 95,
    "radius" => 23.2,
    "dogs" => 25
];

// Define expressions to evaluate
$expressions = [
    "3 + 2 - 1",
    "(3 + 2) * 2 / 5",
    "x = (3.0 + 5.0 + 7.0) / 2.0",
    "x = 7 / 4 * 3.2",
    "y = x * 3",
    "cats = cats + 1",
    "average = (score1 + score2 + score3 + score4)/4",
    "bad_average = score1 + score2 + score3 + score4 / 4",
    "x = 100",
    "aardvarks = (cornflakes + x + r2d2 + c3po) / 4",
    "area = 3.14 * radius^2",
    "average >= 65.0",
    "cats = 13",
    "100 < dogs",
    "dogs != cats",
];

// Generate and display the framed charts
echo "\n" . createFramedCharts($expressions, $variables);

?>