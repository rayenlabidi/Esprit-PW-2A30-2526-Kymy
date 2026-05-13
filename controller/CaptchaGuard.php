<?php
require_once __DIR__ . '/AuthC.php';

class CaptchaGuard
{
    private const SESSION_KEY = 'workify_picture_captchas';

    private static $items = [
        [
            'key' => 'briefcase',
            'label' => 'Mallette',
            'prompt' => 'la mallette',
            'icon' => 'M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z'
        ],
        [
            'key' => 'book',
            'label' => 'Livre',
            'prompt' => 'le livre',
            'icon' => 'M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 0-3-3V4zm3 14h9V7a1 1 0 0 0-1-1H7v10.2c.3-.1.7-.2 1-.2z'
        ],
        [
            'key' => 'shield',
            'label' => 'Bouclier',
            'prompt' => 'le bouclier',
            'icon' => 'M12 2l8 3v6c0 5-3.4 9.4-8 11-4.6-1.6-8-6-8-11V5l8-3zm0 4.2L7 8v3c0 3.1 2 6 5 7.3 3-1.3 5-4.2 5-7.3V8l-5-1.8z'
        ],
        [
            'key' => 'rocket',
            'label' => 'Fusee',
            'prompt' => 'la fusee',
            'icon' => 'M12 2c3.4.6 6.4 3.6 7 7-2.6.4-5.1 1.8-7.1 3.8L9.2 10.1C11.2 8.1 12.6 5.6 13 3c-.3-.1-.6-.1-1-.1zm-4.2 9.5l4.7 4.7-1.4 1.4-4.7-4.7 1.4-1.4zM5 15l4 4-5 1 1-5zm10-1l3 3-1 4-4-4 2-3z'
        ],
        [
            'key' => 'keyboard',
            'label' => 'Clavier',
            'prompt' => 'le clavier',
            'icon' => 'M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm1 4h2V8H5v2zm4 0h2V8H9v2zm4 0h2V8h-2v2zm4 0h2V8h-2v2zM5 14h14v-2H5v2z'
        ],
        [
            'key' => 'calendar',
            'label' => 'Calendrier',
            'prompt' => 'le calendrier',
            'icon' => 'M7 2h2v3h6V2h2v3h3v17H4V5h3V2zm11 8H6v10h12V10z'
        ],
        [
            'key' => 'envelope',
            'label' => 'Enveloppe',
            'prompt' => 'l enveloppe',
            'icon' => 'M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z'
        ],
        [
            'key' => 'bulb',
            'label' => 'Ampoule',
            'prompt' => 'l ampoule',
            'icon' => 'M9 21h6v-2H9v2zm3-19a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2zm2 11.5-.8.6V15h-2.4v-.9l-.8-.6A5 5 0 1 1 14 13.5z'
        ]
    ];

    public static function challenge($scope)
    {
        AuthC::startSession();
        $scope = self::scope($scope);

        if (!isset($_SESSION[self::SESSION_KEY][$scope])) {
            return self::refresh($scope);
        }

        return $_SESSION[self::SESSION_KEY][$scope];
    }

    public static function refresh($scope)
    {
        AuthC::startSession();
        $scope = self::scope($scope);
        $choices = self::$items;
        shuffle($choices);
        $choices = array_slice($choices, 0, 4);
        $answer = $choices[random_int(0, count($choices) - 1)];

        $challenge = [
            'id' => bin2hex(random_bytes(12)),
            'prompt' => 'Selectionnez ' . $answer['prompt'],
            'speak' => 'Selectionnez ' . $answer['label'],
            'answer' => $answer['key'],
            'choices' => $choices
        ];

        $_SESSION[self::SESSION_KEY][$scope] = $challenge;
        return $challenge;
    }

    public static function validate($scope, $id, $answer)
    {
        AuthC::startSession();
        $scope = self::scope($scope);

        if (!isset($_SESSION[self::SESSION_KEY][$scope])) {
            return false;
        }

        $challenge = $_SESSION[self::SESSION_KEY][$scope];
        unset($_SESSION[self::SESSION_KEY][$scope]);

        return hash_equals((string) $challenge['id'], (string) $id)
            && hash_equals((string) $challenge['answer'], trim((string) $answer));
    }

    public static function publicChallenge($challenge)
    {
        return [
            'id' => $challenge['id'],
            'prompt' => $challenge['prompt'],
            'speak' => $challenge['speak'],
            'choices' => $challenge['choices']
        ];
    }

    private static function scope($scope)
    {
        $scope = preg_replace('/[^a-z0-9_-]/i', '', (string) $scope);
        return $scope !== '' ? $scope : 'default';
    }
}
?>
