<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\services;

use craft\helpers\ArrayHelper;
use craft\helpers\StringHelper;
use Exception;

use Craft;
use craft\base\Component;
use bwilliamson\blocklist\models\EntryModel;
use bwilliamson\blocklist\models\SiteLicenseModel;
use bwilliamson\blocklist\records\BlockListEntryRecord;
use bwilliamson\blocklist\records\SiteLicenseRecord;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class BlockListService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @return false|mixed|null
     * @throws Exception
     */
    public function newLicense() {
        return $this->saveLicense($this->createLicense());
    }

    /**
     * @param array $attributes
     * @return bool
     * @throws Exception
     */
    public function newLicenseEntry(array $attributes = []): bool
    {
        return $this->saveEntry($this->createLicenseEntry($attributes));
    }

    /**
     * @param array $attributes
     * @return SiteLicenseModel
     */
    public function createLicense(array $attributes = []): SiteLicenseModel
    {
        $defaults = [
            'id' => 0,
            'site_license_code' => 0,
            'name' => 'New License',
            'site_entity' => 0,
            'corporate_id' => ''
        ];
        $attrs = array_merge($defaults, $attributes);
        return new SiteLicenseModel($attrs);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function updateLicense(int $id, array $attributes = []): bool
    {
        /** @var SiteLicenseRecord $license */
        $license = $this->getLicense($id);
        $license->setAttributes($attributes, false);
        return $license->save();
    }

    /**
     * @param array $attributes
     * @return EntryModel
     */
    public function createLicenseEntry(array $attributes = []): EntryModel
    {
        $defaults = [
            'id' => 0,
            'site_license_id' => 1,
            'ip_range_start' => ip2long('127.0.0.1'),
            'ip_range_end' => ip2long('127.0.0.1')
        ];
        $attributes['ip_range_start'] = ip2long($attributes['ip_range_start'] ?? '127.0.0.1');
        $attributes['ip_range_end'] = ip2long($attributes['ip_range_end'] ?? '127.0.0.1');
        $attrs = array_merge($defaults, $attributes);
        return new EntryModel($attrs);
    }

    /**
     * @return array
     */
    public function getLicenses(): array
    {
        return SiteLicenseRecord::find()
            ->orderBy("id ASC")
            ->all();
    }

    /**
     * @param null $id
     * @return mixed|null
     */
    public function getLicense($id = null) {
        if ($id === null) { return null;}
        return ArrayHelper::firstWhere($this->getLicenses(), 'id', $id);
    }

    /**
     * @param int $siteLicenseId
     * @param bool $long
     * @return array
     */
    public function getLicenseEntries(int $siteLicenseId, bool $long = false): array
    {
        $entries = BlockListEntryRecord::find()
            ->orderBy("id ASC")
            ->where(['site_license_id' => $siteLicenseId])
            ->all();
        if ($long) {
            $longEntries = [];
            foreach ($entries as $id => $entry) {
                /** @var BlockListEntryRecord $entry */
                //stripping the object to avoid 'preparing' values which destroy long2ip values
                foreach($entry->getAttributes() as $key => $value) {
                    $longEntries[$id][$key] = $value;
                }
                $longEntries[$id]['ip_range_start'] = long2ip($entry['ip_range_start']);
                $longEntries[$id]['ip_range_end'] = long2ip($entry['ip_range_end']);
            }
            return $longEntries;
        }
        return $entries;
    }

    /**
     * @param int $id
     * @return array|ActiveRecord|null
     */
    public function getEntry(int $id) {
        return BlockListEntryRecord::find()
            ->where(['id' => $id])
            ->one();
    }

    /**
     * @param string $ip
     * @return bool
     */
    public function authenticate(string $ip): bool
    {
        if (!$ipint = ip2long($ip)) {
            Craft::warning("IP $ip failed authentication");
            return false;
        }
        $query = new BlockListEntryRecord();
        $result = $query::find()
            ->where(['<=',  'ip_range_start', $ipint])
            ->andWhere(['>=', 'ip_range_end', $ipint])->count();
        return (bool)$result;
    }

    /**
     * @param SiteLicenseModel $siteLicenseModel
     * @return false|mixed|null
     * @throws Exception
     */
    public function saveLicense(SiteLicenseModel $siteLicenseModel)
    {
        $isNew = !$siteLicenseModel->id;
        if ($isNew) {
            $siteLicenseModel->uid = StringHelper::UUID();
        }
        $record = new SiteLicenseRecord();
        $record->setAttributes($siteLicenseModel->getAttributes(), false);

        if (!$record->save()) {
            $siteLicenseModel->addErrors($record->getErrors());
            return false;
        }
        return $record->getAttribute('id');
    }

    /**
     * @param EntryModel $entryModel
     * @return bool
     * @throws Exception
     */
    public function saveEntry(EntryModel $entryModel): bool
    {
        $isNew = !$entryModel->id;

        if ($isNew) {
            $entryModel->uid = StringHelper::UUID();
        }
        $record = new BlockListEntryRecord();
        $record->setAttributes($entryModel->getAttributes(), false);

        if (!$record->save()) {
            $entryModel->addErrors($record->getErrors());
            return false;
        }
        return $record->getAttribute('id');
    }

    /**
     * @param int $id
     * @return bool
     * @throws StaleObjectException
     */
    public function deleteLicense(int $id): bool
    {
        $record = SiteLicenseRecord::findOne($id);
        if (!$record) {
            return false;
        }
        $record->delete();
        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws StaleObjectException
     */
    public function deleteEntry(int $id): bool
    {
        $record = BlockListEntryRecord::findOne($id);
        if (!$record) {
            return false;
        }
        $record->delete();
        return true;
    }
}
