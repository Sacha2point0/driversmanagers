<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    /** Rend une vue dans le gabarit (layout/main par défaut). */
    public static function render(string $view, array $data = [], string $layout = 'layout/main'): void
    {
        $content = self::partial($view, $data);
        if ($layout === '') {
            echo $content;
            return;
        }
        echo self::partial($layout, array_merge($data, ['content' => $content]));
    }

    public static function partial(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require ROOT . '/views/' . $view . '.php';
        return (string) ob_get_clean();
    }
}
