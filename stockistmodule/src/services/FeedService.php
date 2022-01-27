<?php
namespace modules\stockistmodule\services;

use Craft;
use modules\stockistmodule\StockistModule;
use yii\base\Component;

use craft\feedme\events\FeedProcessEvent;

class FeedService extends Component
{
    /**
     * Before each element is saved, where the majority of data is processed during the import process
     * @param FeedProcessEvent $event
     */
    public function stepBeforeElementSave(FeedProcessEvent $event)
    {
        Craft::debug('', __METHOD__);
        StockistModule::getInstance()->elementService->process($event);
    }
}
