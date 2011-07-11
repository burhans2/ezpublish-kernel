<?php
/**
 * File containing the ezp\content\Criteria\Operator class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\Persistence\Content\Criterion;

/**
 * Operators struct
 *
 * Note that the method is abstract as there is no point in instanciating it
 */
abstract class Operator
{
    const EQ = "=";
    const GT = ">";
    const GTE = ">=";
    const LT = "<";
    const LTE = "<=";
    const IN = "in";
    const BETWEEN = "between";
    const LIKE = "like";
}
?>
