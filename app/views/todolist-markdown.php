<div class="todolist-container">
    <!-- Button section -->
    <div class="todolist-actions mb-3">
        <a href="/" class="btn btn-secondary btn-sm">
            <i class="bi bi-house-fill"></i> Retour au Dashboard
        </a>
        <form method="POST" action="/todolist/delete-all" style="display: inline;">
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir tout effacer ? Cette action est irréversible.');">
                <i class="bi bi-trash"></i> Tout effacer
            </button>
        </form>
    </div>

    <?php 
    // Display success message if tasks were cleared
    if (!empty($_GET['cleared'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo '<i class="bi bi-check-circle"></i> Les tâches ont été supprimées avec succès.';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    ?>

    <?php 
    // Simple markdown to HTML conversion
    $html = $this->simpleMarkdownToHtml($content ?? '');
    echo $html;
    ?>
</div>

<style>
.todolist-actions {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.todolist-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 8px;
}

.todolist-container h1 {
    font-size: 2.5rem;
    color: #1a73e8;
    border-bottom: 3px solid #1a73e8;
    padding-bottom: 0.5rem;
    margin-top: 0;
    margin-bottom: 1.5rem;
}

.todolist-container h2 {
    font-size: 1.75rem;
    color: #2d5016;
    margin-top: 2rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.todolist-container h3 {
    font-size: 1.3rem;
    color: #5f6368;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.todolist-container h4,
.todolist-container h5,
.todolist-container h6 {
    color: #5f6368;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.todolist-container p {
    line-height: 1.7;
    margin-bottom: 1rem;
    color: #333;
}

.todolist-container ul,
.todolist-container ol {
    margin-left: 2rem;
    margin-bottom: 1rem;
}

.todolist-container li {
    margin-bottom: 0.6rem;
    line-height: 1.6;
    color: #333;
}

.todolist-container table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 1.5rem;
    border: 1px solid #d0d0d0;
    border-radius: 4px;
    overflow: hidden;
}

.todolist-container table th,
.todolist-container table td {
    border: 1px solid #d0d0d0;
    padding: 0.75rem;
    text-align: left;
}

.todolist-container table th {
    background-color: #f0f0f0;
    font-weight: 600;
    color: #2d5016;
}

.todolist-container table tr:hover {
    background-color: #fafafa;
}

.todolist-container code {
    background-color: #f5f5f5;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    color: #d63384;
}

.todolist-container pre {
    background-color: #f5f5f5;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
    margin-bottom: 1rem;
    border: 1px solid #e0e0e0;
}

.todolist-container pre code {
    background: none;
    color: #333;
    padding: 0;
}

.todolist-container blockquote {
    border-left: 4px solid #1a73e8;
    padding-left: 1rem;
    margin-left: 0;
    margin-bottom: 1rem;
    color: #5f6368;
    font-style: italic;
}

.todolist-container hr {
    border: none;
    border-top: 2px solid #e0e0e0;
    margin: 2rem 0;
}

.todolist-container strong {
    font-weight: 600;
    color: #1a73e8;
}

.todolist-container em {
    font-style: italic;
}

/* Specific styling for OK and A FAIRE items */
.todolist-container strong:contains("OK") {
    color: #28a745;
}

/* Responsive */
@media (max-width: 768px) {
    .todolist-container {
        padding: 1rem;
    }

    .todolist-container h1 {
        font-size: 1.75rem;
    }

    .todolist-container h2 {
        font-size: 1.3rem;
    }

    .todolist-container h3 {
        font-size: 1.1rem;
    }

    .todolist-container table {
        font-size: 0.9rem;
    }

    .todolist-container table th,
    .todolist-container table td {
        padding: 0.5rem;
    }
}
</style>
