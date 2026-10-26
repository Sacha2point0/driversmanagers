<?php
declare(strict_types=1);

namespace App\Services;

/** Panneaux de signalisation associés aux drivers (SVG en ligne, couleurs WORKLAB). */
final class Signs
{
    public static function svg(string $code, int $size = 48): string
    {
        $b = '#1226AA';   // bleu
        $c = '#FF8672';   // corail
        $inner = match ($code) {
            // Sois parfait : le contrôle technique permanent (panneau carré bleu, coche)
            'sp' => '<rect x="8" y="8" width="84" height="84" rx="14" fill="' . $b . '"/>'
                  . '<circle cx="50" cy="50" r="26" fill="none" stroke="#fff" stroke-width="7"/>'
                  . '<path d="M38 50 l9 9 l17-19" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>',
            // Fais plaisir : cédez le passage (triangle pointe en bas)
            'fp' => '<path d="M8 14 H92 L50 90 Z" fill="#fff" stroke="' . $c . '" stroke-width="10" stroke-linejoin="round"/>',
            // Sois fort : pas de dépanneuse (rond barré, SOS)
            'sf' => '<circle cx="50" cy="50" r="40" fill="#fff" stroke="' . $c . '" stroke-width="10"/>'
                  . '<text x="50" y="60" font-family="Nunito,Arial,sans-serif" font-weight="900" font-size="28" text-anchor="middle" fill="' . $b . '">SOS</text>'
                  . '<line x1="22" y1="78" x2="78" y2="22" stroke="' . $c . '" stroke-width="9" stroke-linecap="round"/>',
            // Fais des efforts : route en montée (triangle danger, pente)
            'fe' => '<path d="M50 9 L93 86 H7 Z" fill="#fff" stroke="' . $c . '" stroke-width="10" stroke-linejoin="round"/>'
                  . '<path d="M27 74 L73 74 L73 46 Z" fill="' . $b . '"/>'
                  . '<text x="40" y="66" font-family="Nunito,Arial,sans-serif" font-weight="900" font-size="15" text-anchor="middle" fill="' . $b . '">%</text>',
            // Dépêche-toi : excès de vitesse (rond, 130)
            'dt' => '<circle cx="50" cy="50" r="40" fill="#fff" stroke="' . $c . '" stroke-width="10"/>'
                  . '<text x="50" y="61" font-family="Nunito,Arial,sans-serif" font-weight="900" font-size="31" text-anchor="middle" fill="' . $b . '">130</text>',
            // Pas forcément un driver
            default => '<path d="M50 8 L92 50 L50 92 L8 50 Z" fill="#fff" stroke="' . $b . '" stroke-width="7" stroke-linejoin="round"/>'
                  . '<text x="50" y="62" font-family="Nunito,Arial,sans-serif" font-weight="900" font-size="34" text-anchor="middle" fill="' . $b . '">?</text>',
        };
        return '<svg class="sign" width="' . $size . '" height="' . $size . '" viewBox="0 0 100 100" aria-hidden="true">' . $inner . '</svg>';
    }
}
