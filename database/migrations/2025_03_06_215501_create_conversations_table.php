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
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('last_message_id')->nullable()->constrained('messages');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
