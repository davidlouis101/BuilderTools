<?php

declare(strict_types=1);

namespace czechpmdevs\buildertools\async\object;

/**
 * Class FinishedSession
 * @package czechpmdevs\buildertools\async\object
 */
class FinishedSession extends \Threaded {

    /** @var int $id */
    public $id = -1;

    /** @var string|\Threaded $result */
    public $result;

    public function __construct(int $id, $result) {
        $this->id = $id;
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult() {
        return unserialize($this->result);
    }

    public function setGarbage() {}
}