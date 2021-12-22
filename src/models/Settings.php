<?php
namespace jordanbeattie\CraftCmsHealth\models;
use Craft;

class Settings extends \craft\base\Model
{
    public $slackWebhook;
    
    public function rules()
    {
        return [];
    }
    
    public function getSlackWebhook(): string
    {
        return Craft::parseEnv($this->slackWebhook);
    }
    
}
