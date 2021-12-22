<?php

namespace jordanbeattie\CraftCmsHealth\variables;
use Craft;
use jordanbeattie\CraftCmsHealth\checks\EnvCheck;
use jordanbeattie\CraftCmsHealth\models\Check;
use jordanbeattie\CraftCmsHealth\controllers\AppController as App;
use craft\helpers\App as CraftApp;
use craft\db\Query;

Class ChecksVariable
{
    
    public static function all()
    {
        return [
            static::env(),
            static::smtp(),
            static::mailhog(),
            static::https(),
            static::sitemap(),
            static::robots(),
            static::seoPlugin()
        ];
    }

    public static function env()
    {
        $env = getenv('ENVIRONMENT');
        $allowed = ['dev', 'staging', 'production'];
        $ok = false;
        if(in_array( $env, $allowed ))
        {
            $ok = true;
        }
        else
        {
            $env = "Envronment '" . $env . "' not recognised";
        }
        return new Check('Environment', $ok, $env);
    }
    
    public static function smtp()
    {
        $settings = CraftApp::mailSettings();
        $transportType = explode('\\', $settings->transportType);
        $transportType = end($transportType);
        $passed = strtoupper($transportType) == "SMTP";
        return $passed ? new Check('SMTP', $passed) : new Check('SMTP', $passed, $transportType);
    }
    
    public static function mailhog()
    {
        
        if( static::smtp()->failed() )
        {
            $check = new Check('Mailhog', false, 'SMTP is not in use');
            $check->setNotApplicable();
            return $check;
        }
    
        $settings = CraftApp::mailSettings()->transportSettings;
        $mailhogInUse = $settings['host'] == "0.0.0.0" && $settings['port'] == "1025" && $settings['encryptionMethod'] == "none";
        
        if( App::isLocal() && $mailhogInUse )
        {
            return new Check('Mailhog', true );
        }
        elseif( App::isLocal() && !$mailhogInUse )
        {
            return new Check('Mailhog', false);
        }
        elseif( !App::isLocal() && $mailhogInUse )
        {
            return new Check('Mailhog', false, 'Mailhog should only be used in dev');
        }
        elseif( !App::isLocal() && !$mailhogInUse )
        {
            $check = new Check('Mailhog', true);
            $check->setNotApplicable();
            return $check;
        }
    }
    
    public static function https()
    {
        $check = new Check('HTTPS', App::usesHttps());
        if( App::isLocal() )
        {
            $check->setNotApplicable();
        }
        return $check;
    }
    
    public static function sitemap()
    {
        $urlOk = false;
        try
        {
            $client = new \GuzzleHttp\Client();
            $res = $client->get(App::url('/sitemap.xml'));
            if( $res->getStatusCode() == "200" )
            {
                $urlOk = true;
            }
        }
        catch( \GuzzleHttp\Exception\BadResponseException $e ){}
        
        if( static::seoPlugin()->passed() )
        {
            $query = new Query();
            
            try{
                $query = $query->from('seo_sitemap')->where(['enabled' => '1'])->count();
                $enabled = $query > 0;
            }
            catch( \Exception $e )
            {
                return new check('Sitemap', false);
            }
    
            if( $enabled && $urlOk )
            {
                return new Check('Sitemap', true);
            }
            elseif( !$urlOk )
            {
                return new Check('Sitemap', false, 'URL is not accessible');
            }
            elseif( !$enabled )
            {
                return new Check('Sitemap', false, '0 sections enabled');
            }
        }
        elseif( $urlOk )
        {
            return new Check('Sitemap', true);
        }
        return new Check('Sitemap', false);
    }
    
    public static function robots()
    {
        $sitemap = App::robotsHasSitemap();
        $blocksAll = App::robotsBlocksAll();
        
        if( !$sitemap )
        {
            return new Check('Robots', false, 'Sitemap missing from robots.txt');
        }
        
        if( App::env('production') )
        {
            return !$blocksAll
                ? new Check('Robots', true)
                : new Check('Robots', false, 'Robots blocked on production');
        }
        else
        {
            return $blocksAll
                ? new Check('Robots', true)
                : new Check('Robots', false, 'Robots should be blocked outside of production');
        }
        
    }
    
    public static function seoPlugin()
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('seo');
        return new Check('SEO Plugin', ( $plugin ? true : false ));
    }
    
}
