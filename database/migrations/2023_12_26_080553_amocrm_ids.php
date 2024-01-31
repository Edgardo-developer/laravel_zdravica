<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amocrm_ids', function (Blueprint $blueprint){
            $blueprint->id();
            $blueprint->integer('leadDBId')->nullable();
            $blueprint->integer('amoContactID')->nullable();
            $blueprint->integer('amoLeadID')->nullable();
            $blueprint->integer('amoBillID')->nullable();
            $blueprint->string('amoOffers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amocrm_ids');
    }
};
