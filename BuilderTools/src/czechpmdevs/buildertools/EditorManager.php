<?php

declare(strict_types=1);

namespace czechpmdevs\buildertools;

use czechpmdevs\buildertools\editors\BiomeEditor;
use czechpmdevs\buildertools\editors\Canceller;
use czechpmdevs\buildertools\editors\Copier;
use czechpmdevs\buildertools\editors\Decorator;
use czechpmdevs\buildertools\editors\Editor;
use czechpmdevs\buildertools\editors\Filler;
use czechpmdevs\buildertools\editors\Fixer;
use czechpmdevs\buildertools\editors\Naturalizer;
use czechpmdevs\buildertools\editors\Printer;
use czechpmdevs\buildertools\editors\Replacement;
use czechpmdevs\buildertools\editors\SchematicEditor;

/**
 * Class EditorManager
 * @package czechpmdevs\buildertools
 */
class EditorManager {

    /** @var Editor[] $editors */
    protected static $editors = [];

    public static function init() {
        self::$editors["Filler"] = new Filler;
        self::$editors["Printer"] = new Printer;
        self::$editors["Replacement"] = new Replacement;
        self::$editors["Naturalizer"] = new Naturalizer;
        self::$editors["Copier"] = new Copier;
        self::$editors["Canceller"] = new Canceller;
        self::$editors["Decorator"] = new Decorator;
        self::$editors["Fixer"] = new Fixer;
        self::$editors["BiomeEditor"] = new BiomeEditor;
        self::$editors["SchematicEditor"] = new SchematicEditor;
    }

    /**
     * @param string $name
     * @return Editor
     */
    public static function getEditor(string $name) {
        return self::$editors[$name];
    }
}