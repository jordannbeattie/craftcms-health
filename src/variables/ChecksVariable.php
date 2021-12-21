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
        $settings = CraftApp::mailSettings()->transportSettings;
        $mailhogInUse = $settings['host'] == "0.0.0.0" && $settings['port'] == "1025" && $settings['encryptionMethod'] == "none";
        if( static::smtp()->failed() )
        {
            return new Check('Mailhog', false, 'SMTP is not in use');
        }
        if( App::isLocal() && $mailhogInUse )
        {
            return new Check('Mailhog', true );
        }
        elseif( !App::isLocal() && $mailhogInUse )
        {
            return new Check('Mailhog', false, 'Mailhog should only be used in dev');
        }
        elseif( !App::isLocal() )
        {
            $check = new Check('Mailhog', false);
            $check->isApplicable();
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
        $query = new Query();
        $query = $query->from('seo_sitemap')->where(['enabled' => '1'])->count();
        $enabled = $query > 0;
    
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
    
    public static function robots()
    {
        $url = App::url() . '/robots.txt';
        try
        {
            $content = file_get_contents($url);
            
            return new Check('Robots', true);
        }
        catch( \Exception $e )
        {
            return new Check('Robots', false, 'URL not accessible');
        }
    }
    
    public static function seoPlugin()
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('seo');
        return new Check('SEO Plugin', ( $plugin ? true : false ));
    }
    
}
