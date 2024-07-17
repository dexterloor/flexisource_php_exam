<?php

namespace App\Utility;

class Utility
{
    public static function hash($string, $hashType = "md5")
    {
        switch ($hashType) {
            case 'md5':
                return md5($string);
            case 'sha1':
                return sha1($string);
            case 'sha256':
                return hash('sha256', $string);
            case 'bcrypt':
                return password_hash($string, PASSWORD_DEFAULT);
            case 'argon2':
                return password_hash($string, PASSWORD_ARGON2I);
            default:
                throw new \Exception('Unknown hash type provided.');
        }
    }
}