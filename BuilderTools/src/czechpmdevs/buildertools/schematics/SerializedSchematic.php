<?php

/**
 * Copyright (C) 2018-2019  CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace czechpmdevs\buildertools\schematics;

use czechpmdevs\buildertools\editors\object\BlockList;
use pocketmine\math\Vector3;

/**
 * Class SerializedSchematic
 * @package czechpmdevs\buildertools\schematics
 */
class SerializedSchematic extends \Threaded {

    /** @var string $file */
    public $file;

    /** @var string $list */
    public $blockList;

    /** @var string $axis */
    public $axis;

    /** @var string $materials */
    public $materials;

    /** @var string $error */
    public $error = "";

    /**
     * SerializedSchematic constructor.
     * @param string $file
     * @param BlockList $blockList
     * @param Vector3 $axis
     * @param string $materials
     * @param string $error
     */
    public function __construct(string $file, BlockList $blockList, Vector3 $axis, $materials = "Pocket", $error = "") {
        $this->file = $file;
        $this->blockList = $blockList->serializeList();
        $this->axis = serialize([$axis->getX(), $axis->getY(), $axis->getZ()]);
        $this->materials = $materials;
    }


    public function setGarbage() {}
}