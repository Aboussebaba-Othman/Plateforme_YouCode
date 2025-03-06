<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmeGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('cme_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('session_date');
            $table->enum('session_time', ['morning', 'afternoon']);
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->foreignId('cme_group_id')->nullable()->constrained()->onDelete('set null');
        });
    }

   
    public function down()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropForeign(['cme_group_id']);
            $table->dropColumn('cme_group_id');
        });

        Schema::dropIfExists('cme_groups');
    }
}