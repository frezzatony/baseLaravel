<?php

namespace App\Lib\Process\Elements\Gateway;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Exclusive
{

    public static function run($process, $userGroups, $element)
    {
        $expressionLanguage = new ExpressionLanguage();
        $outgoingElementsIds = [];
        foreach ($element['outgoing'] as $idElementOutgoing => $expressionCondiiton) {
            if ($expressionLanguage->evaluate($expressionCondiiton, $process->getVariablesValuesAsObject())) {
                $outgoingElementsIds[] = $idElementOutgoing;
                break;
            }
        }
        $element['outgoing'] = $outgoingElementsIds;
        $element['run_next_step'] = true;
        return $element;
    }
}
