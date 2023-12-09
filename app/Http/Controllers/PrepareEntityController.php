<?php

namespace App\Http\Controllers;

class PrepareEntityController extends Controller
{
    private static array $amoFields;

    public function prepare(array $rawDB, int $Id) : array{
        return [];
    }
    private function matchFields(string $mergedFields, array $rawDB){
        return;
    }
}
