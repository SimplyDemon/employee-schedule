<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CorporateParty;
use App\Models\Employee;
use App\Models\Vacation;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;

class ScheduleController extends Controller {
    public function workTime( Request $request ) {
        $startDate  = $request->input( 'startDate' );
        $endDate    = $request->input( 'endDate' );
        $employeeId = $request->input( 'userId' );

        if ( empty( $startDate ) || empty( $endDate ) || empty( $employeeId ) ) {
            return response( 'Validation was not passed', 400 );
        }

        $employee = Employee::findOrFail( $employeeId );
        /*
         * Find all employee vacations which contacts with $startDate and $endDate
         *
         * 1st where - where $startDate between start or end vacation
         * 2nd where - where $endDate between start or end vacation
         * 3rd where - where start vacation between $startDate and end $endDate
         */
        $vacations        = Vacation::where( function ( $query ) use ( $employee, $startDate, $endDate ) {
            $query->where( 'employee_id', $employee->id )->where( 'start', '<=', $startDate )->where( 'end', '>=', $startDate );
        } )->orWhere( function ( $query ) use ( $employee, $startDate, $endDate ) {
            $query->where( 'employee_id', $employee->id )->where( 'start', '<=', $endDate )->where( 'end', '>=', $endDate );
        } )->orWhere( function ( $query ) use ( $employee, $startDate, $endDate ) {
            $query->where( 'employee_id', $employee->id )->whereBetween( 'start', [ $startDate, $endDate ] );
        } )->get();
        $employeeSchedule = $employee->schedule;

        $corporateParties = CorporateParty::where( function ( $query ) use ( $startDate, $endDate ) {
            $query->where( 'start', '<=', $startDate )->where( 'end', '>=', $startDate );
        } )->orWhere( function ( $query ) use ( $startDate, $endDate ) {
            $query->where( 'start', '<=', $endDate )->where( 'end', '>=', $endDate );
        } )->orWhere( function ( $query ) use ( $startDate, $endDate ) {
            $query->whereBetween( 'start', [ $startDate, $endDate ] );
        } )->get();


        $workStart   = $employeeSchedule->work_start;
        $workEnd     = $employeeSchedule->work_end;
        $dinnerStart = $employeeSchedule->dinner_start;
        $dinnerEnd   = $employeeSchedule->dinner_end;


        $schedule = [];
        $begin    = new DateTime( $startDate );
        $end      = new DateTime( $endDate );


        $interval = DateInterval::createFromDateString( '1 day' );
        $period   = new DatePeriod( $begin, $interval, $end );
        $i        = 0;
        foreach ( $period as $day ) {
            // Check is day off
            if ( file_get_contents( 'https://isdayoff.ru/' . $day->format( 'Ymd' ) ) ) {
                continue;
            }


            if ( ! empty( $vacations ) ) {
                foreach ( $vacations as $vacation ) {
                    $currentDate = $day->format( 'Y-m-d' );
                    if ( $currentDate >= $vacation->start && $currentDate <= $vacation->end ) {
                        continue 2;
                    }

                }
            }

            // I am so sorry
            if ( ! empty( $corporateParties ) ) {
                foreach ( $corporateParties as $party ) {
                    $currentDate = $day->format( 'Y-m-d' );

                    if ( $currentDate >= date( 'Y-m-d', strtotime( $party->start ) ) && $currentDate <= date( 'Y-m-d', strtotime( $party->end ) ) ) {
                        $partyStartTime = date( 'H:i', strtotime( $party->start ) );
                        $partyEndTime   = date( 'H:i', strtotime( $party->end ) );

                        $isPartyStarted = false;
                        $isPartyEnded   = false;
                        // is party start in first part of day
                        if ( $partyStartTime < $dinnerEnd ) {
                            $isPartyStarted = true;
                            // Check party start on work start?
                            if ( $workStart == $partyStartTime ) {
                                // Is party ends after dinner start? Then don't work first part of day
                                if ( $partyEndTime > $dinnerEnd ) {
                                    $workStart = false;
                                } // Is Party end in dinner time
                                else if ( $partyEndTime >= $dinnerStart ) {
                                    $workStart    = false;
                                    $isPartyEnded = true;
                                } // Party end in first part of day
                                else {
                                    $workStart    = $partyEndTime;
                                    $isPartyEnded = true;
                                }
                            } else {
                                $dinnerStart = $partyStartTime;
                                // Party end in first part of day
                                if ( $partyEndTime <= $dinnerEnd ) {
                                    $isPartyEnded = true;
                                }
                            }
                        }

                        // Party started on first part of day and not ended
                        if ( $isPartyStarted && ! $isPartyEnded ) {
                            // Party end after work end
                            if ( $partyEndTime >= $workEnd ) {
                                $workEnd = false;
                            } // Work start after party ends
                            else {
                                $dinnerEnd = $partyEndTime;
                            }
                        } // Party starts in second part of day
                        else if ( ! $isPartyStarted ) {
                            if ( $dinnerEnd == $partyStartTime ) {
                                // Is party ends after work end? Then don't work second part of day
                                if ( $partyEndTime >= $workEnd ) {
                                    $workEnd = false;
                                } // Start work after party end
                                else {
                                    $dinnerEnd = $partyEndTime;
                                }
                            } // Party start in second time of day and in work hours
                            else if ( $partyStartTime < $workEnd ) {
                                $workEnd = $partyStartTime;
                            }
                        }
                    }

                }
            }


            $schedule['schedule'][ $i ]['day'] = $day->format( 'Y-m-d' );
            if ( $workStart ) {
                $schedule['schedule'][ $i ]['timeRanges'][] = [
                    'start' => $workStart,
                    'end'   => $dinnerStart,
                ];
            }
            if ( $workEnd ) {
                $schedule['schedule'][ $i ]['timeRanges'][] = [
                    'start' => $dinnerEnd,
                    'end'   => $workEnd,
                ];
            }

            $i ++;
        }


        return $schedule;
    }
}
