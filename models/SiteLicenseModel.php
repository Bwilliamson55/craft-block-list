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

use craft\base\Model;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class SiteLicenseModel extends Model
{
    // Public Properties
    // =========================================================================

    public int $id;
    public $uid;
    public int $site_license_code;
    public string $name;
    public int $site_entity;
    public string $corporate_id;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['corporate_id', 'string'],
            ['name', 'default', 'value' => 'New Site License'],
        ];
        //TODO more rules for validations
    }
}
