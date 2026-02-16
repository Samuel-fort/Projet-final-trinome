<?php

namespace app\controllers;

use flight\Engine;

abstract class BaseController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /** Rend une vue dans le layout principal */
    protected function render(string $view, array $data = [], string $pageTitle = 'BNGRC'): void
    {
        // Rendre le contenu de la page
        ob_start();
        extract($data);
        $viewPath = $this->app->get('flight.views.path') . '/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<p class='text-danger'>Vue introuvable : $viewPath</p>";
        }
        $content = ob_get_clean();

        // Rendre le layout avec le contenu
        $layoutPath = $this->app->get('flight.views.path') . '/layouts/main.php';
        extract(['content' => $content, 'pageTitle' => $pageTitle]);
        include $layoutPath;
    }

    protected function redirect(string $url): void
    {
        $this->app->redirect($url);
    }

    protected function db()
    {
        return $this->app->db();
    }
}