<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event\Notification;

use eZ\Publish\API\Repository\Events\Notification\BeforeMarkNotificationAsReadEvent as BeforeMarkNotificationAsReadEventInterface;
use eZ\Publish\API\Repository\Values\Notification\Notification;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeMarkNotificationAsReadEvent extends Event implements BeforeMarkNotificationAsReadEventInterface
{
    /** @var \eZ\Publish\API\Repository\Values\Notification\Notification */
    private $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
