<?php
namespace jordanbeattie\CraftCmsHealth\console\controllers;

use jordanbeattie\CraftCmsHealth\models\Check;
use jordanbeattie\CraftCmsHealth\variables\ChecksVariable;
use \craft\helpers\Console;

class CheckController extends \craft\console\Controller
{
    
    public $notify = false;
    
    public function options($actionID)
    {
        return ['notify'];
    }
    
    public function actionIndex()
    {
        $checks = ChecksVariable::all();
        $headers = ['Check', 'Status', 'Notes'];
        $rows = [];
        foreach( $checks as $check )
        {
            array_push($rows, [
                $check->getTitle(),
                ($check->isApplicable() ? ($check->passed() ? 'Passed' : 'Failed') : ''),
                $check->getText()
            ]);
        }
        $console = new Console();
        echo "\n";
        echo $console->table($headers, $rows);
        echo "\n";
    }
    
}
