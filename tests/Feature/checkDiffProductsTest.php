<?php

namespace Tests\Feature;

use App\Http\Controllers\Sends\UpdateLeadController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class checkDiffProductsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testIsEmptyUnlink(): void
    {
        $updateLead = new UpdateLeadController([]);
        $amoOffers = '';
        $listsOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['unlink']);
        $this->assertNotEmpty($result['link']);
    }

    public function testIsEmptylinkTest(): void
    {
        $updateLead = new UpdateLeadController([]);
        $amoOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $listsOffers = '';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['link']);
        $this->assertNotEmpty($result['unlink']);
    }

    public function testIsTwoLeftTest(): void
    {
        $updateLead = new UpdateLeadController([]);
        // В амо было 3 услуги
        $amoOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        // В БД стала 1 услуга
        $listsOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['link']);
        $this->assertCount(2, $result['unlink']['offerNames']);
    }

    public function testIsTwoAddedTest(): void
    {
        $updateLead = new UpdateLeadController([]);
        // В амо была 1 услуга
        $amoOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00';
        // В БД стало 3 услуга
        $listsOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['unlink']);
        $this->assertCount(2, $result['link']['offerNames']);
    }

    public function testOneOfOneDeleted(): void
    {
        $updateLead = new UpdateLeadController([]);
        // В амо была 1 услуга
        $amoOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00';
        // В БД стало 0 услуг
        $listsOffers = '';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['link']);
        $this->assertCount(1, $result['unlink']['offerNames']);
    }

    public function testOneOfOneAdded(): void
    {
        $updateLead = new UpdateLeadController([]);
        // В амо 0 услуг
        $amoOffers = '';
        // В БД стала 1 услуга
        $listsOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['unlink']);
        $this->assertCount(1, $result['link']['offerNames']);
    }

    public function testDeleteOneFromThree(){
        $updateLead = new UpdateLeadController([]);
        // В амо было 3 услуга
        $amoOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        // В БД стало 2 услуги
        $listsOffers = 'Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['link']);
        $this->assertCount(1, $result['unlink']['offerNames']);
    }

    public function testAddOneToTwo(){
        $updateLead = new UpdateLeadController([]);
        // В амо было 2 услуги
        $listsOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        // В БД стало 3 услуги
        $amoOffers = 'Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $result = $updateLead->manageProducts(['amoOffers'=>$amoOffers,'offerLists'=>$listsOffers]);
        $this->assertEmpty($result['unlink']);
        $this->assertCount(1, $result['link']['offerNames']);
    }
}
