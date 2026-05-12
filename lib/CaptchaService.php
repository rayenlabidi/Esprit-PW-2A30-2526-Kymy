<?php

class CaptchaService
{
    public static function getChallenge()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['captcha_challenge']) || empty($_SESSION['captcha_challenge']['type'])) {
            return self::refreshChallenge();
        }

        return $_SESSION['captcha_challenge'];
    }

    public static function refreshChallenge()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $lastType = $_SESSION['captcha_last_type'] ?? null;
        $type = $lastType === 'text' ? 'puzzle' : 'text';
        $_SESSION['captcha_last_type'] = $type;

        if ($type === 'puzzle') {
            $challenge = [
                'type' => 'puzzle',
                'target' => random_int(34, 82),
                'start' => random_int(6, 22),
                'token' => bin2hex(random_bytes(8))
            ];
            unset($_SESSION['captcha_code']);
        } else {
            $challenge = [
                'type' => 'text',
                'code' => self::generateTextCode()
            ];
            $_SESSION['captcha_code'] = $challenge['code'];
        }

        $_SESSION['captcha_challenge'] = $challenge;

        return $challenge;
    }

    public static function clear()
    {
        unset($_SESSION['captcha_challenge'], $_SESSION['captcha_code']);
    }

    public static function validate(array $post) //bech tchouf reponse s7i7a ou non  ou veif ta3 robot
    {
        if (($post['captcha_robot_check'] ?? '') !== '1') {
            return false;
        }

        $challenge = self::getChallenge();

        if (($challenge['type'] ?? '') === 'puzzle') {
            $answer = filter_var($post['captcha_puzzle_answer'] ?? null, FILTER_VALIDATE_INT);

            return $answer !== false && abs($answer - (int)$challenge['target']) <= 1;
        }

        $expected = $challenge['code'] ?? ($_SESSION['captcha_code'] ?? null);

        return $expected !== null && trim((string)($post['captcha_answer'] ?? '')) === (string)$expected;
    }

    private static function generateTextCode()
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $all = $upper . $lower . $numbers;
        $characters = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)]
        ];

        while (count($characters) < 8) {
            $characters[] = $all[random_int(0, strlen($all) - 1)];
        }

        for ($i = count($characters) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$characters[$i], $characters[$j]] = [$characters[$j], $characters[$i]];
        }

        return implode('', $characters);
    }
}

?>
