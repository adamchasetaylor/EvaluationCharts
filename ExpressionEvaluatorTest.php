<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;

require_once 'ExpressionEvaluator.php';

class ExpressionEvaluatorTest extends TestCase
{
    public function testSimpleAddition()
    {
        $evaluator = new ExpressionEvaluator();
        $steps = $evaluator->evaluateExpression("3 + 2 - 1");
        $result = end($steps)[0];
        $this->assertEquals("4", $result);
    }

    public function testParenthesesAndArithmetic()
    {
        $evaluator = new ExpressionEvaluator();
        $steps = $evaluator->evaluateExpression("(3 + 2) * 2 / 5");
        $result = end($steps)[0];
        $this->assertEquals("2", $result);
    }

    public function testVariableAssignment()
    {
        $evaluator = new ExpressionEvaluator();
        $evaluator->evaluateExpression("x = (3.0 + 5.0 + 7.0) / 2.0");
        $this->assertEquals("7.5", $evaluator->getVariable('x'));
    }

    public function testVariableAssignmentWithArithmetic()
    {
        $evaluator = new ExpressionEvaluator();
        $evaluator->evaluateExpression("x = 7 / 4 * 3.2");
        $this->assertEquals("5.6", $evaluator->getVariable('x'));
    }

    public function testVariableMultiplication()
    {
        $evaluator = new ExpressionEvaluator(["x" => 5.6]); 
        $evaluator->evaluateExpression("y = x * 3");
        $this->assertEquals("16.8", $evaluator->getVariable('y'));
    }

    public function testVariableIncrement()
    {
        $evaluator = new ExpressionEvaluator(["cats" => 12]);
        $evaluator->evaluateExpression("cats = cats + 1");
        $this->assertEquals("13", $evaluator->getVariable('cats'));
    }

    public function testAverageCalculation()
    {
        $variables = [
            "score1" => 75,
            "score2" => 100,
            "score3" => 88,
            "score4" => 95,
        ];
        $evaluator = new ExpressionEvaluator($variables);
        $evaluator->evaluateExpression("average = (score1 + score2 + score3 + score4)/4");
        $this->assertEquals("89.5", $evaluator->getVariable('average'));
    }

    public function testBadAverageCalculation()
    {
        $variables = [
            "score1" => 75,
            "score2" => 100,
            "score3" => 88,
            "score4" => 95,
        ];
        $evaluator = new ExpressionEvaluator($variables);
        $evaluator->evaluateExpression("bad_average = score1 + score2 + score3 + score4 / 4");
        $this->assertEquals("286.75", $evaluator->getVariable('bad_average'));
    }

    public function testSimpleVariableAssignment()
    {
        $evaluator = new ExpressionEvaluator();
        $evaluator->evaluateExpression("x = 100");
        $this->assertEquals("100", $evaluator->getVariable('x'));
    }

    public function testAardvarksCalculation()
    {
        $variables = [
            "cornflakes" => 75,
            "x" => 100,
            "r2d2" => 88,
            "c3po" => 95,
        ];
        $evaluator = new ExpressionEvaluator($variables);
        $evaluator->evaluateExpression("aardvarks = (cornflakes + x + r2d2 + c3po) / 4");
        $this->assertEquals("89.5", $evaluator->getVariable('aardvarks'));
    }

    public function testAreaCalculation()
    {
        $evaluator = new ExpressionEvaluator(["radius" => 23.2]);
        $evaluator->evaluateExpression("area = 3.14 * radius^2");
        $this->assertEquals("1690.0736", $evaluator->getVariable('area')); 
    }

    public function testLessThanOrEqual()
    {
        $evaluator = new ExpressionEvaluator();
        $steps = $evaluator->evaluateExpression("100 <= 65.0");
        $result = end($steps)[0];
        $this->assertEquals("false", $result);
    }

    public function testGreaterThanOrEqual()
    {
        $evaluator = new ExpressionEvaluator();
        $steps = $evaluator->evaluateExpression("100 >= 65.0");
        $result = end($steps)[0];
        $this->assertEquals("true", $result);
    }
}

?>