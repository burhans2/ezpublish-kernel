<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event;

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
use eZ\Publish\SPI\Repository\Decorator\RoleServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RoleService extends RoleServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        RoleServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createRole(RoleCreateStruct $roleCreateStruct): RoleDraft
    {
        $eventData = [$roleCreateStruct];

        $beforeEvent = new BeforeCreateRoleEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getRoleDraft();
        }

        $roleDraft = $beforeEvent->hasRoleDraft()
            ? $beforeEvent->getRoleDraft()
            : $this->innerService->createRole($roleCreateStruct);

        $this->eventDispatcher->dispatch(new CreateRoleEvent($roleDraft, ...$eventData));

        return $roleDraft;
    }

    public function createRoleDraft(Role $role)
    {
        $eventData = [$role];

        $beforeEvent = new BeforeCreateRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getRoleDraft();
        }

        $roleDraft = $beforeEvent->hasRoleDraft()
            ? $beforeEvent->getRoleDraft()
            : $this->innerService->createRoleDraft($role);

        $this->eventDispatcher->dispatch(new CreateRoleDraftEvent($roleDraft, ...$eventData));

        return $roleDraft;
    }

    public function updateRoleDraft(
        RoleDraft $roleDraft,
        RoleUpdateStruct $roleUpdateStruct
    ) {
        $eventData = [
            $roleDraft,
            $roleUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRoleDraft();
        }

        $updatedRoleDraft = $beforeEvent->hasUpdatedRoleDraft()
            ? $beforeEvent->getUpdatedRoleDraft()
            : $this->innerService->updateRoleDraft($roleDraft, $roleUpdateStruct);

        $this->eventDispatcher->dispatch(new UpdateRoleDraftEvent($updatedRoleDraft, ...$eventData));

        return $updatedRoleDraft;
    }

    public function addPolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyCreateStruct $policyCreateStruct
    ) {
        $eventData = [
            $roleDraft,
            $policyCreateStruct,
        ];

        $beforeEvent = new BeforeAddPolicyByRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRoleDraft();
        }

        $updatedRoleDraft = $beforeEvent->hasUpdatedRoleDraft()
            ? $beforeEvent->getUpdatedRoleDraft()
            : $this->innerService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);

        $this->eventDispatcher->dispatch(new AddPolicyByRoleDraftEvent($updatedRoleDraft, ...$eventData));

        return $updatedRoleDraft;
    }

    public function removePolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyDraft $policyDraft
    ) {
        $eventData = [
            $roleDraft,
            $policyDraft,
        ];

        $beforeEvent = new BeforeRemovePolicyByRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRoleDraft();
        }

        $updatedRoleDraft = $beforeEvent->hasUpdatedRoleDraft()
            ? $beforeEvent->getUpdatedRoleDraft()
            : $this->innerService->removePolicyByRoleDraft($roleDraft, $policyDraft);

        $this->eventDispatcher->dispatch(new RemovePolicyByRoleDraftEvent($updatedRoleDraft, ...$eventData));

        return $updatedRoleDraft;
    }

    public function updatePolicyByRoleDraft(
        RoleDraft $roleDraft,
        PolicyDraft $policy,
        PolicyUpdateStruct $policyUpdateStruct
    ) {
        $eventData = [
            $roleDraft,
            $policy,
            $policyUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdatePolicyByRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedPolicyDraft();
        }

        $updatedPolicyDraft = $beforeEvent->hasUpdatedPolicyDraft()
            ? $beforeEvent->getUpdatedPolicyDraft()
            : $this->innerService->updatePolicyByRoleDraft($roleDraft, $policy, $policyUpdateStruct);

        $this->eventDispatcher->dispatch(new UpdatePolicyByRoleDraftEvent($updatedPolicyDraft, ...$eventData));

        return $updatedPolicyDraft;
    }

    public function deleteRoleDraft(RoleDraft $roleDraft): void
    {
        $eventData = [$roleDraft];

        $beforeEvent = new BeforeDeleteRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteRoleDraft($roleDraft);

        $this->eventDispatcher->dispatch(new DeleteRoleDraftEvent(...$eventData));
    }

    public function publishRoleDraft(RoleDraft $roleDraft): void
    {
        $eventData = [$roleDraft];

        $beforeEvent = new BeforePublishRoleDraftEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->publishRoleDraft($roleDraft);

        $this->eventDispatcher->dispatch(new PublishRoleDraftEvent(...$eventData));
    }

    public function updateRole(
        Role $role,
        RoleUpdateStruct $roleUpdateStruct
    ) {
        $eventData = [
            $role,
            $roleUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateRoleEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRole();
        }

        $updatedRole = $beforeEvent->hasUpdatedRole()
            ? $beforeEvent->getUpdatedRole()
            : $this->innerService->updateRole($role, $roleUpdateStruct);

        $this->eventDispatcher->dispatch(new UpdateRoleEvent($updatedRole, ...$eventData));

        return $updatedRole;
    }

    public function addPolicy(
        Role $role,
        PolicyCreateStruct $policyCreateStruct
    ) {
        $eventData = [
            $role,
            $policyCreateStruct,
        ];

        $beforeEvent = new BeforeAddPolicyEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedRole();
        }

        $updatedRole = $beforeEvent->hasUpdatedRole()
            ? $beforeEvent->getUpdatedRole()
            : $this->innerService->addPolicy($role, $policyCreateStruct);

        $this->eventDispatcher->dispatch(new AddPolicyEvent($updatedRole, ...$eventData));

        return $updatedRole;
    }

    public function deletePolicy(Policy $policy): void
    {
        $eventData = [$policy];

        $beforeEvent = new BeforeDeletePolicyEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->deletePolicy($policy);

        $this->eventDispatcher->dispatch(new DeletePolicyEvent(...$eventData));
    }

    public function updatePolicy(
        Policy $policy,
        PolicyUpdateStruct $policyUpdateStruct
    ) {
        $eventData = [
            $policy,
            $policyUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdatePolicyEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return $beforeEvent->getUpdatedPolicy();
        }

        $updatedPolicy = $beforeEvent->hasUpdatedPolicy()
            ? $beforeEvent->getUpdatedPolicy()
            : $this->innerService->updatePolicy($policy, $policyUpdateStruct);

        $this->eventDispatcher->dispatch(new UpdatePolicyEvent($updatedPolicy, ...$eventData));

        return $updatedPolicy;
    }

    public function deleteRole(Role $role): void
    {
        $eventData = [$role];

        $beforeEvent = new BeforeDeleteRoleEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteRole($role);

        $this->eventDispatcher->dispatch(new DeleteRoleEvent(...$eventData));
    }

    public function assignRoleToUserGroup(
        Role $role,
        UserGroup $userGroup,
        RoleLimitation $roleLimitation = null
    ): void {
        $eventData = [
            $role,
            $userGroup,
            $roleLimitation,
        ];

        $beforeEvent = new BeforeAssignRoleToUserGroupEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignRoleToUserGroup($role, $userGroup, $roleLimitation);

        $this->eventDispatcher->dispatch(new AssignRoleToUserGroupEvent(...$eventData));
    }

    public function unassignRoleFromUserGroup(
        Role $role,
        UserGroup $userGroup
    ): void {
        $eventData = [
            $role,
            $userGroup,
        ];

        $beforeEvent = new BeforeUnassignRoleFromUserGroupEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->unassignRoleFromUserGroup($role, $userGroup);

        $this->eventDispatcher->dispatch(new UnassignRoleFromUserGroupEvent(...$eventData));
    }

    public function assignRoleToUser(
        Role $role,
        User $user,
        RoleLimitation $roleLimitation = null
    ): void {
        $eventData = [
            $role,
            $user,
            $roleLimitation,
        ];

        $beforeEvent = new BeforeAssignRoleToUserEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignRoleToUser($role, $user, $roleLimitation);

        $this->eventDispatcher->dispatch(new AssignRoleToUserEvent(...$eventData));
    }

    public function unassignRoleFromUser(
        Role $role,
        User $user
    ): void {
        $eventData = [
            $role,
            $user,
        ];

        $beforeEvent = new BeforeUnassignRoleFromUserEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->unassignRoleFromUser($role, $user);

        $this->eventDispatcher->dispatch(new UnassignRoleFromUserEvent(...$eventData));
    }

    public function removeRoleAssignment(RoleAssignment $roleAssignment): void
    {
        $eventData = [$roleAssignment];

        $beforeEvent = new BeforeRemoveRoleAssignmentEvent(...$eventData);
        if ($this->eventDispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            return;
        }

        $this->innerService->removeRoleAssignment($roleAssignment);

        $this->eventDispatcher->dispatch(new RemoveRoleAssignmentEvent(...$eventData));
    }
}
