<?php

declare(strict_types=1);

namespace czechpmdevs\buildertools\editors;

use czechpmdevs\buildertools\async\object\FinishedSession;
use czechpmdevs\buildertools\EditorManager;
use czechpmdevs\buildertools\editors\object\BlockList;
use czechpmdevs\buildertools\schematics\SchematicData;
use czechpmdevs\buildertools\schematics\SerializedSchematic;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;

/**
 * Class SchematicEditor
 * @package czechpmdevs\buildertools\editors
 */
class SchematicEditor extends Editor {

    /**
     * @param string $path
     * @return SerializedSchematic
     */
    public function loadSchematic(string $path): SerializedSchematic {
        $error = "";
        $materials = "Classic";
        $nbt = new BigEndianNBTStream();


        /** @var CompoundTag $data */
        $data = $nbt->readCompressed(file_get_contents($path));
        $width = (int)$data->getShort("Width");
        $height = (int)$data->getShort("Height");
        $length = (int)$data->getShort("Length");

        if($data->offsetExists("Materials")) {
            $materials = $result["materials"] = $data->getString("Materials");
        }

        $blockList = new BlockList();

        if($data->offsetExists("Blocks") && $data->offsetExists("Data")) {
            $blocks = $data->getByteArray("Blocks");
            $data = $data->getByteArray("Data");

            $i = 0;
            for($y = 0; $y < $height; $y++) {
                for ($z = 0; $z < $length; $z++) {
                    for($x = 0; $x < $width; $x++) {
                        $id = ord($blocks{$i});
                        $damage = ord($data{$i});
                        if($damage >= 16) $damage = 0; // prevents bug
                        $blockList->addBlock(new Vector3($x, $y, $z), Block::get($id, $damage));
                        $i++;
                    }
                }
            }
        }
        // WORLDEDIT BY SK89Q and Sponge schematics
        else {
            $error = "Could not load schematic {$path}: BuilderTools supports only MCEdit schematic format.";
        }

        if($materials == "Classic" || $materials == "Alpha") {
            $materials = "Pocket";

            /** @var Fixer $fixer */
            $fixer = EditorManager::getEditor(Editor::FIXER);
            $blockList = $fixer->fixBlockList($blockList);
        }

        return new SerializedSchematic($path, $blockList, new Vector3($width, $height, $length), $materials, $error);

    }

    /**
     * @return string
     */
    public function getName(): string {
        return "SchematicEditor";
    }
}