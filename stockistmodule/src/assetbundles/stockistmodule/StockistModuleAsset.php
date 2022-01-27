<?php
/**
 * Stockist module for Craft CMS 3.x
 *
 * Parses data on a stockist import
 *
 * @link      https://madebyextreme.com/
 * @copyright Copyright (c) 2021 Extreme
 */

namespace modules\stockistmodule\assetbundles\stockistmodule;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Extreme
 * @package   StockistModule
 * @since     1.0.0
 */
class StockistModuleAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@modules/stockistmodule/assetbundles/stockistmodule/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/StockistModule.js',
        ];

        $this->css = [
            'css/StockistModule.css',
        ];

        parent::init();
    }
}
