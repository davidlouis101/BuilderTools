<?php

declare(strict_types=1);

namespace czechpmdevs\buildertools\event\listener;

use czechpmdevs\buildertools\async\object\FinishedSession;
use czechpmdevs\buildertools\async\object\Session;
use czechpmdevs\buildertools\BuilderTools;
use czechpmdevs\buildertools\schematics\SerializedSchematic;
use pocketmine\scheduler\Task;

/**
 * Class ThreadListener
 * @package czechpmdevs\buildertools\event\listener
 */
class ThreadListener extends Task {

    /** @var BuilderTools $plugin */
    public $plugin;

    /**
     * ThreadListener constructor.
     * @param BuilderTools $plugin
     */
    public function __construct(BuilderTools $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        /** @var FinishedSession $session */
        foreach (BuilderTools::getAsyncWorker()->finished as $index => $session) {
            switch ($session["id"]) {
                case Session::SESSION_SCHEMATIC_LOAD:
                    /** @var SerializedSchematic $serializedSchematic */
                    $serializedSchematic = $session->result;
                    $schem = BuilderTools::getSchematicsManager()->getSchematic(basename($serializedSchematic->file, ".schematic"));
                    $schem->loadFromSerialized($serializedSchematic);
                    break;
            }

            unset(BuilderTools::getAsyncWorker()->finished[$index]);
        }
    }
}