<?php
namespace jordanbeattie\CraftCmsHealth\models;
use Craft;

class Settings extends \craft\base\Model
{
    public $slackWebhook, $slackChannel;
    
    public function rules()
    {
        return [];
    }
    
    public function getSlackWebhook(): string
    {
        return Craft::parseEnv($this->slackWebhook);
    }
    
    public function getSlackChannel(): string
    {
        $setting = trim(Craft::parseEnv($this->slackChannel));
        if( $setting )
        {
            if( !(substr( $setting, 0, 1 ) === "#") && !(substr( $setting, 0, 1 ) === "@") )
            {
                $setting = "#" . $setting;
            }
        }
        return $setting ?? '#general';
    }
    
}
