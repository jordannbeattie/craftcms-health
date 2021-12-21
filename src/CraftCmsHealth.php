<?php

namespace jordanbeattie\CraftCmsHealth;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use \craft\web\View;
use \craft\events\RegisterTemplateRootsEvent;
use \craft\web\twig\variables\CraftVariable;
use jordanbeattie\CraftCmsHealth\utilities\Health;
use jordanbeattie\CraftCmsHealth\variables\ChecksVariable;
use yii\base\Event;

class CraftCmsHealth extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = Health::class;
            }
        );
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['health'] = __DIR__ . '/templates';
            }
        );
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('checks', ChecksVariable::class);
            }
        );
    }
}