<?php

namespace App\Enum;

enum Niveau: string
{
    case MATERNELLE    = 'maternelle';
    case PRIMAIRE      = 'primaire';
    case SECONDAIRE    = 'secondaire';
    case UNIVERSITAIRE = 'universitaire';

    public function label(): string
    {
        return match($this) {
            self::MATERNELLE    => 'Maternelle',
            self::PRIMAIRE      => 'Primaire',
            self::SECONDAIRE    => 'Secondaire',
            self::UNIVERSITAIRE => 'Universitaire',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::MATERNELLE    => 'bg-pink-100 text-pink-800',
            self::PRIMAIRE      => 'bg-blue-100 text-blue-800',
            self::SECONDAIRE    => 'bg-green-100 text-green-800',
            self::UNIVERSITAIRE => 'bg-purple-100 text-purple-800',
        };
    }
}
