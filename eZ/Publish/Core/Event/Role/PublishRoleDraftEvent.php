<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event\Role;

use eZ\Publish\API\Repository\Events\Role\PublishRoleDraftEvent as PublishRoleDraftEventInterface;
use eZ\Publish\API\Repository\Values\User\RoleDraft;
use Symfony\Contracts\EventDispatcher\Event;

final class PublishRoleDraftEvent extends Event implements PublishRoleDraftEventInterface
{
    /** @var \eZ\Publish\API\Repository\Values\User\RoleDraft */
    private $roleDraft;

    public function __construct(
        RoleDraft $roleDraft
    ) {
        $this->roleDraft = $roleDraft;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }
}
