<?php

namespace Tests\Feature;

use App\Http\Controllers\Sends\UpdateLeadController;
use Illuminate\Foundation\Application;
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

    public function testCheckExplodeOffer(){
        $updateLead = new UpdateLeadController([]);
        $listsOffers = 'Прием (осмотр, консультация) врача-эндокринолога первичный###3300.00|||Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(3,$offers['offerNames']);
        $this->assertCount(3,$offers['offerPrices']);
    }

    public function testCheckExplodeOffer1(){
        $updateLead = new UpdateLeadController([]);
        $listsOffers = 'Прием (осмотр, консультация) врача-диетолога первичный###4100.00|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(2,$offers['offerNames']);
        $this->assertCount(2,$offers['offerPrices']);
    }

    public function testCheckExplodeOffer3(){
        $updateLead = new UpdateLeadController([]);
        $listsOffers = 'Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(1,$offers['offerNames']);
        $this->assertCount(1,$offers['offerPrices']);
    }

    public function testCheckExplodeOfferWithEmptyInStart(){
        $updateLead = new UpdateLeadController([]);
        $listsOffers = '|||Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(1,$offers['offerNames']);
        $this->assertCount(1,$offers['offerPrices']);
    }

    public function testCheckExplodeOfferWithEmptyInEnd(){
        $updateLead = new UpdateLeadController([]);
        $listsOffers = 'Прием (осмотр, консультация) врача-гастроэнтеролога первичный###3300.00|||';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(1,$offers['offerNames']);
        $this->assertCount(1,$offers['offerPrices']);
    }

    public function testCheckLongWords()
    {
        $updateLead = new UpdateLeadController([]);
        $listsOffers = 'Видеогастроскопия (с тестом на лактазную недостаточность по биопсии)+Видеоколоноскопия под наркозом###3300.00|||';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(1,$offers['offerNames']);
        $this->assertCount(1,$offers['offerPrices']);
        $this->assertEquals('Видеогастроскопия (с тестом на лактазную недостаточность по биопсии)+Видеоколоноскопия под наркозом',$offers['offerNames'][0]);
        $this->assertEquals('3300.00',$offers['offerPrices'][0]);
    }
    public function testCheckLongistWords()
    {
        $updateLead = new UpdateLeadController([]);
        $listsOffers = 'Восстановление временного зуба пломбой II, III класс по Блэку с использованием стоматологических цементов (СИЦ, компомер)###3100.00|||Фторирование всех зубов (аппликация фтористого геля Флюокаль, Топекс-пена, Дюрофат) у детей###1150.00|||Профессиональная гигиена полости рта и зубов у детей с использованием проф.пасты и щетки###1500.00|||Инфильтрационная анестезия###650.00|||Радиовизиография, 1 зуб###350.00|||';
        $offers = $updateLead->explodeOffers($listsOffers);
        $this->assertCount(5,$offers['offerNames']);
        $this->assertCount(5,$offers['offerPrices']);
        dd($offers);
    }
}
