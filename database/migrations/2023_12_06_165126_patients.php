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
        Schema::create('PATIENTS', static function (Blueprint $table){
            $table->id('PATIENTS_ID');
            $table->string('NOM');
            $table->string('PRENOM');
            $table->string('PATRONYME');
            $table->string('MOBIL_NYY');
            $table->string('EMAIL')->nullable();
            $table->string('POL')->nullable();
            $table->string('GOROD')->nullable();
            $table->string('NE_LE')->nullable();
            $table->string('RAYON_VYBORKA')->nullable();
            $table->string('ULICA')->nullable();
            $table->string('DOM')->nullable();
            $table->string('KVARTIRA')->nullable();
            $table->string('updated_at')->default(time());
            $table->string('created_at')->default(time());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PATIENTS');
    }
};
