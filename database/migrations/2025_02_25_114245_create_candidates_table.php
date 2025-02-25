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
    Schema::create('candidates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->date('date_of_birth')->nullable();
        $table->string('phone')->nullable();
        $table->text('address')->nullable();
        $table->string('id_card_path')->nullable();
        $table->enum('status', ['pending', 'documents_submitted', 'documents_approved', 'quiz_passed', 'test_scheduled'])->default('pending');
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
        Schema::dropIfExists('candidates');
    }
};
