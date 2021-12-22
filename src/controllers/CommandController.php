<?php
namespace jordanbeattie\CraftCmsHealth\controllers;
use \craft\console\Request;
use Craft;
use craft\helpers\Console;
use jordanbeattie\CraftCmsHealth\controllers\AppController as App;
use jordanbeattie\CraftCmsHealth\CraftCmsHealth;
use jordanbeattie\CraftCmsHealth\variables\ChecksVariable;

class CommandController extends \craft\web\Controller
{
    
    public static function execute($notify, $returnTable = true)
    {
        $headers = ['Check', 'Status', 'Notes'];
        $rows = [];
        $failed = [];
        $checks = ChecksVariable::all();
        
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
    
        if( count($failed) && $notify )
        {
            static::notifyViaSlack($failed);
        }
        
        if( $returnTable )
        {
            $console = new Console();
            return $console->table($headers, $rows);
        }
        
    }
    
    public static function notifyViaSlack($failed)
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
        
            return $message->send() ? true : false;
        }
    }
    
}
