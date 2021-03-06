<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Location;

use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;

interface BeforeCreateLocationEvent extends BeforeEvent
{
    public function getContentInfo(): ContentInfo;

    public function getLocationCreateStruct(): LocationCreateStruct;

    public function getLocation(): Location;

    public function setLocation(?Location $location): void;

    public function hasLocation(): bool;
}
