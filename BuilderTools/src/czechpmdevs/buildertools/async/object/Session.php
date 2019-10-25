<?php

declare(strict_types=1);

namespace czechpmdevs\buildertools\async\object;

/**
 * Class Session
 * @package czechpmdevs\buildertools\async\object
 */
class Session {

    public const SESSION_SCHEMATIC_LOAD = 1;

    /**
     * @param int $id
     * @param string $string
     * @return string
     */
    public static function serialize(int $id, string $string): string {
        return serialize([$id, $string]);
    }

    /**
     * @param string $data
     * @return array
     */
    public static function unserialize(string $data): array {
        return unserialize($data);
    }
}