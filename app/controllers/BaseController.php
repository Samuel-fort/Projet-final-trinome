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

    /**
     * Simple markdown to HTML conversion
     * Supports: headings, paragraphs, lists, bold, italic, code, blockquotes, tables
     */
    protected function simpleMarkdownToHtml(string $markdown): string
    {
        // First, escape HTML special characters
        $html = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');
        
        // Restore the escaped characters for markdown syntax processing
        $html = $markdown;
        
        // Split into lines for processing
        $lines = explode("\n", $html);
        $result = [];
        $inCodeBlock = false;
        $inList = false;
        $inTable = false;
        $currentList = [];
        
        foreach ($lines as $line) {
            // Code blocks
            if (str_starts_with($line, '```')) {
                if ($inCodeBlock) {
                    $result[] = "</code></pre>";
                    $inCodeBlock = false;
                } else {
                    $inCodeBlock = true;
                    $result[] = "<pre><code>";
                }
                continue;
            }
            
            if ($inCodeBlock) {
                $result[] = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
                continue;
            }
            
            // Tables
            if (strpos($line, '|') !== false && !empty(trim($line))) {
                if (!$inTable) {
                    $result[] = "<table>";
                    $inTable = true;
                }
                
                $cells = explode('|', $line);
                $cells = array_map('trim', $cells);
                $cells = array_filter($cells, fn($c) => $c !== '');
                
                // Check if it's a separator row
                if (count($cells) > 0 && preg_match('/^[\s-:]+$/', $cells[0])) {
                    continue;
                }
                
                $result[] = "<tr>";
                foreach ($cells as $cell) {
                    $cellContent = htmlspecialchars($cell, ENT_QUOTES, 'UTF-8');
                    // Bold
                    $cellContent = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $cellContent);
                    // Italic
                    $cellContent = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $cellContent);
                    // Inline code
                    $cellContent = preg_replace('/`(.+?)`/', '<code>$1</code>', $cellContent);
                    
                    $result[] = "<td>" . $cellContent . "</td>";
                }
                $result[] = "</tr>";
                continue;
            } else {
                if ($inTable) {
                    $result[] = "</table>";
                    $inTable = false;
                }
            }
            
            // Headings
            if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $matches)) {
                $level = strlen($matches[1]);
                $content = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
                // Apply inline formatting
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
                $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);
                $result[] = "<h$level>" . $content . "</h$level>";
                $inList = false;
                continue;
            }
            
            // Horizontal rule
            if (preg_match('/^(\-{3,}|\*{3,}|_{3,})$/', $line)) {
                if ($inList) {
                    $result[] = "</ul>";
                    $inList = false;
                }
                $result[] = "<hr>";
                continue;
            }
            
            // Lists
            if (preg_match('/^[\s]*[-*+]\s+(.+)$/', $line, $matches)) {
                if (!$inList) {
                    $result[] = "<ul>";
                    $inList = true;
                }
                $content = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
                // Apply inline formatting
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
                $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);
                $result[] = "<li>" . $content . "</li>";
                continue;
            } elseif (preg_match('/^[\s]*\d+\.\s+(.+)$/', $line, $matches)) {
                if ($inList && count($result) > 0 && substr(end($result), 0, 3) !== '<ol') {
                    $result[] = "</ul>";
                    $result[] = "<ol>";
                } elseif (!$inList) {
                    $result[] = "<ol>";
                }
                $inList = true;
                $content = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
                // Apply inline formatting
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
                $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);
                $result[] = "<li>" . $content . "</li>";
                continue;
            }
            
            // Blockquotes
            if (str_starts_with(trim($line), '>')) {
                $content = htmlspecialchars(substr(trim($line), 1), ENT_QUOTES, 'UTF-8');
                // Apply inline formatting
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
                $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);
                $result[] = "<blockquote>" . trim($content) . "</blockquote>";
                $inList = false;
                continue;
            }
            
            // Paragraphs
            $trimmedLine = trim($line);
            if (!empty($trimmedLine)) {
                if ($inList) {
                    $result[] = "</ul>";
                    $inList = false;
                }
                
                $content = htmlspecialchars($trimmedLine, ENT_QUOTES, 'UTF-8');
                // Apply inline formatting
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
                $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);
                // Links [text](url)
                $content = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank">$1</a>', $content);
                $result[] = "<p>" . $content . "</p>";
            } else {
                if ($inList) {
                    $result[] = "</ul>";
                    $inList = false;
                }
            }
        }
        
        // Close any open tags
        if ($inCodeBlock) {
            $result[] = "</code></pre>";
        }
        if ($inList) {
            $result[] = "</ul>";
        }
        if ($inTable) {
            $result[] = "</table>";
        }
        
        return implode("\n", $result);
    }
}