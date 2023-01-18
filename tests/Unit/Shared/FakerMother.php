<?php

namespace Tests\Unit\Shared;

use Faker\Generator as Faker;
use Illuminate\Support\Facades\App;

final class FakerMother
{

    public static function quantity(int $min = 1, int $max = 2000): int
    {
        return App::make(Faker::class)->numberBetween($min, $max);
    }

    public static function word(): string
    {
        return App::make(Faker::class)->word();
    }


}
