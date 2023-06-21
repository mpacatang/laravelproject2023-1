<?php

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();

            $table->timestamps();
        });

        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->foreignIdFor(Chat::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default(ChatMessage::$text_type);
            $table->text('message')->nullable();
            $table->string('image')->nullable();
            $table->boolean('seen')->default(false);

            $table->foreignIdFor(Chat::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ChatParticipant::class)->constrained()->cascadeOnDelete();
            $table->timestamp('sent_at')->useCurrent();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('chats');
        Schema::drop('chat_participants');
        Schema::drop('chat_messages');
    }
};
