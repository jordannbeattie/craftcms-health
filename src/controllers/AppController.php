<?php
namespace jordanbeattie\CraftCmsHealth\controllers;
use \craft\console\Request;
use Craft;

class AppController extends \craft\web\Controller
{
    
    public static function isLocal()
    {
        return getenv('ENVIRONMENT') == "dev";
    }
    
    public static function url( $url = null )
    {
        return Craft::$app->getSites()->getPrimarySite()->getBaseUrl() . $url;
    }
    
    public static function usesHttps()
    {
        if( str_contains(static::url(), 'https://') )
        {
            if( Craft::$app->request->getIsSiteRequest() )
            {
                if(isset($_SERVER['HTTPS'])) {
                    if ($_SERVER['HTTPS'] == "on") {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
}
