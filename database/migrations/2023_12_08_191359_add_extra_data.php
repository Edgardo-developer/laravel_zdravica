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
        Schema::table('amocrm_lead', function(Blueprint $table){
            $table->integer('leadDBId')->nullable()->unique();
            $table->integer('amoLeadID')->nullable()->unique();
            $table->integer('amoContactID')->nullable();
            $table->string('responsibleFIO')->nullable();
            $table->integer('responsible_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::table('amocrm_lead', function(Blueprint $table){
            $table->dropColumn('amoLeadID');
            $table->dropColumn('leadDBId');
            $table->dropColumn('amoContactID');
            $table->dropColumn('responsibleFIO');
            $table->dropColumn('responsible_user_id');
        });
    }
};
