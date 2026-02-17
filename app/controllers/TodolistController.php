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


---

Les tâches ont été supprimées.
');
        }
        
        $this->redirect('/todolist?cleared=1');
    }

    public function pdf(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $pdfPath = $projectRoot . '/public/todolist.pdf';
        $htmlPath = $projectRoot . '/public/todolist.html';
        
        // Vérifier si le PDF existe et a du contenu
        if (file_exists($pdfPath) && filesize($pdfPath) > 0) {
            $this->servePdf($pdfPath);
            return;
        }
        
        // Fallback: Si le HTML existe, le servir comme PDF
        if (file_exists($htmlPath) && filesize($htmlPath) > 0) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="BNGRC-Todolist.pdf"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            readfile($htmlPath);
            exit;
        }
        
        // Si rien n'existe
        http_response_code(404);
        echo '<h1>Fichier non trouvé</h1>';
    }

    private function servePdf(string $pdfPath): void
    {
        if (!file_exists($pdfPath)) {
            http_response_code(404);
            echo '<h1>PDF non trouvé</h1>';
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="BNGRC-Todolist.pdf"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        readfile($pdfPath);
        exit;
    }
}
