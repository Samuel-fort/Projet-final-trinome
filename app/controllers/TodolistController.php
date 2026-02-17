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
        // Chemins des fichiers
        $projectRoot = dirname(__DIR__, 2);
        $htmlPath = $projectRoot . '/public/todolist.html';
        $pdfPath = $projectRoot . '/public/todolist.pdf';
        
        // Si le PDF existe et est récent (moins de 1 heure), le servir directement
        if (file_exists($pdfPath) && (time() - filemtime($pdfPath)) < 3600) {
            $this->servePdf($pdfPath);
            return;
        }
        
        // Régénérer le PDF via LibreOffice
        if (file_exists($htmlPath)) {
            $this->generatePdfFromHtml($htmlPath, $pdfPath);
            if (file_exists($pdfPath)) {
                $this->servePdf($pdfPath);
                return;
            }
        }
        
        // Fallback: créer le HTML puis le servir comme PDF téléchargeable
        header('Content-Type: text/html; charset=utf-8');
        echo '<h1>Erreur</h1><p>Impossible de générer le PDF. Veuillez essayer ultérieurement.</p>';
    }

    private function generatePdfFromHtml(string $htmlPath, string $pdfPath): bool
    {
        // Utiliser LibreOffice pour convertir HTML en PDF
        $command = sprintf(
            'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg(dirname($pdfPath)),
            escapeshellarg($htmlPath)
        );
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        return file_exists($pdfPath);
    }

    private function servePdf(string $pdfPath): void
    {
        if (!file_exists($pdfPath)) {
            return;
        }

        // Envoyer les headers pour téléchargement PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="BNGRC-Todolist.pdf"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        // Lire et afficher le fichier PDF
        readfile($pdfPath);
        exit;
    }
}
