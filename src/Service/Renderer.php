<?php

namespace src\Service;

class Renderer
{
    public static function render(array $variables = []): void
    {
        extract($variables);
        ob_start();
        require('./src/Views/index.html.php');
        $pageContent = ob_get_clean();

        require('./src/Views/layout.html.php');
    }
}

