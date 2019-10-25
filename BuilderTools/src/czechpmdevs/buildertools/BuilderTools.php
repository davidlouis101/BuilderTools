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

namespace czechpmdevs\buildertools;

use czechpmdevs\buildertools\async\BuilderToolsThread;
use czechpmdevs\buildertools\commands\BiomeCommand;
use czechpmdevs\buildertools\commands\BlockInfoCommand;
use czechpmdevs\buildertools\commands\ClearInventoryCommand;
use czechpmdevs\buildertools\commands\CopyCommand;
use czechpmdevs\buildertools\commands\CubeCommand;
use czechpmdevs\buildertools\commands\CylinderCommand;
use czechpmdevs\buildertools\commands\DrawCommand;
use czechpmdevs\buildertools\commands\FillCommand;
use czechpmdevs\buildertools\commands\FirstPositionCommand;
use czechpmdevs\buildertools\commands\FixCommand;
use czechpmdevs\buildertools\commands\FlipCommand;
use czechpmdevs\buildertools\commands\HelpCommand;
use czechpmdevs\buildertools\commands\HollowCubeCommand;
use czechpmdevs\buildertools\commands\HollowCylinderCommand;
use czechpmdevs\buildertools\commands\HollowPyramidCommand;
use czechpmdevs\buildertools\commands\HollowSphereCommand;
use czechpmdevs\buildertools\commands\IdCommand;
use czechpmdevs\buildertools\commands\MergeCommand;
use czechpmdevs\buildertools\commands\MoveCommand;
use czechpmdevs\buildertools\commands\NaturalizeCommand;
use czechpmdevs\buildertools\commands\OutlineCommand;
use czechpmdevs\buildertools\commands\PasteCommand;
use czechpmdevs\buildertools\commands\PyramidCommand;
use czechpmdevs\buildertools\commands\RedoCommand;
use czechpmdevs\buildertools\commands\ReplaceCommand;
use czechpmdevs\buildertools\commands\RotateCommand;
use czechpmdevs\buildertools\commands\SchematicCommand;
use czechpmdevs\buildertools\commands\SecondPositionCommand;
use czechpmdevs\buildertools\commands\SphereCommand;
use czechpmdevs\buildertools\commands\StackCommand;
use czechpmdevs\buildertools\commands\TreeCommand;
use czechpmdevs\buildertools\commands\UndoCommand;
use czechpmdevs\buildertools\commands\WandCommand;
use czechpmdevs\buildertools\editors\Editor;
use czechpmdevs\buildertools\event\listener\EventListener;
use czechpmdevs\buildertools\event\listener\ThreadListener;
use czechpmdevs\buildertools\schematics\SchematicsManager;
use pocketmine\command\Command;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\plugin\PluginBase;
use pocketmine\ThreadManager;

/**
 * Class BuilderTools
 * @package buildertools
 */
class BuilderTools extends PluginBase {

    /** @var  BuilderTools $instance */
    private static $instance;

    /** @var  string $prefix */
    private static $prefix;

    /** @var EventListener $listener */
    private static $listener;

    /** @var SchematicsManager $schematicManager */
    private static $schematicsManager;

    /** @var Command[] $commands */
    private static $commands = [];

    /** @var array $config */
    private static $configuration = [];

    /** @var BuilderToolsThread $customThread */
    private static $customThread;

    public function onEnable() {
        self::$instance = $this;
        self::$prefix = "§7[BuilderTools] §a";
        $this->initConfig();
        $this->startCustomThread();
        $this->registerCommands();
        $this->initListner();
        EditorManager::init();
        $this->registerEnchantment();
        self::$schematicsManager = new SchematicsManager($this);
    }

    private function initConfig() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if($this->getConfig()->get("config-version") != "1.2.0-beta3") {
            $this->getLogger()->notice("BuilderTools config is outdated. Updating configuration file...");
            rename($this->getDataFolder() . "/config.yml" , $this->getDataFolder() . "/config.yml.old");
            $this->saveResource("/config.yml");
            $this->getConfig()->reload();
            $this->getLogger()->info("Config successfully updated! (Old config name changed to 'config.yml.old')");
        }
        self::$configuration = $this->getConfig()->getAll();
    }


    private function initListner() {
        $this->getServer()->getPluginManager()->registerEvents(self::$listener = new EventListener, $this);
    }

    private function registerEnchantment() {
        Enchantment::registerEnchantment(new Enchantment(50, "BuilderTools", Enchantment::RARITY_COMMON, 0, 0, 1));
    }

    private function registerCommands() {
        $map = $this->getServer()->getCommandMap();
        self::$commands = [
            new FirstPositionCommand,
            new SecondPositionCommand,
            new WandCommand,
            new FillCommand,
            new HelpCommand,
            new DrawCommand,
            new SphereCommand,
            new HollowSphereCommand,
            new ReplaceCommand,
            new IdCommand,
            new CubeCommand,
            new HollowCubeCommand,
            new CopyCommand,
            new PasteCommand,
            new MergeCommand,
            new RotateCommand,
            new FlipCommand,
            new UndoCommand,
            new RedoCommand,
            new TreeCommand,
            new FixCommand,
            new BlockInfoCommand,
            new ClearInventoryCommand,
            new NaturalizeCommand,
            new SchematicCommand,
            new PyramidCommand,
            new HollowPyramidCommand,
            new CylinderCommand,
            new HollowCylinderCommand,
            new StackCommand,
            new OutlineCommand,
            new MoveCommand,
            new BiomeCommand
        ];
        foreach (self::$commands as $command) {
            $map->register("BuilderTools", $command);
        }
        HelpCommand::buildPages();
    }

    public function startCustomThread() {
        if(self::getConfiguration()["custom-thread"]) {
            self::$customThread = new BuilderToolsThread();
            ThreadManager::getInstance()->add(self::$customThread);
            self::$customThread->start(PTHREADS_INHERIT_NONE);
            $this->getScheduler()->scheduleRepeatingTask(new ThreadListener($this), self::getConfiguration()["thread-reader-ticks"]);
        }
    }

    /**
     * @param string $name
     * @return Editor $editor
     */
    public static function getEditor(string $name): Editor {
        return EditorManager::getEditor($name);
    }

    /**
     * @return Command[] $commands
     */
    public static function getAllCommands(): array {
        return self::$commands;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix(): string {
        return self::$prefix;
    }

    /**
     * @return array
     */
    public static function getConfiguration(): array {
        return self::$configuration;
    }

    /**
     * @return EventListener $listener
     */
    public static function getListener(): EventListener {
        return self::$listener;
    }

    /**
     * @return SchematicsManager $schematicsManager
     */
    public static function getSchematicsManager(): SchematicsManager {
        return self::$schematicsManager;
    }

    /**
     * @return BuilderToolsThread|null $customThread
     */
    public static function getAsyncWorker(): ?BuilderToolsThread {
        return self::$customThread;
    }

    /**
     * @return BuilderTools $instance
     */
    public static function getInstance(): BuilderTools {
        return self::$instance;
    }
}
