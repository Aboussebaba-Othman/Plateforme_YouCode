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
    Schema::create('quiz_attempts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('candidate_id')->constrained();
        $table->foreignId('quiz_id')->constrained();
        $table->timestamp('started_at');
        $table->timestamp('completed_at')->nullable();
        $table->integer('score')->nullable();
        $table->enum('status', ['started', 'completed', 'passed', 'failed'])->default('started');
        $table->json('answers')->nullable()->comment('JSON of question_id => answer_id pairs');
        $table->integer('current_question')->default(0); 
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
