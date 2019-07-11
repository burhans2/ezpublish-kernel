<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\EventSubscriber;

use eZ\Publish\Core\Event\User\CreateUserEvent;
use eZ\Publish\Core\Event\User\CreateUserGroupEvent;
use eZ\Publish\Core\Event\User\DeleteUserEvent;
use eZ\Publish\Core\Event\User\DeleteUserGroupEvent;
use eZ\Publish\Core\Event\User\MoveUserGroupEvent;
use eZ\Publish\Core\Event\User\UpdateUserEvent;
use eZ\Publish\Core\Event\User\UpdateUserGroupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreateUserEvent::class => 'onCreateUser',
            CreateUserGroupEvent::class => 'onCreateUserGroup',
            DeleteUserEvent::class => 'onDeleteUser',
            DeleteUserGroupEvent::class => 'onDeleteUserGroup',
            MoveUserGroupEvent::class => 'onMoveUserGroup',
            UpdateUserEvent::class => 'onUpdateUser',
            UpdateUserGroupEvent::class => 'onUpdateUserGroup',
        ];
    }

    public function onCreateUser(\eZ\Publish\API\Repository\Events\User\CreateUserEvent $event)
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUser()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onCreateUserGroup(\eZ\Publish\API\Repository\Events\User\CreateUserGroupEvent $event)
    {
        $userGroupContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUserGroup()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userGroupContentInfo->id,
                $userGroupContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userGroupContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onDeleteUser(\eZ\Publish\API\Repository\Events\User\DeleteUserEvent $event)
    {
        $this->searchHandler->deleteContent($event->getUser()->id);

        foreach ($event->getLocations() as $locationId) {
            $this->searchHandler->deleteLocation($locationId, $event->getUser()->id);
        }
    }

    public function onDeleteUserGroup(\eZ\Publish\API\Repository\Events\User\DeleteUserGroupEvent $event)
    {
        $this->searchHandler->deleteContent($event->getUserGroup()->id);

        foreach ($event->getLocations() as $locationId) {
            $this->searchHandler->deleteLocation($locationId, $event->getUserGroup()->id);
        }
    }

    public function onMoveUserGroup(\eZ\Publish\API\Repository\Events\User\MoveUserGroupEvent $event)
    {
        $userGroupContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUserGroup()->id
        );

        $this->indexSubtree($userGroupContentInfo->mainLocationId);
    }

    public function onUpdateUser(\eZ\Publish\API\Repository\Events\User\UpdateUserEvent $event)
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUser()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function onUpdateUserGroup(\eZ\Publish\API\Repository\Events\User\UpdateUserGroupEvent $event)
    {
        $userContentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo(
            $event->getUserGroup()->id
        );

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $userContentInfo->id,
                $userContentInfo->currentVersionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $userContentInfo->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
