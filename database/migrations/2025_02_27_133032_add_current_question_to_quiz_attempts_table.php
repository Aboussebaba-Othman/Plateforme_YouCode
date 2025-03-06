<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('quiz_attempts', function (Blueprint $table) {
        if (!Schema::hasColumn('quiz_attempts', 'current_question')) {
            $table->integer('current_question')->default(0);
        }
    });
}


public function down()
{
    Schema::table('quiz_attempts', function (Blueprint $table) {
        $table->dropColumn('current_question');
    });
}

    
};
