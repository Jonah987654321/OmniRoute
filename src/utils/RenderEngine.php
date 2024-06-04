<?php

namespace OmniRoute\utils;

use OmniRoute\Page;
require_once __DIR__."/../Page.php";

class RenderEngine {
    private Page $page;

    public function __construct(Page $page) {
        $this->page = $page;
    }

    public function render() {
        ob_start();
        $this->__generateRenderOutput();
        $content = ob_get_clean();
        echo $this->formatHtml($content);
    }

    private function __generateRenderOutput() {
        ?>
        <!doctype html>
        <html>
            <head>
                <title><?php echo $this->page->getTitle()?></title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta charset="utf-8">
                <?php
                $this->insertCSS();
                ?>
            </head>
            <body>
                <?php
                foreach ($this->page->getRenderQueue() as $ro) {
                    $ro->render();
                }
                ?>
            </body>
        </html>
    <?php
    }

    private function insertCSS() {
        foreach ($this->page->getStylesheets() as $s) {
            echo "<style>\n";
            require_once $this->page->getSiteDir()."/assets/css/$s";
            echo "\n</style>";
        }
    }

    private function formatHtml($html) {
        $html = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $html);
        $html = $this->indentHtml($html);
        return $html;
    }
    
    private function indentHtml($html) {
        $result = '';
        $level = 0;
        $tokens = preg_split('/(<[^>]+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        
        foreach ($tokens as $token) {
            if (preg_match('/^<\/\w/', $token)) {
                $level--;
            }
    
            $result .= str_repeat("    ", $level) . $token . "\n";
    
            if (preg_match('/^<\w[^>]*[^\/]>$/', $token)) {
                $level++;
            }
        }
        
        return $result;
    }
}

?>