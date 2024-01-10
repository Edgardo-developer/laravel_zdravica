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
        Schema::create('amocrm_products', function(Blueprint $blueprint){
            $blueprint->id();
            $blueprint->string('name')->nullable();
            $blueprint->integer('amoID')->nullable();
            $blueprint->integer('DBId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amocrm_products');
    }
};
