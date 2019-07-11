<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\EventSubscriber;

use eZ\Publish\Core\Event\Content\CopyContentEvent;
use eZ\Publish\Core\Event\Content\DeleteContentEvent;
use eZ\Publish\Core\Event\Content\DeleteTranslationEvent;
use eZ\Publish\Core\Event\Content\HideContentEvent;
use eZ\Publish\Core\Event\Content\PublishVersionEvent;
use eZ\Publish\Core\Event\Content\RevealContentEvent;
use eZ\Publish\Core\Event\Content\UpdateContentMetadataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CopyContentEvent::class => 'onCopyContent',
            DeleteContentEvent::class => 'onDeleteContent',
            DeleteTranslationEvent::class => 'onDeleteTranslation',
            HideContentEvent::class => 'onHideContent',
            PublishVersionEvent::class => 'onPublishVersion',
            RevealContentEvent::class => 'onRevealContent',
            UpdateContentMetadataEvent::class => 'onUpdateContentMetadata',
        ];
    }

    public function onCopyContent(\eZ\Publish\API\Repository\Events\Content\CopyContentEvent $event)
    {
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $event->getContent()->getVersionInfo()->getContentInfo()->id,
                $event->getContent()->getVersionInfo()->versionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $event->getContent()->getVersionInfo()->getContentInfo()->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onDeleteContent(\eZ\Publish\API\Repository\Events\Content\DeleteContentEvent $event)
    {
        $this->searchHandler->deleteContent($event->getContentInfo()->id);

        foreach ($event->getLocations() as $locationId) {
            $this->searchHandler->deleteLocation($locationId, $event->getContentInfo()->id);
        }
    }

    public function onDeleteTranslation(\eZ\Publish\API\Repository\Events\Content\DeleteTranslationEvent $event)
    {
        $contentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getContentInfo()->id
        );

        if (!$contentInfo->isPublished) {
            return;
        }

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $contentInfo->id,
                $contentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $contentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onHideContent(\eZ\Publish\API\Repository\Events\Content\HideContentEvent $event)
    {
        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent($event->getContentInfo()->id);
        foreach ($locations as $location) {
            $this->indexSubtree($location->id);
        }
    }

    public function onPublishVersion(\eZ\Publish\API\Repository\Events\Content\PublishVersionEvent $event)
    {
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($event->getContent()->id, $event->getContent()->getVersionInfo()->versionNo)
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent($event->getContent()->id);
        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onRevealContent(\eZ\Publish\API\Repository\Events\Content\RevealContentEvent $event)
    {
        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent($event->getContentInfo()->id);
        foreach ($locations as $location) {
            $this->indexSubtree($location->id);
        }
    }

    public function onUpdateContentMetadata(\eZ\Publish\API\Repository\Events\Content\UpdateContentMetadataEvent $event)
    {
        $contentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo($event->getContent()->id);
        if (!$contentInfo->isPublished) {
            return;
        }
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($contentInfo->id, $contentInfo->currentVersionNo)
        );
        $this->searchHandler->indexLocation($this->persistenceHandler->locationHandler()->load($contentInfo->mainLocationId));
    }
}
