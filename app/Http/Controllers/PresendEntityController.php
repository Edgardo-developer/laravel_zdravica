<?php

namespace App\Http\Controllers;

class PresendEntityController extends Controller
{
    public function getAmoID($client, $contactDB, $contactPrepared) : int{
        return 0;
    }
    private function checkExists($client, $contact) : string|object{
        return '';
    }
    private function createAmo($client, $contactDB) : string|int{
        return '';
    }
}
