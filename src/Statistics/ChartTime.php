<?php

namespace Ro749\SharedUtils\Statistics;

interface InterfaceChartTime{
    public function format(): string;
}

trait TraitFormatChartTime {
    public function format(): string {
        return match($this) {
            self::DAY => '%Y-%m-%d',
            self::WEEK => '%Y-%u',
            self::MONTH => '%Y-%m',
            self::YEAR => '%Y',
        };
    }
}

enum ChartTime: string implements InterfaceChartTime {
    use TraitFormatChartTime;
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}


