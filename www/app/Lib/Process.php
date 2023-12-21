<?php

namespace App\Lib;

use App\Lib\Process\Elements\Event\Event;
use App\Lib\Process\Elements\Gateway\Gateway;
use App\Lib\Process\Elements\Task\Task;

class Process
{
    private $structure;

    public function __construct(array $structure)
    {
        $this->structure = $structure;
        $this->structure['variables'][] = [
            'id'        =>  'process_status',
            'type'      =>  'varchar',
            'value'     =>  'open',
        ];
    }

    public function getVariablesValuesAsObject($variables = null)
    {
        $returnValues = [];
        foreach ($variables ?? $this->structure['variables'] as $variable) {
            $returnValues[$variable['id']] = $variable['value'] ?? null;
        }
        return $returnValues;
    }

    public function setVariablesValues($values)
    {
        foreach ($values as $idVariable => $value) {
            $keyVariable = array_search($idVariable, array_column($this->structure['variables'], 'id'));
            if ($keyVariable !== false) {
                $this->structure['variables'][$keyVariable]['value'] = $value;
            }
        }
    }

    public function run($userGroups)
    {
        $run = true;
        while ($run) {
            $run = false;
            foreach ($this->getCurrentSteps() as $currentStep) {
                if (in_array($currentStep['element'], ['task']) && in_array($currentStep['type'], ['user'])) {
                    continue;
                }
                if ($this->canExecute($userGroups, $currentStep)) {
                    $currentStep = $this->executeElement($userGroups, $currentStep);
                    $this->removeExecutedStep($currentStep);
                    foreach ($currentStep['outgoing'] as $nextStepKey => $nextStepId) {
                        if (($currentStep['run_next_step'] ?? false)) {
                            foreach ($currentStep['outgoing'] as $idElementOutgoing) {
                                if (empty($idElementOutgoing)) {
                                    continue;
                                }
                                $elementOutgoing = $this->findProperyByAttributes('elements', ['id' => $idElementOutgoing]);
                                $this->addCurrentStep(array_pop($elementOutgoing), true);
                            }
                            $run = true;
                        }
                    }
                }
            }
        }
    }

    public function getCurrentSteps()
    {
        if (empty($this->structure['current_steps'])) {
            $startEvent = $this->findProperyByAttributes('elements', ['id'   =>  'event_start',]);
            $this->addCurrentStep(array_pop($startEvent));
        }
        return $this->structure['current_steps'];
    }

    public static function getStringBpmnFileToArray($fileContent, $return = 'text')
    {
        $process = json_decode(json_encode(simplexml_load_string($fileContent, "SimpleXMLElement", LIBXML_NOCDATA)), true);
        $processArray = [
            'version'   =>  'XXX',
            'variables' =>  [
                [
                    'id'    =>  'exemplo',
                    'type'  =>  'varchar',
                ]
            ],
            'papers'    =>  [
                [
                    'id'            =>  'XXX',
                    'users_groups'  =>  [],
                ]
            ],
            'lanes'     =>  [],
            'elements'  =>  [],
        ];

        $lanesElements = [];
        foreach ($process['process']['laneSet']['lane'] as $lane) {
            $lanesElements = array_merge($lanesElements, array_fill_keys($lane['flowNodeRef'], $lane['@attributes']['id']));
            $processArray['lanes'][] = [
                'id'        =>  $lane['@attributes']['id'],
                'name'      =>  $lane['@attributes']['name'],
                'papers'    =>  [],
            ];
        }

        $flows = [];

        foreach ($process['process']['sequenceFlow'] as $flow) {
            $flows[$flow['@attributes']['id']] = $flow['@attributes']['targetRef'];
        }

        if (isset($process['process']['startEvent']['@attributes'])) {
            $process['process']['startEvent'] = [$process['process']['startEvent']];
        }
        foreach ($process['process']['startEvent'] as $key => $startEvent) {
            $type = 'start';
            if (isset($startEvent['conditionalEventDefinition'])) {
                $type = 'start_conditional';
            }
            $startEvent['outgoing'] = is_array($startEvent['outgoing']) ? $startEvent['outgoing'] : [$startEvent['outgoing']];
            $outgoingFlow = [];
            foreach ($startEvent['outgoing'] as $outgoing) {
                $outgoingFlow[] = $flows[$outgoing];
            }
            $processArray['elements'][] = [
                'id'        =>  'event_start',
                'name'      =>  $startEvent['@attributes']['name'],
                'element'   =>  'event',
                'type'      =>  $type,
                'lane'      =>  $lanesElements[$startEvent['@attributes']['id']],
                'outgoing'  =>  $outgoingFlow,
            ];
        }

        foreach ($process['process'] as $key => $property) {
            if (mb_strtolower(substr($key, -strlen('task'))) == 'task') {
                if (isset($property['@attributes'])) {
                    $property = [$property];
                }
                foreach ($property as $task) {
                    $task['outgoing'] = is_array($task['outgoing']) ? $task['outgoing'] : [$task['outgoing']];
                    $outgoingFlow = [];
                    foreach ($task['outgoing'] as $outgoing) {
                        $outgoingFlow[] = $flows[$outgoing];
                    }
                    $processArray['elements'][] = [
                        'id'        =>  $task['@attributes']['id'],
                        'name'      =>  $task['@attributes']['name'],
                        'element'   =>  'task',
                        'type'      =>  explode('task', mb_strtolower($key))[0],
                        'lane'      =>  $lanesElements[$task['@attributes']['id']],
                        'outgoing'  =>  $outgoingFlow,
                    ];
                }
            }
            if (mb_strtolower(substr($key, -strlen('gateway'))) == 'gateway') {
                if (isset($property['@attributes'])) {
                    $property = [$property];
                }
                foreach ($property as $gateway) {
                    $gateway['outgoing'] = is_array($gateway['outgoing']) ? $gateway['outgoing'] : [$gateway['outgoing']];
                    $gatewayOutgoing = [];
                    foreach ($gateway['outgoing'] as $outgoing) {
                        $gatewayOutgoing[$flows[$outgoing]] = 'definir_ExpressionLanguage';
                    }
                    $processArray['elements'][] = [
                        'id'        =>  $gateway['@attributes']['id'],
                        'name'      =>  $gateway['@attributes']['name'],
                        'element'   =>  'gateway',
                        'type'      =>  explode('gateway', mb_strtolower($key))[0],
                        'lane'      =>  $lanesElements[$gateway['@attributes']['id']],
                        'outgoing'  =>  $gatewayOutgoing,
                    ];
                }
            }
        }

        if (isset($process['process']['endEvent']['@attributes'])) {
            $process['process']['endEvent'] = [$process['process']['endEvent']];
        }
        foreach ($process['process']['endEvent'] as $key => $endEvent) {
            $type = 'end';
            $processArray['elements'][] = [
                'id'        =>  $endEvent['@attributes']['id'],
                'name'      =>  $endEvent['@attributes']['name'],
                'element'   =>  'event',
                'lane'      =>  $lanesElements[$endEvent['@attributes']['id']],
                'type'      =>  $type,
            ];
        }

        $export = var_export($processArray, true);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        return preg_replace(array_keys($patterns), array_values($patterns), $export);
    }

    private function addCurrentStep($element)
    {
        if (!isset($this->structure['current_steps'])) {
            $this->structure['current_steps'] = [];
        }
        $this->structure['current_steps'][] = $element;
        return $this;
    }

    private function removeExecutedStep($element)
    {
        $keyElementRemove = array_search($element['id'], array_column($this->structure['current_steps'], 'id'));
        unset($this->structure['current_steps'][$keyElementRemove]);
        $this->structure['current_steps'] = array_values($this->structure['current_steps']);
        return $this;
    }

    private function executeElement($userGroups, array $element)
    {
        switch ($element['element']) {
            case 'task':
                return Task::run($this, $userGroups, $element);
                break;
            case 'event':
                return Event::run($this, $userGroups, $element);
                break;
            case 'gateway':
                return Gateway::run($this, $userGroups, $element);
                break;
            default:
                $element['run_next_step'] = true;
                return $element;
                break;
        }
    }

    private function canExecute($userGroups, $element)
    {
        $lane = $this->findProperyByAttributes('lanes', ['id' => $element['lane']]);
        if (!empty($lane)) {
            $lane = array_pop($lane);
            foreach ($lane['papers'] as $idPaper) {
                $paper = $this->findProperyByAttributes('papers', ['id' => $idPaper]);
                if (!empty($paper)) {
                    $paper = array_pop($paper);
                    foreach ($paper['users_groups'] as $paperUserGroup) {
                        if (in_array($paperUserGroup, $userGroups)) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function findProperyByAttributes($property, $attributes)
    {
        return array_filter($this->structure[$property], function ($element) use ($attributes) {
            foreach ($attributes as $key => $value) {
                if (!isset($element[$key]) || $element[$key] != $value) {
                    return false;
                }
            }
            return true;
        });
    }
}
