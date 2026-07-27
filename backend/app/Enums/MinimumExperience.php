<?php

namespace App\Enums;

enum MinimumExperience: string
{
    case LessThanOneYear = 'less-than-1-year';
    case OneToThreeYears = '1-3-years';
    case FourToFiveYears = '4-5-years';
    case SixToTenYears = '6-10-years';
    case MoreThanTenYears = 'more-than-10-years';

    public function label(): string
    {
        return match ($this) {
            self::LessThanOneYear => 'Kurang dari 1 tahun',
            self::OneToThreeYears => '1-3 tahun',
            self::FourToFiveYears => '4-5 tahun',
            self::SixToTenYears => '6-10 tahun',
            self::MoreThanTenYears => 'Lebih dari 10 tahun',
        };
    }
}
