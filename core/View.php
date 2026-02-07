<?php

namespace Core;

class View
{
    protected static array $sections = [];
    protected static ?string $currentSection = null;

    public static function render(string $view, array $data = [], string $layout = 'layouts/app')
    {
        extract($data);

        // Render vista
        ob_start();
        require BASE_PATH . "/app/Views/{$view}.php";
        $content = ob_get_clean();

        // Render layout
        require BASE_PATH . "/app/Views/{$layout}.php";
    }

    public static function startSection(string $name)
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection()
    {
        self::$sections[self::$currentSection] = ob_get_clean();
        self::$currentSection = null;
    }

    public static function yield(string $name)
    {
        echo self::$sections[$name] ?? '';
    }
}