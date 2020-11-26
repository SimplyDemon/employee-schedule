<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScheduleWorkTimeTest extends TestCase {

    public function testPositive() {
        $response = $this->call( 'GET', 'schedule', [
            'startDate' => '2018-01-01',
            'endDate'   => '2018-01-14',
            'userId'    => '1',
        ] );
        $response->assertOk();
        $response->assertJson( json_decode( '{"schedule":[{"day":"2018-01-09","timeRanges":[{"start":"10:00","end":"13:00"},{"start":"14:00","end":"19:00"}]},{"day":"2018-01-10","timeRanges":[{"start":"10:00","end":"13:00"},{"start":"14:00","end":"15:00"}]}]}', true ) );

        $response = $this->call( 'GET', 'schedule', [
            'startDate' => '2018-01-25',
            'endDate'   => '2018-02-05',
            'userId'    => '2',
        ] );

        $response->assertOk();
        $response->assertJson( json_decode( '{"schedule":[{"day":"2018-01-25","timeRanges":[{"start":"9:00","end":"12:00"},{"start":"13:00","end":"18:00"}]},{"day":"2018-01-26","timeRanges":[{"start":"9:00","end":"12:00"},{"start":"13:00","end":"18:00"}]},{"day":"2018-01-29","timeRanges":[{"start":"9:00","end":"12:00"},{"start":"13:00","end":"18:00"}]},{"day":"2018-01-30","timeRanges":[{"start":"9:00","end":"12:00"},{"start":"13:00","end":"18:00"}]},{"day":"2018-01-31","timeRanges":[{"start":"9:00","end":"12:00"},{"start":"13:00","end":"18:00"}]}]}', true ) );
    }

    public function testNegative() {
        $response = $this->call( 'GET', 'schedule', [
            'endDate' => '2018-01-14',
            'userId'  => '1',
        ] );
        $response->assertStatus( 400 );

        $response = $this->call( 'GET', 'schedule', [
            'startDate' => '2018-01-25',
            'endDate'   => '2018-02-05',
            'userId'    => '999999',
        ] );
        $response->assertStatus( 404 );

        $response = $this->call( 'GET', 'schedule' );
        $response->assertStatus( 400 );

    }
}
