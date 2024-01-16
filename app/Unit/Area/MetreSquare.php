<?php

declare(strict_types = 1);

/**
 * This file is part of the jordanbrauer/unit-converter PHP package.
 *
 * @copyright 2018 Jordan Brauer <18744334+jordanbrauer@users.noreply.github.com>
 * @license MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Unit\Area;

use App\Unit\Area\AreaUnit;

/**
 * Square metre data class. All area units will use this
 * class as the $base property.
 *
 * @version 2.0.0
 * @since 0.0.1
 * @author Jordan Brauer <18744334+jordanbrauer@users.noreply.github.com>
 */
class MetreSquare extends AreaUnit
{
    protected function configure(): void
    {
        $this
            ->setName("metre square")

            ->setSymbol("sq. m")

            ->setScientificSymbol("m²")

            ->setUnits(1);
    }
}
