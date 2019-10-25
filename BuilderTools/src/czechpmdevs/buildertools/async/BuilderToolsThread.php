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

namespace czechpmdevs\buildertools\async;

use Composer\Autoload\ClassLoader;
use czechpmdevs\buildertools\async\object\FinishedSession;
use czechpmdevs\buildertools\async\object\Session;
use czechpmdevs\buildertools\EditorManager;
use czechpmdevs\buildertools\editors\Editor;
use czechpmdevs\buildertools\editors\SchematicEditor;
use pocketmine\Thread;

/**
 * Class BuilderToolsThread
 * @package czechpmdevs\buildertools\async
 */
class BuilderToolsThread extends Thread {

    /** @var array|\Threaded $sessions */
    public $sessions;

    /** @var array|\Threaded $finished */
    public $finished;


    /**
     * BuilderToolsThread constructor.
     */
    public function __construct() {
        $this->sessions = new \Threaded;
        $this->finished = new \Threaded;
    }


    public function run() {
        $this->getClassLoader()->register(true);

        EditorManager::init();
        while ($this->isKilled !== true) {
            $this->checkSessions();
        }
    }

    public function checkSessions() {
        if(!is_null($session = $this->sessions->shift())) {
            $data = Session::unserialize($session);
            $this->processSession($data[0], $data[1]);
        }
    }

    /**
     * @param int $id
     * @param string $data
     */
    public function processSession(int $id, string $data) {
        switch ($id) {
            case Session::SESSION_SCHEMATIC_LOAD:
                /** @var SchematicEditor $schematicEditor */
                $schematicEditor = $this->getEditor(Editor::SCHEMATIC_EDITOR);
                $schem = $schematicEditor->loadSchematic($data);

                $this->finished[] = new FinishedSession($id, $schem);
                break;
        }
    }

    /**
     * @param string $editor
     * @return \czechpmdevs\buildertools\editors\Editor
     */
    public function getEditor(string $editor) {
        return EditorManager::getEditor($editor);
    }

    public function setGarbage() {

    }
}