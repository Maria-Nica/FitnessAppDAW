<?php
// /app/Core/Escaper.php
//Protectie impotriva XSS prin escaparea caracterelor speciale in HTML
//Se folosește în TOATE view-urile când se afișează date venite de la user sau DB
class Escaper {
    public  static function escape(?string $value): string {
        if ($value === null) return '';
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
