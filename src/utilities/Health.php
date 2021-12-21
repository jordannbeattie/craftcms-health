<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace jordanbeattie\CraftCmsHealth\utilities;

use Craft;
use craft\base\Utility;
use craft\helpers\Html;
use craft\web\assets\assetindexes\AssetIndexesAsset;
use jordanbeattie\CraftCmsHealth\variables\ChecksVariable;

/**
 * AssetIndexes represents a AssetIndexes dashboard widget.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class Health extends Utility
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'Health');
    }
    
    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'health';
    }
    
    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return '';
    }
    
    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('health/utilities/page.twig', ['checks' => ChecksVariable::all()]);
    }
}
