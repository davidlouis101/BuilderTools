<?php

/**
 * Copyright (C) 2018-2020  CzechPMDevs
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

namespace czechpmdevs\buildertools\commands;

use czechpmdevs\buildertools\BuilderTools;
use czechpmdevs\buildertools\Clipboard;
use czechpmdevs\buildertools\editors\object\EditorStep;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class UndoCommand
 * @package buildertools\commands
 */
class UndoCommand extends BuilderToolsCommand {

    /**
     * UndoCommand constructor.
     */
    public function __construct() {
        parent::__construct("/undo", "Loscht Die Letzen BuilderTool", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        parent::execute($sender, $commandLabel, $args);

        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in game!");
            return;
        }

        $clipboard = Clipboard::getClipboard($sender);
        $step = $clipboard->getLastStep();

        if(!$step instanceof EditorStep) {
            $sender->sendMessage(BuilderTools::getPrefix() . "§cThere aren't any actions to undo.");
            return;
        }

        if($step->useOn($sender)) $sender->sendMessage(BuilderTools::getPrefix()."§aEs Wurde Zuruckgesetz!");
    }

}
