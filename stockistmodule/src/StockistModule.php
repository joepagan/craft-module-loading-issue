<?php
/**
 * Stockist module for Craft CMS 3.x
 *
 * Parses data on a stockist import
 *
 * @link      https://madebyextreme.com/
 * @copyright Copyright (c) 2021 Extreme
 */

namespace modules\stockistmodule;

use modules\stockistmodule\assetbundles\stockistmodule\StockistModuleAsset;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;

use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;

// Services
use modules\stockistmodule\services\{
    ElementService,
    FeedService,
    ApiService,
};

// FeedMe
use craft\feedme\events\FeedProcessEvent;
use craft\feedme\services\Process;

/**
 * Class StockistModule
 *
 * @author    Extreme
 * @package   StockistModule
 * @since     1.0.0
 * @property FeedService $feedService
 * @property ApiService $elementService
 *
 */
class StockistModule extends Module
{
    const feedId = 2;
    /**
     * @var StockistModule
     */
    public static StockistModule $instance;

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@modules/stockistmodule', $this->getBasePath());
        $this->controllerNamespace = 'modules\stockistmodule\controllers';

        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
            $i18n->translations[$id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@modules/stockistmodule/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }

        $this->setComponents([
            'feedService' => FeedService::class,
            'elementService' => ElementService::class,
            'apiService' => ApiService::class,
        ]);

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(StockistModuleAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
        }

        Event::on(
            Process::class,
            Process::EVENT_STEP_BEFORE_ELEMENT_SAVE,
            function(FeedProcessEvent $event) {
                if ($event->feed['id'] === self::feedId) {
                    self::getInstance()->feedService->stepBeforeElementSave($event);
                }
            }
        );

        Craft::info(
            Craft::t(
                'stockist-module',
                '{name} module loaded',
                ['name' => 'Stockist']
            ),
            __METHOD__
        );
    }
}
