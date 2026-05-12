<?php

/*
 * Service QR code utilisateur.
 * Il prepare une vCard contenant les informations personnelles a encoder.
 */

class UserQrService
{
    // Construit l'URL publique vers le profil d'un utilisateur.
    public static function buildProfileUrl($userId)
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = rtrim(dirname($_SERVER['REQUEST_URI'] ?? '/standalone_gestion_utilisateurs/view/listeUtilisateurs.php'), '/\\');

        return $scheme . '://' . $host . $basePath . '/profile.php?id=' . urlencode((string)$userId);
    }

    public static function enrichUser(array $user)
    {
        $roleName = !empty($user['role_name']) ? $user['role_name'] : 'USER';
        $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $profileUrl = self::buildProfileUrl($user['id'] ?? '');

        // On ajoute a l'utilisateur les valeurs dont la vue a besoin.
        $user['full_name'] = $fullName;
        $user['profile_url'] = $profileUrl;
        $user['qr_payload'] = self::buildVcard($user, $roleName, $fullName, $profileUrl);

        return $user;
    }

    private static function buildVcard(array $user, $roleName, $fullName, $profileUrl)
    {
        // vCard est un format standard: beaucoup de telephones savent le lire apres scan.
        return "BEGIN:VCARD\n"
            . "VERSION:3.0\n"
            . "FN:" . self::escape($fullName) . "\n"
            . "N:" . self::escape($user['last_name'] ?? '') . ";" . self::escape($user['first_name'] ?? '') . ";;;\n"
            . "EMAIL:" . self::escape($user['email'] ?? '') . "\n"
            . "TEL:" . self::escape($user['phone'] ?? '') . "\n"
            . "TITLE:" . self::escape(!empty($user['headline']) ? $user['headline'] : 'Utilisateur Workify') . "\n"
            . "NOTE:" . self::escape(
                'ID: #' . ($user['id'] ?? '')
                . ' | Role: ' . strtoupper((string)$roleName)
                . ' | Statut: ' . ucfirst((string)($user['status'] ?? ''))
                . ' | Bio: ' . (!empty($user['bio']) ? $user['bio'] : 'Non renseignee')
            ) . "\n"
            . "URL:" . self::escape($profileUrl) . "\n"
            . "END:VCARD";
    }

    private static function escape($value)
    {
        // Echappement obligatoire pour eviter de casser le format vCard avec des virgules ou retours ligne.
        $value = str_replace(["\\", "\r\n", "\r", "\n", ";", ","], ["\\\\", "\\n", "\\n", "\\n", "\;", "\,"], (string)$value);
        return trim($value);
    }
}

?>
