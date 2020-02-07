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
use czechpmdevs\buildertools\Selectors;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

/**
 * Class FirstPositionCommand
 * @package buildertools\commands
 */
class FirstPositionCommand extends BuilderToolsCommand {

    /**
     * FirstPositionCommand constructor.
     */
    public function __construct() {
        parent::__construct("/pos1", "Erste Pos Gesetz", null, ["/1"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        parent::execute($sender, $commandLabel, $args);

        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in game!");
            return;
        }
        Selectors::addSelector($sender, 1, $position = new Position((int)round($sender->getX()), (int)round($sender->getY()), (int)round($sender->getZ()), $sender->getLevel()));
        $sender->sendMessage(BuilderTools::getPrefix()."§aAusgewählte erste Stelle bei {$position->getX()}, {$position->getY()}, {$position->getZ()}");
    }
}
