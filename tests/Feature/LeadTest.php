<?php

namespace Tests\Feature;

use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use App\Models\PLANNING;
use Database\Factories\AmoCRMLeadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_example() : void{
        $this->seed();
            $array = AmoCRMLead::find(1);
            if ($array){
                $array = $array->toArray();
                $SendToAmoCRM = new SendToAmoCRM();
                $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM($array);
                $this->assertIsArray($SendToAmoCRMArr);
                $this->assertNotEmpty($SendToAmoCRMArr);
                if ($SendToAmoCRMArr){
                    $this->assertGreaterThan(0, $SendToAmoCRMArr['amoContactID']);
                    $this->assertGreaterThan(0, $SendToAmoCRMArr['amoLeadID']);
                    $this->assertGreaterThan(0, $SendToAmoCRMArr['leadDBId']);
                }
            }
    }

//    public function updateLead(){
//
//    }
//
//    public function deleteLead(){
//
//    }
//
//    public function deleteLeadsOnFail(){
//
//    }
//
//    public function deleteLeadsOnSuccess(){
//
//    }
}
