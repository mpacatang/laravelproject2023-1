<?php

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
        Schema::table('home_layouts', function (Blueprint $table) {
            $table->string('type')->change();
        });

        //FEAT: Adding action buttons, image capabilities in notifications
        Schema::table('manual_notifications', function (Blueprint $table) {
            $table->json('actions')->nullable();
            $table->string('image')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('manual_notifications', function (Blueprint $table){
            $table->dropColumn('actions');
            $table->dropColumn('image');
        });

    }
};
