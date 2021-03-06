<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event;

use eZ\Publish\API\Repository\BookmarkService as BookmarkServiceInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Event\Bookmark\BeforeCreateBookmarkEvent;
use eZ\Publish\Core\Event\Bookmark\BeforeDeleteBookmarkEvent;
use eZ\Publish\Core\Event\Bookmark\CreateBookmarkEvent;
use eZ\Publish\Core\Event\Bookmark\DeleteBookmarkEvent;
use eZ\Publish\SPI\Repository\Decorator\BookmarkServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookmarkService extends BookmarkServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        BookmarkServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createBookmark(Location $location): void
    {
        $eventData = [$location];

        $beforeEvent = new BeforeCreateBookmarkEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->createBookmark($location);

        $this->eventDispatcher->dispatch(new CreateBookmarkEvent(...$eventData));
    }

    public function deleteBookmark(Location $location): void
    {
        $eventData = [$location];

        $beforeEvent = new BeforeDeleteBookmarkEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteBookmark($location);

        $this->eventDispatcher->dispatch(new DeleteBookmarkEvent(...$eventData));
    }
}
