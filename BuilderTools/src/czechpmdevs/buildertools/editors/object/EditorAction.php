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

namespace czechpmdevs\buildertools\editors\object;

/**
 * Class EditorAction
 * @package czechpmdevs\buildertools\editors\object
 */
class EditorAction {

    public const ACTION_LOAD_SCHEMATIC = 0;

    /** @var $actionId */
    public $actionId = -1;

    /** @var string $data */
    public $data;

    /**
     * EditorAction constructor.
     * @param int $id
     * @param $data
     */
    public function __construct(int $id, $data) {
        $this->actionId = $id;
        $this->data = serialize($data);
    }
}