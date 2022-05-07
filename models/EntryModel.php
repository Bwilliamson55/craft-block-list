<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\models;

use bwilliamson\blocklist\BlockList;

use Craft;
use craft\base\Model;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class EntryModel extends Model
{
    // Public Properties
    // =========================================================================

    public int $id;
    public $uid;
    public int $site_license_id;
    public int $ip_range_start;
    public int $ip_range_end;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }
}
