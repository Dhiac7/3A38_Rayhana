<?php
namespace App\Enum;

enum Role: string
{
    case AGRICULTEUR = 'agriculteur';
    case CLIENT = 'client';
    case FERMIER = 'fermier';
    case LIVREUR = 'livreur';
    case INSPECTEUR = 'inspecteur';
}
