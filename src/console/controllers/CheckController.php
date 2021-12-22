<?php
namespace jordanbeattie\CraftCmsHealth\console\controllers;

use jordanbeattie\CraftCmsHealth\controllers\AppController as App;
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
        $failed = [];
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
            if( $check->isApplicable() && $check->failed() )
            {
                $failed[$check->getTitle()] = $check->getText() ?? 'Failed';
            }
        }
        
        $console = new Console();
        echo "\n";
        echo $console->table($headers, $rows);
        echo "\n";
        
        if( count($failed) && $this->notify )
        {
            $this->sendNotification($failed);
        }
        
    }
    
    public function sendNotification($failed)
    {
        if( App::slackAvailable() && count($failed) )
        {
            $message = App::slackMessage();
            
            $attachment = new \SlackAttachment(count($failed) . " checks failed for " . App::siteNameAndUrl());
            $attachment->setText(count($failed) . " checks failed for " . App::siteNameAndUrl());
            $attachment->setColor("#a10002");
            foreach( $failed as $check => $text )
            {
                $attachment->addField($check, $text);
            }
            $message->addAttachment($attachment);
            
            echo $message->send() ? "Notification sent!" : "Error sending notification";
        }
        else
        {
            echo "Notification could not be sent. No webhook set.";
        }
        echo "\n \n";
    
    }
    
}
