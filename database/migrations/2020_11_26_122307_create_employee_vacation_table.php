<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeVacationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'employee_vacation', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'employee_id' );
            $table->foreignId( 'vacation_id' );

            $table->foreign( 'employee_id' )->references( 'id' )->on( 'employees' )->onDelete( 'cascade' );
            $table->foreign( 'vacation_id' )->references( 'id' )->on( 'vacations' )->onDelete( 'cascade' );

            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'employee_vacation' );
    }
}
