<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\migrations;

use bwilliamson\blocklist\BlockList;

use Craft;
use craft\db\Migration;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public string $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%blocklist_sitelicenserecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%blocklist_sitelicenserecord}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull()->defaultValue(1),
                    'site_license_code' => $this->integer(),
                    'name' => $this->string(255)->notNull(),
                    'site_entity' => $this->integer(),
                    'corporate_id' => $this->string(255)
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%blocklist_blocklistentryrecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%blocklist_blocklistentryrecord}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull()->defaultValue(1),
                    'site_license_id' => $this->integer()->notNull(),
                    'ip_range_start' => $this->integer(10)->unsigned(),
                    'ip_range_end' => $this->integer(10)->unsigned()
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes(): void
    {
        $this->createIndex(
            'sitelicenseid',
            '{{%blocklist_sitelicenserecord}}',
            'id',
            true
        );

        $this->createIndex(
            'sitelicensecodename',
            '{{%blocklist_sitelicenserecord}}',
            ['site_license_code', 'name']
        );

        $this->createIndex(
            'blocklistentryid',
            '{{%blocklist_blocklistentryrecord}}',
            'id',
            true
        );

        $this->createIndex(
            'blocklistentryidsitelicenseid',
            '{{%blocklist_blocklistentryrecord}}',
            ['id', 'site_license_id'],
            true
        );

        $this->createIndex(
            'blocklistentryipranges',
            '{{%blocklist_blocklistentryrecord}}',
            ['ip_range_start', 'ip_range_end'],
            false
        );
    }

    /**
     * @return void
     */
    protected function addForeignKeys(): void
    {
        $this->addForeignKey(
            ('blocklist_sitelicenserecord_siteId'),
            '{{%blocklist_sitelicenserecord}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            ('blocklist_blocklistentryrecord_siteId'),
            '{{%blocklist_blocklistentryrecord}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            ('blocklist_blocklistentryrecord_site_license_id'),
            '{{%blocklist_blocklistentryrecord}}',
            'site_license_id',
            '{{%blocklist_sitelicenserecord}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData(): void
    {
        $licenses = [
            'columns' => [
                'id',
                'site_license_code',
                'name',
                'site_entity',
                'corporate_id'
            ],
            'rows' => [
                [1, 123456, "Company Name Example", 12341234, "Text field for a varchar id"]
            ]
        ];
        $entries = [
            'columns' => [
                'site_license_id',
                'ip_range_start',
                'ip_range_end'
            ],
            'rows' => [
                [1, '192.168.1.1', '192.168.1.255'],
                [1, '127.0.0.1', '127.0.0.1']
            ]
        ];
        foreach ($entries['rows'] as $rowId => $row) {
            if(!ip2long($row[1]) || !ip2long($row[2])) {
                Craft::error("IP row failed validation and was not inserted: $rowId - $row[1] - $row[2]");
                unset($entries['rows'][$rowId]);
                continue;
                }
            $entries['rows'][$rowId][1] = ip2long($row[1]);
            $entries['rows'][$rowId][2] = ip2long($row[2]);
        }
        $this->batchInsert('{{%blocklist_sitelicenserecord}}', $licenses['columns'], $licenses['rows']);
        $this->batchInsert('{{%blocklist_blocklistentryrecord}}', $entries['columns'], $entries['rows']);
    }

    /**
     * @return void
     */
    protected function removeTables(): void
    {
        $this->dropTableIfExists('{{%blocklist_blocklistentryrecord}}');
        $this->dropTableIfExists('{{%blocklist_sitelicenserecord}}');
    }
}
