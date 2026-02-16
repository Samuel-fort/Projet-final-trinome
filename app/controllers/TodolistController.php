<?php

namespace app\controllers;

class TodolistController extends BaseController
{
    public function index(): void
    {
        // Read the todolist.md file from the project root
        $todolistPath = dirname(__DIR__, 2) . '/todolist.md';
        
        if (file_exists($todolistPath)) {
            $content = file_get_contents($todolistPath);
            // Pass the markdown content to the view
            $this->render('todolist-markdown', ['content' => $content], 'Todolist - BNGRC');
        } else {
            // Fallback to the old view if file doesn't exist
            $this->render('todolist', [], 'Todolist - BNGRC');
        }
    }
}
