<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'schedules', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'work_start', 5 ); // Format "H:i"
            $table->string( 'work_end', 5 ); // Format "H:i"
            $table->string( 'dinner_start', 5 ); // Format "H:i"
            $table->string( 'dinner_end', 5 ); // Format "H:i"
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'schedules' );
    }
}
