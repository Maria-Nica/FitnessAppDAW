<?php

/**
 * Renderer de sabloane pentru View-uri.
 * Centralizeaza logica de injectare a datelor in View-uri.
 * 
 * Utilizare:
 *   $renderer = new ViewRenderer();
 *   $renderer->render('Views/retete.php', ['isAdmin' => true, 'recipes' => $data]);
 */
class ViewRenderer {
    
    /**
     * Randa un fisier View cu variabilele date.
     * 
     * @param string $viewPath Calea relativă la View 
     * @param array $data Variabilele care vor fi disponibile in View
     * @return void
     */
    public function render(string $viewPath, array $data = []): void {
        // Extrage datele in variabile locale
        extract($data, EXTR_SKIP);
        
        // Determina calea absoluta a fisierului
        $filePath = __DIR__ . '/../' . $viewPath;
        
        // Verifica daca fisierul exista
        if (!file_exists($filePath)) {
            throw new Exception("Fisierul View nu a fost gasit: {$filePath}");
        }
        
        // Include View-ul cu variabilele disponibile
        require_once $filePath;
    }
}
