<?php

namespace OmniRoute\utils\RenderObjects;

abstract class RenderObject {
    private int $queuePos;

    abstract public function render();
    
    public function __construct(int $queuePos) {
        $this->queuePos = $queuePos;
    }

    public function getQueuePos(): int {
        return $this->queuePos;
    }
}

class HTMLRender extends RenderObject {
    private string $html;

    public function __construct($queuePos, $html) {
        $this->html = $html;
        parent::__construct($queuePos);
    }

    public function render() {
        echo $this->html;
    }
}

?>