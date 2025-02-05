<?php
namespace App\Enum;

enum StatutUser: string
{
    case ACTIF = 'actif';
    case INACTIF = 'inactif';
    case BANNI = 'banni';
}
