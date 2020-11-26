<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model {
    protected $guarded = [ 'id', 'created_at', 'updated_at' ];

    public function vacations() {
        return $this->belongsToMany( 'App\Models\Vacation' );
    }

    public function schedule() {
        return $this->belongsTo( 'App\Models\Schedule' );
    }
}
