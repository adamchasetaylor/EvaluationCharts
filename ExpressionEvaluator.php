<?php

require_once 'ExpressionChartRenderer.php';

class ExpressionEvaluator {
    private $variables = [];
    private $chartRenderer;

    public function __construct($variables = []) {
        $this->variables = $variables;
        $this->chartRenderer = new ExpressionChartRenderer();
    }

    public function evaluateExpression($expression) {
        $steps = [];

        // Check if there's a variable assignment
        if (strpos($expression, '=') !== false && !preg_match('/[<>!]=/', $expression)) {
            list($var, $expr) = array_map('trim', explode('=', $expression, 2));
            $expr = $this->replaceVariables($expr);
            $steps[] = [$expression, null];
        } else {
            $expr = $this->replaceVariables($expression);
            $steps[] = [$expr, null];
        }

        // Handle parentheses first
        while (strpos($expr, '(') !== false) {
            $start = strrpos($expr, '(');
            $end = strpos($expr, ')', $start);
            $inner = substr($expr, $start + 1, $end - $start - 1);
            $result = $this->evaluateSimpleExpression($inner);
            $steps[] = [$expr, $inner];
            $expr = substr_replace($expr, $result, $start, $end - $start + 1);
        }

        // Evaluate the final expression
        $finalSteps = $this->evaluateSimpleExpression($expr, true);
        $steps = array_merge($steps, $finalSteps);

        // Store the result if it's an assignment
        if (isset($var)) {
            $result = end($finalSteps)[0];
            $this->variables[$var] = $result;
            $steps[] = [$var . ' = ' . $result, null];
        }

        return $steps;
    }

    private function evaluateSimpleExpression($expression, $returnSteps = false)
    {
        $steps = [];
        $operations = [
            ['^'],
            ['*', '/'],
            ['+', '-']
        ];
    
        // Compile regular expressions for operators
        $compiledRegexes = [];
        foreach ($operations as $operators) {
            $compiledRegexes[] = '/(-?\d+(?:\.\d+)?)\s*([' . preg_quote(implode('', $operators), '/') . '])\s*(-?\d+(?:\.\d+)?)/';
        }
    
        foreach ($compiledRegexes as $regex) {
            while (preg_match($regex, $expression, $matches)) {
                $left = floatval($matches[1]);
                $op = $matches[2];
                $right = floatval($matches[3]);
                $subExpr = $matches[0];
    
                $result = $this->performOperation($left, $op, $right);
    
                if ($returnSteps) {
                    $steps[] = [$expression, $subExpr];
                }
                $expression = preg_replace('/' . preg_quote($subExpr, '/') . '/', $result, $expression, 1);
            }
        }
    
        // Use evaluateComparison for comparison operators
        if (preg_match('/(-?\d+(?:\.\d+)?)\s*(>=|<=|>|<|==|!=)\s*(-?\d+(?:\.\d+)?)/', $expression, $matches)) {
            $comparisonSteps = $this->evaluateComparison($expression, $returnSteps);
            if ($returnSteps) {
                $steps = array_merge($steps, $comparisonSteps);
                $expression = end($comparisonSteps)[0];
            } else {
                $expression = $comparisonSteps;
            }
        }
    
        if ($returnSteps) {
            $steps[] = [$expression, null]; // Add the final step
            return $steps;
        } else {
            return $expression;
        }
    }

    public function evaluateComparison($expression, $returnSteps = false) {
        $steps = [];
        $pattern = '/(-?\d+(?:\.\d+)?)\s*(>=|<=|>|<|==|!=)\s*(-?\d+(?:\.\d+)?)/';

        if (preg_match($pattern, $expression, $matches)) {
            $left = floatval($matches[1]);
            $op = $matches[2];
            $right = floatval($matches[3]);
            $result = $this->performOperation($left, $op, $right);
            if ($returnSteps) {
                $steps[] = [$expression, $matches[0]];
                $steps[] = [$result, null];
            }
            $expression = $result;
        }

        if ($returnSteps) {
            return $steps;
        } else {
            return $expression;
        }
    }

    private function performOperation($left, $op, $right) {
        switch ($op) {
            case '*': return $left * $right;
            case '/': 
                if ($right == 0) {
                    throw new Exception("Division by zero");
                }
                return $left / $right;
            case '+': return $left + $right;
            case '-': return $left - $right;
            case '^': return pow($left, $right);
            case '>=': return $left >= $right ? 'true' : 'false';
            case '<=': return $left <= $right ? 'true' : 'false';
            case '>': return $left > $right ? 'true' : 'false';
            case '<': return $left < $right ? 'true' : 'false';
            case '==': return $left == $right ? 'true' : 'false';
            case '!=': return $left != $right ? 'true' : 'false';
            default: throw new Exception("Unknown operator: $op");
        }
    }

    private function replaceVariables($expression) {
        foreach ($this->variables as $var => $value) {
            $expression = preg_replace('/\b' . preg_quote($var, '/') . '\b/', $value, $expression);
        }
        return $expression;
    }

    public function createFixedWidthChart($expression) {
        $steps = $this->evaluateExpression($expression);
        return $this->chartRenderer->renderChart($expression, $steps);
    }

    public function getVariable($var) {
        return isset($this->variables[$var]) ? $this->variables[$var] : null;
    }
}

?>