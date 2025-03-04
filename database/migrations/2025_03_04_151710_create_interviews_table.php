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
    Schema::create('interviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');  // The candidate
        $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');  // The examiner
        $table->date('date');
        $table->time('start_time');
        $table->time('end_time');
        $table->string('location');
        $table->string('type')->default('technical');  // technical, administrative, CME
        $table->string('status')->default('scheduled');  // scheduled, completed, cancelled
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interviews');
    }
};
