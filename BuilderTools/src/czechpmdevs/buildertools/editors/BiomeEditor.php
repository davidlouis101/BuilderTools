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

namespace czechpmdevs\buildertools\editors;

use czechpmdevs\buildertools\biome\BiomeIds;
use czechpmdevs\buildertools\EditorException;
use pocketmine\level\Level;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\Player;

/**
 * Class BiomeEditor
 * @package czechpmdevs\buildertools\editors
 */
class BiomeEditor extends Editor implements BiomeIds {

    /**
     * @return string
     */
    public function getName(): string {
        return "BiomeEditor";
    }

    /**
     * @param Level $level
     * @param Vector2 $pos1
     * @param Vector2 $pos2
     * @param string $biome
     *
     * @return int|void
     */
    public function setBiomeColor(Level $level, Vector2 $pos1, Vector2 $pos2, string $biome) {
        $constants = [];
        try {
            $constants = (new \ReflectionClass(BiomeIds::class))->getConstants();
            if(!isset($constants[strtoupper($biome)])) {
                throw new EditorException("Could not change biome colors, biome was not found");
            }
            $biome = $constants[$biome];
        }
        catch (\ReflectionException $exception) {
            $this->getPlugin()->getLogger()->error("Could not change biomes color. For this feature you need Reflection extension.");
            return;
        }

        


        $chunks = [];
        for($x = min($pos1->getX(), $pos2->getX()); $x < max($pos1->getX(), $pos2->getX()); $x++) {
            for($z = min($pos1->getZ(), $pos2->getZ()); $z < max($pos1->getZ(), $pos2->getZ()); $z++) {
                $chunk = $level->getChunk($x >> 4, $z >> 4);
                $chunk->setBiomeId($x & 0x0f, $z & 0x0f, $biome);
                if(!isset($chunks[spl_object_hash($chunk)])) {
                    $chunks[spl_object_hash($chunk)] = $chunk;
                }
            }
        }

        foreach ($chunks as $chunk) {
            foreach ($level->getChunkLoaders($chunk->getX(), $chunk->getZ()) as $chunkLoader) {
                if($chunkLoader instanceof Player) {
                    if(class_exists(FullChunkDataPacket::class)) {
                        $pk = new FullChunkDataPacket();
                        $pk->chunkX = $chunk->getX();
                        $pk->chunkZ = $chunk->getZ();
                        $pk->data = $chunk->networkSerialize();
                    }
                    else {
                        $pk = LevelChunkPacket::withoutCache($chunk->getX(), $chunk->getZ(), $chunk->getSubChunkSendCount(), $chunk->networkSerialize());
                    }
                    $chunkLoader->dataPacket($pk);
                }
            }
        }

        return count($chunks);
    }
}