<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Event\Tests;

use eZ\Publish\API\Repository\Events\Role\BeforeAddPolicyByRoleDraftEvent as BeforeAddPolicyByRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeAddPolicyEvent as BeforeAddPolicyEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeAssignRoleToUserEvent as BeforeAssignRoleToUserEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeAssignRoleToUserGroupEvent as BeforeAssignRoleToUserGroupEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeCreateRoleDraftEvent as BeforeCreateRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeCreateRoleEvent as BeforeCreateRoleEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeDeletePolicyEvent as BeforeDeletePolicyEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeDeleteRoleDraftEvent as BeforeDeleteRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeDeleteRoleEvent as BeforeDeleteRoleEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforePublishRoleDraftEvent as BeforePublishRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeRemovePolicyByRoleDraftEvent as BeforeRemovePolicyByRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeRemoveRoleAssignmentEvent as BeforeRemoveRoleAssignmentEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeUnassignRoleFromUserEvent as BeforeUnassignRoleFromUserEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeUnassignRoleFromUserGroupEvent as BeforeUnassignRoleFromUserGroupEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeUpdatePolicyByRoleDraftEvent as BeforeUpdatePolicyByRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeUpdatePolicyEvent as BeforeUpdatePolicyEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeUpdateRoleDraftEvent as BeforeUpdateRoleDraftEventInterface;
use eZ\Publish\API\Repository\Events\Role\BeforeUpdateRoleEvent as BeforeUpdateRoleEventInterface;
use eZ\Publish\API\Repository\RoleService as RoleServiceInterface;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\API\Repository\Values\User\PolicyCreateStruct;
use eZ\Publish\API\Repository\Values\User\PolicyDraft;
use eZ\Publish\API\Repository\Values\User\PolicyUpdateStruct;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use eZ\Publish\API\Repository\Values\User\RoleCreateStruct;
use eZ\Publish\API\Repository\Values\User\RoleDraft;
use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use eZ\Publish\Core\Event\Role\AddPolicyByRoleDraftEvent;
use eZ\Publish\Core\Event\Role\AddPolicyEvent;
use eZ\Publish\Core\Event\Role\AssignRoleToUserEvent;
use eZ\Publish\Core\Event\Role\AssignRoleToUserGroupEvent;
use eZ\Publish\Core\Event\Role\BeforeAddPolicyByRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeAddPolicyEvent;
use eZ\Publish\Core\Event\Role\BeforeAssignRoleToUserEvent;
use eZ\Publish\Core\Event\Role\BeforeAssignRoleToUserGroupEvent;
use eZ\Publish\Core\Event\Role\BeforeCreateRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeCreateRoleEvent;
use eZ\Publish\Core\Event\Role\BeforeDeletePolicyEvent;
use eZ\Publish\Core\Event\Role\BeforeDeleteRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeDeleteRoleEvent;
use eZ\Publish\Core\Event\Role\BeforePublishRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeRemovePolicyByRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeRemoveRoleAssignmentEvent;
use eZ\Publish\Core\Event\Role\BeforeUnassignRoleFromUserEvent;
use eZ\Publish\Core\Event\Role\BeforeUnassignRoleFromUserGroupEvent;
use eZ\Publish\Core\Event\Role\BeforeUpdatePolicyByRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeUpdatePolicyEvent;
use eZ\Publish\Core\Event\Role\BeforeUpdateRoleDraftEvent;
use eZ\Publish\Core\Event\Role\BeforeUpdateRoleEvent;
use eZ\Publish\Core\Event\Role\CreateRoleDraftEvent;
use eZ\Publish\Core\Event\Role\CreateRoleEvent;
use eZ\Publish\Core\Event\Role\DeletePolicyEvent;
use eZ\Publish\Core\Event\Role\DeleteRoleDraftEvent;
use eZ\Publish\Core\Event\Role\DeleteRoleEvent;
use eZ\Publish\Core\Event\Role\PublishRoleDraftEvent;
use eZ\Publish\Core\Event\Role\RemovePolicyByRoleDraftEvent;
use eZ\Publish\Core\Event\Role\RemoveRoleAssignmentEvent;
use eZ\Publish\Core\Event\Role\UnassignRoleFromUserEvent;
use eZ\Publish\Core\Event\Role\UnassignRoleFromUserGroupEvent;
use eZ\Publish\Core\Event\Role\UpdatePolicyByRoleDraftEvent;
use eZ\Publish\Core\Event\Role\UpdatePolicyEvent;
use eZ\Publish\Core\Event\Role\UpdateRoleDraftEvent;
use eZ\Publish\Core\Event\Role\UpdateRoleEvent;
use eZ\Publish\Core\Event\RoleService;

class RoleServiceTest extends AbstractServiceTest
{
    public function testDeletePolicyEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeletePolicyEvent::class,
            DeletePolicyEvent::class
        );

        $parameters = [
            $this->createMock(Policy::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->deletePolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeletePolicyEvent::class, 0],
            [DeletePolicyEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testDeletePolicyStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeletePolicyEvent::class,
            DeletePolicyEvent::class
        );

        $parameters = [
            $this->createMock(Policy::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeDeletePolicyEvent::class, function (BeforeDeletePolicyEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->deletePolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeletePolicyEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeDeletePolicyEvent::class, 0],
            [DeletePolicyEvent::class, 0],
        ]);
    }

    public function testUpdateRoleEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateRoleEvent::class,
            UpdateRoleEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(RoleUpdateStruct::class),
        ];

        $updatedRole = $this->createMock(Role::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updateRole')->willReturn($updatedRole);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedRole, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateRoleEvent::class, 0],
            [UpdateRoleEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdateRoleResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateRoleEvent::class,
            UpdateRoleEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(RoleUpdateStruct::class),
        ];

        $updatedRole = $this->createMock(Role::class);
        $eventUpdatedRole = $this->createMock(Role::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updateRole')->willReturn($updatedRole);

        $traceableEventDispatcher->addListener(BeforeUpdateRoleEvent::class, function (BeforeUpdateRoleEventInterface $event) use ($eventUpdatedRole) {
            $event->setUpdatedRole($eventUpdatedRole);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedRole, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateRoleEvent::class, 10],
            [BeforeUpdateRoleEvent::class, 0],
            [UpdateRoleEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateRoleStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateRoleEvent::class,
            UpdateRoleEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(RoleUpdateStruct::class),
        ];

        $updatedRole = $this->createMock(Role::class);
        $eventUpdatedRole = $this->createMock(Role::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updateRole')->willReturn($updatedRole);

        $traceableEventDispatcher->addListener(BeforeUpdateRoleEvent::class, function (BeforeUpdateRoleEventInterface $event) use ($eventUpdatedRole) {
            $event->setUpdatedRole($eventUpdatedRole);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedRole, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateRoleEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUpdateRoleEvent::class, 0],
            [UpdateRoleEvent::class, 0],
        ]);
    }

    public function testPublishRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforePublishRoleDraftEvent::class,
            PublishRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->publishRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforePublishRoleDraftEvent::class, 0],
            [PublishRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testPublishRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforePublishRoleDraftEvent::class,
            PublishRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforePublishRoleDraftEvent::class, function (BeforePublishRoleDraftEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->publishRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforePublishRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforePublishRoleDraftEvent::class, 0],
            [PublishRoleDraftEvent::class, 0],
        ]);
    }

    public function testAssignRoleToUserEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignRoleToUserEvent::class,
            AssignRoleToUserEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(User::class),
            $this->createMock(RoleLimitation::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->assignRoleToUser(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignRoleToUserEvent::class, 0],
            [AssignRoleToUserEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testAssignRoleToUserStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignRoleToUserEvent::class,
            AssignRoleToUserEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(User::class),
            $this->createMock(RoleLimitation::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeAssignRoleToUserEvent::class, function (BeforeAssignRoleToUserEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->assignRoleToUser(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignRoleToUserEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [AssignRoleToUserEvent::class, 0],
            [BeforeAssignRoleToUserEvent::class, 0],
        ]);
    }

    public function testAddPolicyEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAddPolicyEvent::class,
            AddPolicyEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(PolicyCreateStruct::class),
        ];

        $updatedRole = $this->createMock(Role::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('addPolicy')->willReturn($updatedRole);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->addPolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedRole, $result);
        $this->assertSame($calledListeners, [
            [BeforeAddPolicyEvent::class, 0],
            [AddPolicyEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnAddPolicyResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAddPolicyEvent::class,
            AddPolicyEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(PolicyCreateStruct::class),
        ];

        $updatedRole = $this->createMock(Role::class);
        $eventUpdatedRole = $this->createMock(Role::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('addPolicy')->willReturn($updatedRole);

        $traceableEventDispatcher->addListener(BeforeAddPolicyEvent::class, function (BeforeAddPolicyEventInterface $event) use ($eventUpdatedRole) {
            $event->setUpdatedRole($eventUpdatedRole);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->addPolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedRole, $result);
        $this->assertSame($calledListeners, [
            [BeforeAddPolicyEvent::class, 10],
            [BeforeAddPolicyEvent::class, 0],
            [AddPolicyEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testAddPolicyStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAddPolicyEvent::class,
            AddPolicyEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(PolicyCreateStruct::class),
        ];

        $updatedRole = $this->createMock(Role::class);
        $eventUpdatedRole = $this->createMock(Role::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('addPolicy')->willReturn($updatedRole);

        $traceableEventDispatcher->addListener(BeforeAddPolicyEvent::class, function (BeforeAddPolicyEventInterface $event) use ($eventUpdatedRole) {
            $event->setUpdatedRole($eventUpdatedRole);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->addPolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedRole, $result);
        $this->assertSame($calledListeners, [
            [BeforeAddPolicyEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [AddPolicyEvent::class, 0],
            [BeforeAddPolicyEvent::class, 0],
        ]);
    }

    public function testUpdateRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateRoleDraftEvent::class,
            UpdateRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(RoleUpdateStruct::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updateRoleDraft')->willReturn($updatedRoleDraft);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateRoleDraftEvent::class, 0],
            [UpdateRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdateRoleDraftResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateRoleDraftEvent::class,
            UpdateRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(RoleUpdateStruct::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $eventUpdatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updateRoleDraft')->willReturn($updatedRoleDraft);

        $traceableEventDispatcher->addListener(BeforeUpdateRoleDraftEvent::class, function (BeforeUpdateRoleDraftEventInterface $event) use ($eventUpdatedRoleDraft) {
            $event->setUpdatedRoleDraft($eventUpdatedRoleDraft);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateRoleDraftEvent::class, 10],
            [BeforeUpdateRoleDraftEvent::class, 0],
            [UpdateRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateRoleDraftEvent::class,
            UpdateRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(RoleUpdateStruct::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $eventUpdatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updateRoleDraft')->willReturn($updatedRoleDraft);

        $traceableEventDispatcher->addListener(BeforeUpdateRoleDraftEvent::class, function (BeforeUpdateRoleDraftEventInterface $event) use ($eventUpdatedRoleDraft) {
            $event->setUpdatedRoleDraft($eventUpdatedRoleDraft);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updateRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdateRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUpdateRoleDraftEvent::class, 0],
            [UpdateRoleDraftEvent::class, 0],
        ]);
    }

    public function testAssignRoleToUserGroupEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignRoleToUserGroupEvent::class,
            AssignRoleToUserGroupEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(UserGroup::class),
            $this->createMock(RoleLimitation::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->assignRoleToUserGroup(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignRoleToUserGroupEvent::class, 0],
            [AssignRoleToUserGroupEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testAssignRoleToUserGroupStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAssignRoleToUserGroupEvent::class,
            AssignRoleToUserGroupEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(UserGroup::class),
            $this->createMock(RoleLimitation::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeAssignRoleToUserGroupEvent::class, function (BeforeAssignRoleToUserGroupEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->assignRoleToUserGroup(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeAssignRoleToUserGroupEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [AssignRoleToUserGroupEvent::class, 0],
            [BeforeAssignRoleToUserGroupEvent::class, 0],
        ]);
    }

    public function testUnassignRoleFromUserEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUnassignRoleFromUserEvent::class,
            UnassignRoleFromUserEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(User::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->unassignRoleFromUser(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeUnassignRoleFromUserEvent::class, 0],
            [UnassignRoleFromUserEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUnassignRoleFromUserStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUnassignRoleFromUserEvent::class,
            UnassignRoleFromUserEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(User::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeUnassignRoleFromUserEvent::class, function (BeforeUnassignRoleFromUserEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->unassignRoleFromUser(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeUnassignRoleFromUserEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUnassignRoleFromUserEvent::class, 0],
            [UnassignRoleFromUserEvent::class, 0],
        ]);
    }

    public function testUpdatePolicyByRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdatePolicyByRoleDraftEvent::class,
            UpdatePolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyDraft::class),
            $this->createMock(PolicyUpdateStruct::class),
        ];

        $updatedPolicyDraft = $this->createMock(PolicyDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updatePolicyByRoleDraft')->willReturn($updatedPolicyDraft);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updatePolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedPolicyDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdatePolicyByRoleDraftEvent::class, 0],
            [UpdatePolicyByRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdatePolicyByRoleDraftResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdatePolicyByRoleDraftEvent::class,
            UpdatePolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyDraft::class),
            $this->createMock(PolicyUpdateStruct::class),
        ];

        $updatedPolicyDraft = $this->createMock(PolicyDraft::class);
        $eventUpdatedPolicyDraft = $this->createMock(PolicyDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updatePolicyByRoleDraft')->willReturn($updatedPolicyDraft);

        $traceableEventDispatcher->addListener(BeforeUpdatePolicyByRoleDraftEvent::class, function (BeforeUpdatePolicyByRoleDraftEventInterface $event) use ($eventUpdatedPolicyDraft) {
            $event->setUpdatedPolicyDraft($eventUpdatedPolicyDraft);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updatePolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedPolicyDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdatePolicyByRoleDraftEvent::class, 10],
            [BeforeUpdatePolicyByRoleDraftEvent::class, 0],
            [UpdatePolicyByRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdatePolicyByRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdatePolicyByRoleDraftEvent::class,
            UpdatePolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyDraft::class),
            $this->createMock(PolicyUpdateStruct::class),
        ];

        $updatedPolicyDraft = $this->createMock(PolicyDraft::class);
        $eventUpdatedPolicyDraft = $this->createMock(PolicyDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updatePolicyByRoleDraft')->willReturn($updatedPolicyDraft);

        $traceableEventDispatcher->addListener(BeforeUpdatePolicyByRoleDraftEvent::class, function (BeforeUpdatePolicyByRoleDraftEventInterface $event) use ($eventUpdatedPolicyDraft) {
            $event->setUpdatedPolicyDraft($eventUpdatedPolicyDraft);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updatePolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedPolicyDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdatePolicyByRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUpdatePolicyByRoleDraftEvent::class, 0],
            [UpdatePolicyByRoleDraftEvent::class, 0],
        ]);
    }

    public function testCreateRoleEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateRoleEvent::class,
            CreateRoleEvent::class
        );

        $parameters = [
            $this->createMock(RoleCreateStruct::class),
        ];

        $roleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('createRole')->willReturn($roleDraft);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($roleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateRoleEvent::class, 0],
            [CreateRoleEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnCreateRoleResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateRoleEvent::class,
            CreateRoleEvent::class
        );

        $parameters = [
            $this->createMock(RoleCreateStruct::class),
        ];

        $roleDraft = $this->createMock(RoleDraft::class);
        $eventRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('createRole')->willReturn($roleDraft);

        $traceableEventDispatcher->addListener(BeforeCreateRoleEvent::class, function (BeforeCreateRoleEventInterface $event) use ($eventRoleDraft) {
            $event->setRoleDraft($eventRoleDraft);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateRoleEvent::class, 10],
            [BeforeCreateRoleEvent::class, 0],
            [CreateRoleEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testCreateRoleStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateRoleEvent::class,
            CreateRoleEvent::class
        );

        $parameters = [
            $this->createMock(RoleCreateStruct::class),
        ];

        $roleDraft = $this->createMock(RoleDraft::class);
        $eventRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('createRole')->willReturn($roleDraft);

        $traceableEventDispatcher->addListener(BeforeCreateRoleEvent::class, function (BeforeCreateRoleEventInterface $event) use ($eventRoleDraft) {
            $event->setRoleDraft($eventRoleDraft);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateRoleEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeCreateRoleEvent::class, 0],
            [CreateRoleEvent::class, 0],
        ]);
    }

    public function testRemovePolicyByRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemovePolicyByRoleDraftEvent::class,
            RemovePolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyDraft::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('removePolicyByRoleDraft')->willReturn($updatedRoleDraft);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->removePolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeRemovePolicyByRoleDraftEvent::class, 0],
            [RemovePolicyByRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnRemovePolicyByRoleDraftResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemovePolicyByRoleDraftEvent::class,
            RemovePolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyDraft::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $eventUpdatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('removePolicyByRoleDraft')->willReturn($updatedRoleDraft);

        $traceableEventDispatcher->addListener(BeforeRemovePolicyByRoleDraftEvent::class, function (BeforeRemovePolicyByRoleDraftEventInterface $event) use ($eventUpdatedRoleDraft) {
            $event->setUpdatedRoleDraft($eventUpdatedRoleDraft);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->removePolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeRemovePolicyByRoleDraftEvent::class, 10],
            [BeforeRemovePolicyByRoleDraftEvent::class, 0],
            [RemovePolicyByRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testRemovePolicyByRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemovePolicyByRoleDraftEvent::class,
            RemovePolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyDraft::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $eventUpdatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('removePolicyByRoleDraft')->willReturn($updatedRoleDraft);

        $traceableEventDispatcher->addListener(BeforeRemovePolicyByRoleDraftEvent::class, function (BeforeRemovePolicyByRoleDraftEventInterface $event) use ($eventUpdatedRoleDraft) {
            $event->setUpdatedRoleDraft($eventUpdatedRoleDraft);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->removePolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeRemovePolicyByRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeRemovePolicyByRoleDraftEvent::class, 0],
            [RemovePolicyByRoleDraftEvent::class, 0],
        ]);
    }

    public function testAddPolicyByRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAddPolicyByRoleDraftEvent::class,
            AddPolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyCreateStruct::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('addPolicyByRoleDraft')->willReturn($updatedRoleDraft);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->addPolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeAddPolicyByRoleDraftEvent::class, 0],
            [AddPolicyByRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnAddPolicyByRoleDraftResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAddPolicyByRoleDraftEvent::class,
            AddPolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyCreateStruct::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $eventUpdatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('addPolicyByRoleDraft')->willReturn($updatedRoleDraft);

        $traceableEventDispatcher->addListener(BeforeAddPolicyByRoleDraftEvent::class, function (BeforeAddPolicyByRoleDraftEventInterface $event) use ($eventUpdatedRoleDraft) {
            $event->setUpdatedRoleDraft($eventUpdatedRoleDraft);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->addPolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeAddPolicyByRoleDraftEvent::class, 10],
            [BeforeAddPolicyByRoleDraftEvent::class, 0],
            [AddPolicyByRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testAddPolicyByRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeAddPolicyByRoleDraftEvent::class,
            AddPolicyByRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
            $this->createMock(PolicyCreateStruct::class),
        ];

        $updatedRoleDraft = $this->createMock(RoleDraft::class);
        $eventUpdatedRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('addPolicyByRoleDraft')->willReturn($updatedRoleDraft);

        $traceableEventDispatcher->addListener(BeforeAddPolicyByRoleDraftEvent::class, function (BeforeAddPolicyByRoleDraftEventInterface $event) use ($eventUpdatedRoleDraft) {
            $event->setUpdatedRoleDraft($eventUpdatedRoleDraft);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->addPolicyByRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeAddPolicyByRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [AddPolicyByRoleDraftEvent::class, 0],
            [BeforeAddPolicyByRoleDraftEvent::class, 0],
        ]);
    }

    public function testDeleteRoleEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteRoleEvent::class,
            DeleteRoleEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeleteRoleEvent::class, 0],
            [DeleteRoleEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testDeleteRoleStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteRoleEvent::class,
            DeleteRoleEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeDeleteRoleEvent::class, function (BeforeDeleteRoleEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteRole(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeleteRoleEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeDeleteRoleEvent::class, 0],
            [DeleteRoleEvent::class, 0],
        ]);
    }

    public function testDeleteRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteRoleDraftEvent::class,
            DeleteRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeleteRoleDraftEvent::class, 0],
            [DeleteRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testDeleteRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeDeleteRoleDraftEvent::class,
            DeleteRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(RoleDraft::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeDeleteRoleDraftEvent::class, function (BeforeDeleteRoleDraftEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->deleteRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeDeleteRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeDeleteRoleDraftEvent::class, 0],
            [DeleteRoleDraftEvent::class, 0],
        ]);
    }

    public function testRemoveRoleAssignmentEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemoveRoleAssignmentEvent::class,
            RemoveRoleAssignmentEvent::class
        );

        $parameters = [
            $this->createMock(RoleAssignment::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->removeRoleAssignment(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeRemoveRoleAssignmentEvent::class, 0],
            [RemoveRoleAssignmentEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testRemoveRoleAssignmentStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemoveRoleAssignmentEvent::class,
            RemoveRoleAssignmentEvent::class
        );

        $parameters = [
            $this->createMock(RoleAssignment::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeRemoveRoleAssignmentEvent::class, function (BeforeRemoveRoleAssignmentEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->removeRoleAssignment(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeRemoveRoleAssignmentEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeRemoveRoleAssignmentEvent::class, 0],
            [RemoveRoleAssignmentEvent::class, 0],
        ]);
    }

    public function testCreateRoleDraftEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateRoleDraftEvent::class,
            CreateRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
        ];

        $roleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('createRoleDraft')->willReturn($roleDraft);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($roleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateRoleDraftEvent::class, 0],
            [CreateRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnCreateRoleDraftResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateRoleDraftEvent::class,
            CreateRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
        ];

        $roleDraft = $this->createMock(RoleDraft::class);
        $eventRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('createRoleDraft')->willReturn($roleDraft);

        $traceableEventDispatcher->addListener(BeforeCreateRoleDraftEvent::class, function (BeforeCreateRoleDraftEventInterface $event) use ($eventRoleDraft) {
            $event->setRoleDraft($eventRoleDraft);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateRoleDraftEvent::class, 10],
            [BeforeCreateRoleDraftEvent::class, 0],
            [CreateRoleDraftEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testCreateRoleDraftStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateRoleDraftEvent::class,
            CreateRoleDraftEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
        ];

        $roleDraft = $this->createMock(RoleDraft::class);
        $eventRoleDraft = $this->createMock(RoleDraft::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('createRoleDraft')->willReturn($roleDraft);

        $traceableEventDispatcher->addListener(BeforeCreateRoleDraftEvent::class, function (BeforeCreateRoleDraftEventInterface $event) use ($eventRoleDraft) {
            $event->setRoleDraft($eventRoleDraft);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->createRoleDraft(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventRoleDraft, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateRoleDraftEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeCreateRoleDraftEvent::class, 0],
            [CreateRoleDraftEvent::class, 0],
        ]);
    }

    public function testUpdatePolicyEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdatePolicyEvent::class,
            UpdatePolicyEvent::class
        );

        $parameters = [
            $this->createMock(Policy::class),
            $this->createMock(PolicyUpdateStruct::class),
        ];

        $updatedPolicy = $this->createMock(Policy::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updatePolicy')->willReturn($updatedPolicy);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updatePolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($updatedPolicy, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdatePolicyEvent::class, 0],
            [UpdatePolicyEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnUpdatePolicyResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdatePolicyEvent::class,
            UpdatePolicyEvent::class
        );

        $parameters = [
            $this->createMock(Policy::class),
            $this->createMock(PolicyUpdateStruct::class),
        ];

        $updatedPolicy = $this->createMock(Policy::class);
        $eventUpdatedPolicy = $this->createMock(Policy::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updatePolicy')->willReturn($updatedPolicy);

        $traceableEventDispatcher->addListener(BeforeUpdatePolicyEvent::class, function (BeforeUpdatePolicyEventInterface $event) use ($eventUpdatedPolicy) {
            $event->setUpdatedPolicy($eventUpdatedPolicy);
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updatePolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUpdatedPolicy, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdatePolicyEvent::class, 10],
            [BeforeUpdatePolicyEvent::class, 0],
            [UpdatePolicyEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdatePolicyStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdatePolicyEvent::class,
            UpdatePolicyEvent::class
        );

        $parameters = [
            $this->createMock(Policy::class),
            $this->createMock(PolicyUpdateStruct::class),
        ];

        $updatedPolicy = $this->createMock(Policy::class);
        $eventUpdatedPolicy = $this->createMock(Policy::class);
        $innerServiceMock = $this->createMock(RoleServiceInterface::class);
        $innerServiceMock->method('updatePolicy')->willReturn($updatedPolicy);

        $traceableEventDispatcher->addListener(BeforeUpdatePolicyEvent::class, function (BeforeUpdatePolicyEventInterface $event) use ($eventUpdatedPolicy) {
            $event->setUpdatedPolicy($eventUpdatedPolicy);
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->updatePolicy(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUpdatedPolicy, $result);
        $this->assertSame($calledListeners, [
            [BeforeUpdatePolicyEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUpdatePolicyEvent::class, 0],
            [UpdatePolicyEvent::class, 0],
        ]);
    }

    public function testUnassignRoleFromUserGroupEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUnassignRoleFromUserGroupEvent::class,
            UnassignRoleFromUserGroupEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(UserGroup::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->unassignRoleFromUserGroup(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeUnassignRoleFromUserGroupEvent::class, 0],
            [UnassignRoleFromUserGroupEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUnassignRoleFromUserGroupStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUnassignRoleFromUserGroupEvent::class,
            UnassignRoleFromUserGroupEvent::class
        );

        $parameters = [
            $this->createMock(Role::class),
            $this->createMock(UserGroup::class),
        ];

        $innerServiceMock = $this->createMock(RoleServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeUnassignRoleFromUserGroupEvent::class, function (BeforeUnassignRoleFromUserGroupEventInterface $event) {
            $event->stopPropagation();
        }, 10);

        $service = new RoleService($innerServiceMock, $traceableEventDispatcher);
        $service->unassignRoleFromUserGroup(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeUnassignRoleFromUserGroupEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeUnassignRoleFromUserGroupEvent::class, 0],
            [UnassignRoleFromUserGroupEvent::class, 0],
        ]);
    }
}
