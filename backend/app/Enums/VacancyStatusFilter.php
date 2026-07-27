<?php

namespace App\Enums;

enum VacancyStatusFilter: string
{
    case Active = 'active';
    case Expired = 'expired';
    case All = 'all';

}
