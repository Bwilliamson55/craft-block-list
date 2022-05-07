<?php
/**
 * Block List plugin for Craft CMS 3.x
 *
 * A CP extension for basic ip block-list entries
 *
 * @link      Github.com/Bwilliamson55
 * @copyright Benjamin Williamson
 */

namespace bwilliamson\blocklist\controllers;

use craft\errors\MissingComponentException;
use Exception;
use bwilliamson\blocklist\BlockList;

use Craft;
use craft\web\Controller;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * @author    Benjamin Williamson
 * @package   BlockList
 * @since     1
 */
class BlockListController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requirePermission('blocklist:read');
        return $this->redirect('block-list');
    }

    /**
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionNewLicense(): Response
    {
        $this->requirePermission('blocklist:write');
        return $this->renderTemplate('block-list/_edit', []);
    }

    /**
     * @return Response
     * @throws ForbiddenHttpException
     * @throws StaleObjectException
     * @throws BadRequestHttpException
     */
    public function actionDeleteLicense(): Response
    {
        $this->requirePermission('blocklist:write');
        $this->requirePostRequest();
        $licenseId = $this->request->getRequiredBodyParam('id');
        $result = BlockList::$plugin->blockListService->deleteLicense($licenseId);
        return $this->asJson(['success' => $result]);
    }

    /**
     * @param null $licenseId
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionEdit($licenseId = null): Response
    {
        $this->requirePermission('blocklist:read');
        return $this->renderTemplate('block-list/_edit', [
            'licenseId' => $licenseId
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws MissingComponentException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionAddEntry(): Response
    {
        $this->requirePermission('blocklist:write');
        $this->requirePostRequest();
        $params = Craft::$app->getRequest()->getBodyParams()['params'];
        if (!ip2long($params['entry']['ip_range_start']) || !ip2long($params['entry']['ip_range_end'])) {
            Craft::$app->getSession()->setError(Craft::t('block-list',
                'Error - Please enter a valid IP address'));
            return $this->renderTemplate('block-list/_edit', [
                'licenseId' => $params['entry']['site_license_id']
            ]);
        }
        $result = BlockList::$plugin->blockListService->newLicenseEntry($params['entry']);
        if (!$result) {
            Craft::$app->getSession()->setError(Craft::t('block-list',
                'Error - License entry could not be saved'));
        } else {
            Craft::$app->getSession()->setNotice(Craft::t('block-list',
                'Success - License entry saved'));
        }
        return $this->renderTemplate('block-list/_edit', [
            'licenseId' => $params['entry']['site_license_id']
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws StaleObjectException
     */
    public function actionDeleteEntry(): Response
    {
        $this->requirePermission('blocklist:write');
        $this->requirePostRequest();
        $entryId = $this->request->getRequiredBodyParam('id');
        $result = BlockList::$plugin->blockListService->deleteEntry($entryId);
        return $this->asJson(['success' => $result]);
    }

    /**
     * @return Response
     * @throws InvalidConfigException
     * @throws MissingComponentException
     * @throws ForbiddenHttpException
     */
    public function actionUpdateLicense(): Response
    {
        $this->requirePermission('blocklist:write');
        $params = Craft::$app->getRequest()->getBodyParams()['params'];
        $result = BlockList::$plugin->blockListService->updateLicense($params['licenseId'], $params['license']);
        if (!$result) {
            Craft::$app->getSession()->setError(Craft::t('block-list',
                'Error - License could not be saved'));
        } else {
            Craft::$app->getSession()->setNotice(Craft::t('block-list',
                'Success - License updated'));
        }
        return $this->renderTemplate('block-list/_edit', [
            'licenseId' => $params['licenseId']
        ]);
    }

    /**
     * @return Response
     * @throws InvalidConfigException
     * @throws MissingComponentException
     * @throws ForbiddenHttpException
     */
    public function actionAuthenticateTest(): Response
    {
        $this->requirePermission('blocklist:read');
        $params = Craft::$app->getRequest()->getBodyParams()['params'];
        $result = BlockList::$plugin->blockListService->authenticate($params['ip']);
        if (!$result) {
            Craft::$app->getSession()->setError(Craft::t('block-list',
                'Error - IP is NOT authenticated'));
        } else {
            Craft::$app->getSession()->setNotice(Craft::t('block-list',
                'Success - IP Authenticated'));
        }
        return $this->renderTemplate('block-list/_edit', [
            'licenseId' => $params['licenseId']
        ]);
    }
}
