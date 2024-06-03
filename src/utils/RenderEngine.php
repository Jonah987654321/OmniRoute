<?php

namespace TemplaPHP\utils;

use TemplaPHP\Page;
require_once __DIR__."/../Page.php";

class RenderEngine {
    private Page $page;

    public function __construct(Page $page) {
        $this->page = $page;
    }

    public function render() {
        echo '
        <html>
            <head>
                <title>'.$this->page->getTitle().'</title>
            </head>
        </html>
        ';
    }
}

?>