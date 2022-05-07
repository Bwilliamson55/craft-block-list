<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist;

use bwilliamson\blocklist\services\BlockListService as BlockListServiceService;
use bwilliamson\blocklist\variables\BlockListVariable;

use Craft;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;

use yii\base\Event;

/**
 * Class BlockList
 *
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 *
 * @property  BlockListServiceService $blockListService
 */
class BlockList extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var BlockList
     */
    public static BlockList $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1';

    /**
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['block-list/new'] = 'block-list/block-list/new-license';
                $event->rules['block-list/edit/<licenseId:\d+>'] = 'block-list/block-list/edit';
                $event->rules['block-list/edit/update-license'] = 'block-list/block-list/update-license';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('blockList', BlockListVariable::class);
            }
        );

        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event) {
                $event->permissions['Block List'] = [
                    'blocklist:read' => [
                        'label' => 'Block List Read',
                    ],
                    'blocklist:write' => [
                        'label' => 'Block List Write',
                    ],
                ];
            }
        );

        Craft::info(
            Craft::t(
                'block-list',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
}
