<?php

namespace Bejao\Shared\Framework;

use Illuminate\Support\Facades\App;

class Container
{

    /**
     * @param string $id
     * @return object
     */
    public static function getObjectInstance(string $id): object
    {
        return App::make($id);
    }
}
