<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Cache\Tests\Http\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\LocationService\HideLocationSignal;

class HideLocationSlotTest extends AbstractContentSlotTest implements SlotTest, PurgeForContentExpectation
{
    protected static $locationId = 99;

    public static function createSignal()
    {
        return new HideLocationSignal(
            [
                'contentId' => static::$contentId,
                'locationId' => static::$locationId,
            ]
        );
    }

    public static function generateTags()
    {
        $tags = parent::generateTags();
        $tags[] = 'path-'.static::$locationId;

        return $tags;
    }

    public function getSlotClass()
    {
        return 'eZ\Publish\Core\MVC\Symfony\Cache\Http\SignalSlot\HideLocationSlot';
    }

    public static function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\LocationService\HideLocationSignal'];
    }
}
