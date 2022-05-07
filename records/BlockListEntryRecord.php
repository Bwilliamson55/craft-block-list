<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\records;

use craft\db\ActiveRecord;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class BlockListEntryRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%blocklist_blocklistentryrecord}}';
    }
}
