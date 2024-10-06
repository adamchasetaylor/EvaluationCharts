<?php

class ExpressionChartRenderer {
    private $chartWidth = 88;

    public function renderChart($expression, $steps) {
        $isAssignment = $this->isAssignment($expression);
        $isSimpleAssignment = $isAssignment && count($steps) <= 3;
        $isComparison = $this->isComparison($expression);

        if ($isSimpleAssignment) {
            $chart = $this->renderSimpleAssignment($expression, $steps);
        } elseif ($isComparison) {
            $chart = $this->renderComparison($expression, $steps);
        } else {
            $chart = ["The evaluation chart for $expression is", ""];
            foreach ($steps as $step) {
                $this->addStepToChart($chart, $step);
            }
        }

        return implode("\n", $chart);
    }

    private function renderSimpleAssignment($expression, $steps) {
        $chart = ["The evaluation chart for $expression is", ""];
        $chart[] = $this->centerText($steps[0][0]);
        return $chart;
    }

    private function renderComparison($expression, $steps) {
        $chart = ["The evaluation chart for $expression is", ""];
        if($expression != $steps[0][0]){
            $chart[] = $this->centerText($expression);
        }
        $chart[] = $this->centerText($steps[0][0]);
        
        if (count($steps) > 2) {
            $this->addUnderlineToChart($chart, $steps[1][0], $steps[1][0]);
        }
        
        $chart[] = $this->centerText($steps[count($steps) - 1][0]);
        return $chart;
    }

    private function addStepToChart(&$chart, $step) {
        if (!is_array($step)) {
            return;
        }

        $fullExpr = isset($step[0]) ? $step[0] : '';
        $partToEvaluate = isset($step[1]) ? $step[1] : null;

        if (end($chart) !== $this->centerText($fullExpr)) {
            $chart[] = $this->centerText($fullExpr);
        }

        if ($partToEvaluate) {
            $this->addUnderlineToChart($chart, $fullExpr, $partToEvaluate);
        }
    }

    private function addUnderlineToChart(&$chart, $fullExpr, $partToEvaluate) {
        $centeredExpr = $this->centerText($fullExpr);
        $underlineStart = strpos($centeredExpr, $partToEvaluate);
        if ($underlineStart !== false) {
            $underline = str_repeat(' ', $underlineStart) . '|' . str_repeat('_', strlen($partToEvaluate) - 2) . '|';
            $chart[] = $underline;

            $this->addOperatorPitchfork($chart, $partToEvaluate, $underlineStart);
            
            $chart[] = "";
        }
    }

    private function addOperatorPitchfork(&$chart, $partToEvaluate, $underlineStart) {
        preg_match('/[\+\-\*\/\^]|>=|<=|==|!=|>|</', $partToEvaluate, $opMatch); 
        if (!empty($opMatch)) {
            $operatorIndex = strpos($partToEvaluate, $opMatch[0]);
            $pitchfork = str_repeat(' ', $underlineStart + $operatorIndex) . '|';
            $chart[] = $pitchfork;
        }
    }

    private function isAssignment($expression) {
        return strpos($expression, '=') !== false && !preg_match('/[<>!]=/', $expression);
    }

    private function isComparison($expression) {
        return preg_match('/>=|<=|==|!=|>|</', $expression);
    }

    private function centerText($text) {
        return str_pad($text, $this->chartWidth, " ", STR_PAD_BOTH);
    }
}

?>