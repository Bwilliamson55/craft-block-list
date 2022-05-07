<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\assetbundles\blocklistcpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class BlockListCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = "@bwilliamson/blocklist/assetbundles/blocklistcpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/BlockList.js',
        ];

        $this->css = [
            'css/BlockList.css',
        ];

        parent::init();
    }
}
