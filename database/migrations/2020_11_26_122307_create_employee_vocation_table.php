<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeVocationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'employee_vocation', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'employee_id' );
            $table->foreignId( 'vocation_id' );

            $table->foreign( 'employee_id' )->references( 'id' )->on( 'employees' )->onDelete( 'cascade' );
            $table->foreign( 'vocation_id' )->references( 'id' )->on( 'vocations' )->onDelete( 'cascade' );

            $table->primary( [ 'employee_id', 'vocation_id' ] );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'employee_vocation' );
    }
}
