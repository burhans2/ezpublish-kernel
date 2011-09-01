<?php
/**
 * File containing the content updater remove field action class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\Persistence\Storage\Legacy\Content\Type\ContentUpdater\Action;
use ezp\Persistence\Storage\Legacy\Content;

/**
 * Action to remove a field from content objects
 */
class RemoveField extends Content\Type\ContentUpdater\Action
{
    /**
     * Applies the action to the given $content
     *
     * @param Content $content
     * @return void
     */
    public function apply( Content $content )
    {
        throw new \RuntimeException( 'Not implemented, yet.' );
    }
}
