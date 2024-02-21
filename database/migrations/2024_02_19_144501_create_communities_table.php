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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('comments_id');
            $table->string('title', 100);
            $table->string('language', 30);
            $table->string('type', 30)->default('public');
            $table->integer('num_persons')->default(1);
            $table->integer('num_comments')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
