<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amocrm_lead', function(Blueprint $table){
            $table->id();
            $table->string('direction');
            $table->string('specDoc');
            $table->integer('patID');
            $table->string('fioDoc');
            $table->boolean('declareVisit')->default(false);
            $table->string('filial');
            $table->date('date');
            $table->integer('updated_at')->default($this->getTimestamp());
            $table->integer('created_at')->default($this->getTimestamp());
            $table->integer('billID')->nullable();
            $table->float('billSum')->nullable();
            $table->string('offers')->nullable();
            $table->string('managerName')->nullable();
            $table->string('amoManagerID')->nullable();
        });
    }

    private function getTimestamp(){
        $arr = (array)DB::select("SELECT DATEDIFF(s, '1970-01-01', GETUTCDATE())")[0];
        reset($arr);
        return $arr[key($arr)];
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amocrm_lead');
    }
};
