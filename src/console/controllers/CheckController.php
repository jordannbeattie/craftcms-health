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
    public $output = true;
    
    public function options($actionID)
    {
        return ['notify', 'output'];
    }
    
    public function optionAliases()
    {
        return [
          'notify' => 'notify',
          'no-output' => 'noOutput'
        ];
    }
    
    public function actionIndex()
    {
        if( $this->notify )
        {
            echo "\n";
            echo CommandController::execute($this->notify, $this->output);
            echo "\n";
        }
        else
        {
            echo CommandController::execute($this->notify, $this->output);
        }
    }
    
}
