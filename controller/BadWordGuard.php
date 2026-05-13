<?php

class BadWordGuard
{
    private static $words = [
        'fuck',
        'shit',
        'bitch',
        'asshole',
        'slut',
        'whore',
        'dick',
        'pussy',
        'faggot',
        'nigger',
        'kys',
        'kill yourself',
        'merde',
        'putain',
        'salope',
        'connard',
        'pute',
        'zebi',
        'nik',
        'nique',
        'kos',
        'zabour',
        'kahba',
        'nayek',
        'nayak'
    ];

    public static function containsBadWords($text)
    {
        $normalized = strtolower((string) $text);
        $normalized = preg_replace('/[^a-z0-9]+/i', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        foreach (self::$words as $word) {
            $needle = strtolower($word);
            if (preg_match('/(^|\s)' . preg_quote($needle, '/') . '(\s|$)/', $normalized)) {
                return true;
            }
        }

        return false;
    }

    public static function message()
    {
        return 'Votre texte contient des mots inappropries. Merci de reformuler avant d envoyer.';
    }
}
?>
