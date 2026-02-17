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

    public function deleteAll(): void
    {
        // Clear the todolist.md file
        $todolistPath = dirname(__DIR__, 2) . '/todolist.md';
        
        if (file_exists($todolistPath)) {
            // Clear the file content
            file_put_contents($todolistPath, '# BNGRC - Todolist du Projet

**Projet:** Système de Gestion des Dons pour Sinistrés  
**Équipe:** Voara (004587), Samuel (003889), Lionel (003972)  
**Dernière mise à jour:** ' . date('d F Y') . '

---

Les tâches ont été supprimées.
');
        }
        
        $this->redirect('/todolist?cleared=1');
    }
}
