<?php

namespace App\Http\Controllers;

class PrepareEntityController extends Controller
{
    private static array $amoFields;

    public static function prepare(array $rawDB, int $Id) : array{
        return [];
    }
    private static function matchFields(string $mergedFields, array $rawDB){
        return;
    }
}
