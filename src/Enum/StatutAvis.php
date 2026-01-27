<?php

namespace App\Enum;

enum StatutAvis: string
{
    case EN_ATTENTE = 'En attente';
    case VALIDE = 'Validé';
    case REJETE = 'Rejeté';
}