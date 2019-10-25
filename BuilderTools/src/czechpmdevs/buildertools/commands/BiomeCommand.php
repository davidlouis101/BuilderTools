<?php

declare(strict_types=1);

namespace czechpmdevs\buildertools\commands;

use czechpmdevs\buildertools\biome\BiomeIds;
use czechpmdevs\buildertools\BuilderTools;
use czechpmdevs\buildertools\EditorException;
use czechpmdevs\buildertools\editors\BiomeEditor;
use czechpmdevs\buildertools\editors\Editor;
use czechpmdevs\buildertools\Selectors;
use czechpmdevs\buildertools\utils\Math;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 *
 * Class BlockInfoCommand
 * @package czechpmdevs\buildertools\commands
 */
class BiomeCommand extends BuilderToolsCommand {

    /**
     * BiomeCommand constructor.
     */
    public function __construct() {
        parent::__construct("/biome", "Switch block info mode", null, ["/bi", "/debug"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) return;
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in game!");
            return;
        }

        if(!isset($args[0])) {
            $sender->sendMessage("§cUsage: §7//biome <biomeId|list>");
            return;
        }

        if($args[0] == "list") {
            try {
                $biomes = array_keys((new \ReflectionClass(BiomeIds::class))->getConstants());
                foreach ($biomes as $index => $name) {
                    $biomes[$index] = ucfirst(strtolower($name));
                }

                $sender->sendMessage(BuilderTools::getPrefix() . "§aAvailable biomes (" . (string)count($biomes) . "): " . implode(", ", $biomes));
                return;
            }
            catch (\ReflectionException $e) {
                $sender->sendMessage(BuilderTools::getPrefix() . "§cYou need reflection extension to get list of biomes.");
                return;
            }
        }

        $args[0] = strtoupper($args[0]);

        if(!Selectors::isSelected(1, $sender)) {
            $sender->sendMessage(BuilderTools::getPrefix()."§cFirst you need to select the first position.");
            return;
        }

        if(!Selectors::isSelected(2, $sender)) {
            $sender->sendMessage(BuilderTools::getPrefix()."§cFirst you need to select the second position.");
            return;
        }

        try {
            /** @var BiomeEditor $biomeManager */
            $biomeManager = BuilderTools::getEditor(Editor::BIOME_EDITOR);
            $changed = $biomeManager->setBiomeColor($sender->getLevel(), Math::getXZVector(Selectors::getPosition($sender, 1)), Math::getXZVector(Selectors::getPosition($sender, 2)), $args[0]);
            $sender->sendMessage(BuilderTools::getPrefix() . "§aBiome color successfully changed to {$args[0]} ($changed chunks affected)!");
        }
        catch (EditorException $e) {
            $sender->sendMessage(BuilderTools::getPrefix() . "§cBiome wasn't found.");
        }
    }
}