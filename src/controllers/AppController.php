<?php
namespace jordanbeattie\CraftCmsHealth\controllers;
use \craft\console\Request;
use Craft;
use jordanbeattie\CraftCmsHealth\controllers\AppController as App;
use jordanbeattie\CraftCmsHealth\CraftCmsHealth;

class AppController extends \craft\web\Controller
{
    
    public static function siteNameAndUrl()
    {
        $site = Craft::$app->getSites()->getPrimarySite();
        return $site->getName() . " (" . $site->getBaseUrl() . ")";
    }
    
    public static function env( $env = null )
    {
        $setEnv = getenv('ENVIRONMENT');
        return $env ? $setEnv == $env : $setEnv;
    }
    
    public static function isLocal()
    {
        return static::env('dev');
    }
    
    public static function url( $url = null )
    {
        return Craft::$app->getSites()->getPrimarySite()->getBaseUrl() . $url;
    }
    
    public static function usesHttps()
    {
        return str_contains(static::url(), 'https://');
    }
    
    public static function parseRobots( $url )
    {
        try{
            $client = new \GuzzleHttp\Client();
            $res = $client->get($url);
            $content = $res->getBody()->getContents();
            $array = explode("\n", $content);
            $newarray = [];
            foreach( $array as $line )
            {
                if( $line != "" )
                {
                    $newline = explode(': ', $line);
                    $newarray[$newline[0]] = $newline[1];
                }
            }
            return $newarray;
        }
        catch( \Exception $e )
        {
            return false;
        }
    }
    
    public static function robotsBlocksAll()
    {
        $parsed = static::parseRobots(static::formatUrl(static::url('/robots.txt')));
        if(array_key_exists( 'Disallow', $parsed ))
        {
            if( $parsed['Disallow'] == '/' )
            {
                return true;
            }
        }
        return false;
    }
    
    public function robotsHasUserAgent()
    {
        $parsed = static::parseRobots(static::formatUrl(static::url('/robots.txt')));
        return array_key_exists( 'User-agent', $parsed );
    }
    
    public static function robotsHasSitemap()
    {
        $parsed = static::parseRobots(static::formatUrl(static::url('/robots.txt')));
        return array_key_exists( 'Sitemap', $parsed );
    }
    
    public static function slackMessage()
    {
        $settings = CraftCmsHealth::getInstance()->getSettings();
        $slack = new \Slack($settings->getSlackWebhook());
        $slack->setDefaultUsername('CraftCMS Health Check');
        $slack->setDefaultChannel($settings->getSlackChannel());
        $slack->setDefaultEmoji(":stethoscope:");
        return new \SlackMessage($slack);
    }
    
    public static function slackAvailable()
    {
        return CraftCmsHealth::getInstance()->getSettings()->getSlackWebhook()
            ? true
            : false;
    }
    
    public static function canConnect( $url = null )
    {
        try
        {
            $client = new \GuzzleHttp\Client();
            $res = $client->get( $url ?? App::url());
            return ($res->getStatusCode() == "200");
        }
        catch( \Exception $e ){
            return false;
        }
    }
    
    public static function formatUrl($url)
    {
        return ( static::usesHttps() && !static::canConnect() )
            ? str_replace('https://', 'http://', $url)
            : $url;
    }
    
}
