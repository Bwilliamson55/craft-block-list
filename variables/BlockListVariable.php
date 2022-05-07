<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\variables;

use Exception;
use bwilliamson\blocklist\BlockList;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class BlockListVariable
{
    // Public Methods
    // =========================================================================


    /**
     * @return array
     */
    public function siteLicenses(): array
    {
        return BlockList::$plugin->blockListService->getLicenses();
    }

    /**
     * @param null $licenseId
     * @return mixed|null
     */
    public function getLicense($licenseId = null)
    {
        return BlockList::$plugin->blockListService->getLicense($licenseId);
    }

    /**
     * @return false|mixed|null
     * @throws Exception
     */
    public function newLicense() {
        return BlockList::$plugin->blockListService->newLicense();
    }

    /**
     * @param null $ip
     * @return bool
     */
    public function auth($ip = null): bool
    {
        return BlockList::$plugin->blockListService->authenticate($ip);
    }

    /**
     * @param int $siteLicenseId
     * @return array
     */
    public function entries(int $siteLicenseId): array
    {
        return BlockList::$plugin->blockListService->getLicenseEntries($siteLicenseId, true);
    }
}
