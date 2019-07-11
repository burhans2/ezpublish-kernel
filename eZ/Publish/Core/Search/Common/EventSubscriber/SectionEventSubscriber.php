<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\EventSubscriber;

use eZ\Publish\Core\Event\Section\AssignSectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SectionEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssignSectionEvent::class => 'onAssignSection',
        ];
    }

    public function onAssignSection(\eZ\Publish\API\Repository\Events\Section\AssignSectionEvent $event)
    {
        $contentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo($event->getContentInfo()->id);
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($contentInfo->id, $contentInfo->currentVersionNo)
        );
    }
}
