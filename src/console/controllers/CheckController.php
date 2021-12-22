<?php
namespace jordanbeattie\CraftCmsHealth\console\controllers;

use jordanbeattie\CraftCmsHealth\controllers\AppController as App;
use jordanbeattie\CraftCmsHealth\controllers\CommandController;
use jordanbeattie\CraftCmsHealth\CraftCmsHealth;
use jordanbeattie\CraftCmsHealth\models\Check;
use jordanbeattie\CraftCmsHealth\variables\ChecksVariable;
use \craft\helpers\Console;
use Slack;
use SlackMessage;

class CheckController extends \craft\console\Controller
{
    
    public $notify = false;
    
    public function options($actionID)
    {
        return ['notify'];
    }
    
    public function actionIndex()
    {
        echo "\n";
        echo CommandController::execute($this->notify);
        echo "\n";
    }
    
}
