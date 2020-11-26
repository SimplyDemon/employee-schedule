<?php

namespace Database\Seeders;

use App\Models\CorporateParty;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Vacation;
use Illuminate\Database\Seeder;

class GeneralSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $scheduleFirst = Schedule::create( [
            'work_start'   => '10:00',
            'work_end'     => '19:00',
            'dinner_start' => '13:00',
            'dinner_end'   => '14:00',
        ] );

        $scheduleSecond = Schedule::create( [
            'work_start'   => '09:00',
            'work_end'     => '18:00',
            'dinner_start' => '12:00',
            'dinner_end'   => '13:00',
        ] );

        $employeeFirst  = Employee::create( [
            'name'        => 'Работник 1',
            'schedule_id' => $scheduleFirst->id,
        ] );
        $employeeSecond = Employee::create( [
            'name'        => 'Работник 2',
            'schedule_id' => $scheduleSecond->id,
        ] );

        foreach ( [ '2018', '2019', '2020' ] as $year ) {
            Vacation::create( [
                'start'       => $year . '-01-11 00:00:00',
                'end'         => $year . '-01-25 00:00:00',
                'employee_id' => $employeeFirst->id,
            ] );
            Vacation::create( [
                'start'       => $year . '-02-01 00:00:00',
                'end'         => $year . '-02-15 00:00:00',
                'employee_id' => $employeeFirst->id,
            ] );

            Vacation::create( [
                'start'       => $year . '-02-01 00:00:00',
                'end'         => $year . '-03-01 00:00:00',
                'employee_id' => $employeeSecond->id,
            ] );
        }
        CorporateParty::create( [
            'start' => '2018-01-10 15:00:00',
            'end'   => '2018-01-11 00:00:00',
        ] );
    }
}
