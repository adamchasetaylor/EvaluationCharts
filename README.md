# Expression Evaluation and Chart Generation

This code provides a tool for evaluating arithmetic and conditional expressions, generating step-by-step evaluation charts, and displaying them in a framed format.

## Key Features

* **Expression Evaluation:** Evaluates a wide range of arithmetic and conditional expressions, including those with parentheses, variables, and various operators.
* **Chart Generation:** Creates detailed evaluation charts that show the order of operations and intermediate results.
* **Framed Output:** Presents the evaluation charts in a visually appealing framed format for clarity.
* **Error Handling:** Includes robust error handling to catch and report potential issues during expression evaluation.

## Usage

The primary function is `createFramedCharts`, which takes two arguments:

* `$expressions`: An array of expressions to evaluate.
* `$variables`: An array of variables with their initial values.

The function returns a string containing the formatted evaluation charts for each expression.

**Example:**

```php
require_once 'ExpressionEvaluator.php';

// Define initial variables
$variables = [
    "cats" => 12,
    // ... other variables
];

// Define expressions to evaluate
$expressions = [
    "3 + 2 - 1",
    "(3 + 2) * 2 / 5",
    "cats - 5 + (6 / 2)",
    // ... other expressions
];

// Generate and display the framed charts
echo "\n" . createFramedCharts($expressions, $variables);
```

## Example Output

For the expression `(3 + 2) * 2 / 5`, the output would be:

```
|----------------------------------------------------------------------------------------|
|The evaluation chart for (3 + 2) * 2 / 5 is                                             |
|                                                                                        |
|                                    (3 + 2) * 2 / 5                                     |
|                                     |___|                                              |
|                                       |                                                |
|                                                                                        |
|                                       5 * 2 / 5                                        |
|                                       |___|                                            |
|                                         |                                              |
|                                                                                        |
|                                         10 / 5                                         |
|                                         |____|                                         |
|                                            |                                           |
|                                                                                        |
|                                           2                                            |
|----------------------------------------------------------------------------------------|
```

## Additional Notes

* `ExpressionEvaluator` class performs the expression evaluation and chart generation.
* `ExpressionChartRenderer` class is responsible for formatting the charts and adding the frames.
* The code includes a set of unit tests (`ExpressionEvaluatorTest.php`) to ensure the accuracy of the expression evaluation logic.

## Potential Enhancements

* **More Operators:** Add support for additional operators, such as modulo or bitwise operators.
* **Chart Customization:** Allow for customization of the chart format, such as adjusting the width or spacing.
* **User Interface:** Develop a user interface to make it easier to input expressions and variables.

## License

This code is provided under an MIT license. See the LICENSE file for more information.