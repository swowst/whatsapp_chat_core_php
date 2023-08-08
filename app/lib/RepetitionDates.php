<?php

namespace Lib;


class RepetitionDates
{
    public static $oneDayUnixTime = 86400;
    public static $oneWeekUnixTime = 7 * 86400;
    public static $oneYearUnixTime = 365 * 86400;

    // MAIN FUNCTINOS
    public static function getRepetationDates($params)
    {
        $params = self::filterParams($params);
        $result = [];
        $repeatCount = 0;
        $c = 0;
        $i = $params['dtStart'];

//        if ($params['isRecur'] && in_array($params['endType'], ['last_date', 'never'])) {
//            $i = self::calculateCalendarStartDate($params['from'], $params['dtStart'], $params['freq'], $params['interval']);
//
//            if ($params['endValue'] != '' && strtotime('today', strtotime($params['endValue'])) < strtotime('today', $i)) {
//                return $result;
//            }
//        }


        if ($params['isRecur']) {
            switch ($params['freq']) {
                case 'day':
                    while ($params['untilType'] == 'date' ? $i < $params['until'] : $repeatCount < $params['until']) {

                        $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                        $repeatCount++;
                        if ($endRepeat)
                            break;

                        $max = $i + ($params['duration'] * self::$oneDayUnixTime);

                        $result = array_merge($result, self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']));

                        $i += self::$oneDayUnixTime * $params['interval'];
                    }
                    break;
                case 'week':
                    $s = $i;
                    $mondayThisWeek = strtotime('monday this week', $i);
                    $i = strtotime(date('Y-m-d ' . date('H:i:s', $i), $mondayThisWeek));
                    $endOfWeek = strtotime('monday next week', $i);

                    while ($params['untilType'] == 'date' ? $i < $params['until'] : $repeatCount < $params['until']) {

                        if ($c > 0) {
                            $s = $i;
                            $endOfWeek = $i + self::$oneWeekUnixTime;
                        }

                        while ($s < $endOfWeek) {
                            $w = date("w", $s);
                            if ($w == 0) {
                                $w = 7;
                            }

                            if (in_array($w, $params['byWeekDay'])) {
                                $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $s, $params['duration']);

                                $endGeneration = self::checkCalendarEnds($params['untilType'], $params['until'], $repeatCount, $s, $params['duration']);

                                $repeatCount++;
                                if ($endRepeat || $endGeneration)
                                    break 2;

                                $max = $s + ($params['duration'] * self::$oneDayUnixTime);

                                $result = array_merge($result, self::getResultDates($s, $max, $params['dtEnd'], $params['from'], $params['exceptDays']));
                            }
                            $s += self::$oneDayUnixTime;
                        }

                        $i += $params['interval'] * self::$oneWeekUnixTime;
                        $c++;
                    }
                    break;
                case 'month':
                    while ($params['untilType'] == 'date' ? $i < $params['until'] : $repeatCount < $params['until']) {
                        $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                        $repeatCount++;
                        if ($endRepeat)
                            break;

                        $max = $i + ($params['duration'] * self::$oneDayUnixTime);
                        $result = array_merge($result, self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']));

                        $i = self::safeMonthAdd($i, $params['dtStart'],$params['interval'],$params['monthOption']);
                    }
                    break;
                case 'year':
                    while ($params['untilType'] == 'date' ? $i < $params['until'] : $repeatCount < $params['until']) {

                        if (date('d', $i) === date('d', $params['dtStart']) && date('m', $i) === date('m', $params['dtStart'])) {

                            $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                            $repeatCount++;
                            if ($endRepeat)
                                break;

                            $max = $i + ($params['duration'] * self::$oneDayUnixTime);

                            $result = array_merge($result, self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']));
                        }
                        $c += $params['interval'];
                        $i = strtotime('+' . $c . ' year', $params['dtStart']);
                    }
                    break;
            }
        } else {
            $max = $i + ($params['duration'] * self::$oneDayUnixTime);
            $result = array_merge($result, self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']));
        }

        return $result;
    }

    public static function getLastRepeatDate($params)
    {
        $lastRepeatDate = false;
        $params = self::filterParams($params);

        $repeatCount = 0;
        $c = 0;
        $i = $params['dtStart'];


        if ($params['isRecur'] && $params['endType'] != 'never') {
            switch ($params['freq']) {
                case 'day':
                    while (true) {
                        $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                        $repeatCount++;
                        if ($endRepeat)
                            break;

                        $max = $i + ($params['duration'] * self::$oneDayUnixTime);

                        $repeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                        if (count($repeatDate) > 0)
                            $lastRepeatDate = $repeatDate;

                        $i += self::$oneDayUnixTime * $params['interval'];
                    }
                    break;
                case 'week':
                    $mondayThisWeek = strtotime('monday this week', $params['dtStart']);
                    $i = strtotime(date('Y-m-d ' . date('H:i:s', $params['dtStart']), $mondayThisWeek));
                    $endOfWeek = strtotime('monday next week', $params['dtStart']);
                    $s = $params['dtStart'];

                    while (true) {
                        if ($c > 0) {
                            $s = $i;
                            $endOfWeek = $i + self::$oneWeekUnixTime;
                        }

                        while ($s < $endOfWeek) {
                            $w = date("w", $s);
                            if ($w == 0) {
                                $w = 7;
                            }

                            if (in_array($w, $params['byWeekDay'])) {
                                $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $s, $params['duration']);
                                $repeatCount++;
                                if ($endRepeat)
                                    break 2;

                                $max = $s + ($params['duration'] * self::$oneDayUnixTime);

                                $repeatDate = self::getResultDates($s, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                                if (count($repeatDate) > 0)
                                    $lastRepeatDate = $repeatDate;
                            }
                            $s += self::$oneDayUnixTime;
                        }

                        $i += $params['interval'] * self::$oneWeekUnixTime;
                        $c++;
                    }
                    break;
                case 'month':
                    while (true) {
                        if (date('d', $i) === date('d', $params['dtStart'])) {

                            $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                            $repeatCount++;
                            if ($endRepeat)
                                break;

                            $max = $i + ($params['duration'] * self::$oneDayUnixTime);

                            $repeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                            if (count($repeatDate) > 0)
                                $lastRepeatDate = $repeatDate;
                        }

                        $i = self::safeMonthAdd($i, $params['dtStart'],$params['interval'],$params['monthOption']);
                    }
                    break;
                case 'year':
                    while (true) {
                        if (date('d', $i) === date('d', $params['dtStart']) && date('m', $i) === date('m', $params['dtStart'])) {

                            $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                            $repeatCount++;
                            if ($endRepeat)
                                break;

                            $max = $i + ($params['duration'] * self::$oneDayUnixTime);

                            $repeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                            if (count($repeatDate) > 0)
                                $lastRepeatDate = $repeatDate;
                        }
                        $c += $params['interval'];
                        $i = strtotime('+' . $c . ' year', $params['dtStart']);
                    }
                    break;
            }
            $lastRepeatDate = isset($lastRepeatDate[0]) ? $lastRepeatDate[0] : $lastRepeatDate;
        }

        return $lastRepeatDate;
    }

    public static function getNextRepeatDate($params)
    {
        $params = self::filterParams($params);
        $nextRepeatDate = false;

        $repeatCount = 0;
        $c = 0;
        $i = $params['dtStart'];

        if ($params['isRecur']) {
            switch ($params['freq']) {
                case 'day':
                    while (true) {
                        $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                        $repeatCount++;
                        if ($endRepeat)
                            break;

                        if ($i >= $params['from']) {
                            $max = $i + ($params['duration'] * self::$oneDayUnixTime);
                            $nextRepeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                            if (count($nextRepeatDate) > 0)
                                break;
                        }

                        $i += self::$oneDayUnixTime * $params['interval'];
                    }
                    break;
                case 'week':
                    $mondayThisWeek = strtotime('monday this week', $params['dtStart']);
                    $i = strtotime(date('Y-m-d ' . date('H:i:s', $params['dtStart']), $mondayThisWeek));
                    $endOfWeek = strtotime('monday next week', $params['dtStart']);
                    $s = $params['dtStart'];

                    while (true) {
                        if ($c > 0) {
                            $s = $i;
                            $endOfWeek = $i + self::$oneWeekUnixTime;
                        }

                        while ($s < $endOfWeek) {
                            $w = date("w", $s);
                            if ($w == 0) {
                                $w = 7;
                            }

                            if (in_array($w, $params['byWeekDay'])) {
                                $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $s, $params['duration']);
                                $repeatCount++;
                                if ($endRepeat)
                                    break 2;

                                if ($s >= $params['from']) {
                                    $max = $s + ($params['duration'] * self::$oneDayUnixTime);
                                    $nextRepeatDate = self::getResultDates($s, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                                    if (count($nextRepeatDate) > 0)
                                        break 2;
                                }
                            }
                            $s += self::$oneDayUnixTime;
                        }

                        $i += $params['interval'] * self::$oneWeekUnixTime;
                        $c++;
                    }
                    break;
                case 'month':
                    while (true) {
                        if (date('d', $i) === date('d', $params['dtStart'])) {

                            $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                            $repeatCount++;
                            if ($endRepeat)
                                break;

                            if ($i >= $params['from']) {
                                $max = $i + ($params['duration'] * self::$oneDayUnixTime);
                                $nextRepeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                                if (count($nextRepeatDate) > 0)
                                    break;
                            }
                        }
                        $i = self::safeMonthAdd($i, $params['dtStart'],$params['interval'],$params['monthOption']);
                    }
                    break;
                case 'year':
                    while (true) {
                        if (date('d', $i) === date('d', $params['dtStart']) && date('m', $i) === date('m', $params['dtStart'])) {

                            $endRepeat = self::checkCalendarEnds($params['endType'], $params['endValue'], $repeatCount, $i, $params['duration']);
                            $repeatCount++;
                            if ($endRepeat)
                                break;

                            if ($i >= $params['from']) {
                                $max = $i + ($params['duration'] * self::$oneDayUnixTime);
                                $nextRepeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
                                if (count($nextRepeatDate) > 0)
                                    break;
                            }
                        }
                        $c += $params['interval'];
                        $i = strtotime('+' . $c . ' year', $params['dtStart']);
                    }
                    break;
            }
        } else {
            if ($i >= $params['from']) {
                $max = $i + ($params['duration'] * self::$oneDayUnixTime);
                $nextRepeatDate = self::getResultDates($i, $max, $params['dtEnd'], $params['from'], $params['exceptDays']);
            }
        }

        if ($nextRepeatDate)
            $nextRepeatDate = $nextRepeatDate[0];

        return $nextRepeatDate;
    }


    // HELPER FUNCTIONS
    public static function filterParams($params)
    {
        $duration = $params['duration'];
        $dtStart = $params['dtStart'] ? strtotime($params['dtStart']) : false;
        $dtEnd = $params['dtEnd'] ? strtotime($params['dtEnd']) : false;

        if (!$dtStart && !$dtEnd) {
            $dtStart = time();
            $dtEnd = time();
        } elseif ($dtStart && !$dtEnd) {
            $dtEnd = $dtStart;
        } elseif (!$dtStart && $dtEnd) {
            $dtStart = $dtEnd;
        }

        if (!$duration) {
            $duration = (strtotime('today', $dtEnd) - strtotime('today', $dtStart)) / self::$oneDayUnixTime;
        }

        $until = time() + self::$oneYearUnixTime;

        if (isset($params['until'])) {
            if (is_numeric($params['until'])) {
                $until = (int)$params['until'];
            } else {
                //end of day
                $until = strtotime("tomorrow", strtotime($params['until'])) - 1;
            }
        }

        return [
            'isRecur' => $params['isRecur'] ? true : false,
            'freq' => in_array($params['freq'], ['day', 'week', 'month', 'year']) ? $params['freq'] : 'day',
            'interval' => $params['interval'] ? $params['interval'] : 1,
            'duration' => $duration > 0 ? $duration : 0,
            'dtStart' => $dtStart,
            'dtEnd' => $dtEnd,
            'endType' => $params['endType'] ? $params['endType'] : 'never',
            'endValue' => $params['endValue'],
            'monthOption' => $params['monthOption'] ?? 2,
            'byWeekDay' => (array)$params['byWeekDay'] ? (array)$params['byWeekDay'] : [1],
            'untilType' => $params['untilType'] ? $params['untilType'] : 'date',
            'until' => $until,
            'from' => $params['from'] ? strtotime($params['from']) : $dtStart,
            'exceptDays' => is_array($params['exceptDays']) ? $params['exceptDays'] : [],
        ];
    }

    public static function calculateCalendarStartDate($from, $dtStart, $freq, $interval)
    {

        $timeBetween = $from - $dtStart;
        $currentStartDate = $dtStart;

        if ($timeBetween > 0 && in_array($freq, ['day', 'week', 'month', 'year'])) {

            if ($freq === 'day') {
                $daysBetween = (strtotime('today', $from) - strtotime('today', $dtStart)) / self::$oneDayUnixTime;
                $currentStartDate = $dtStart + ($daysBetween - ($daysBetween % $interval)) * self::$oneDayUnixTime;
            } else if ($freq === 'week') {
                $daysBetween = (strtotime('monday this week', $from) - strtotime('monday this week', $dtStart)) / self::$oneDayUnixTime;
                $addDays = $daysBetween >= ($interval * 7) ? $daysBetween - ($daysBetween % $interval * 7) : 0;
                $currentStartDate = strtotime('+' . $addDays . ' days monday this week', $dtStart);

            } else if ($freq === 'month') {
                $yearsBetween = date('Y', $from) - date('Y', $dtStart);
                $yearsCount = $yearsBetween > 0 ? $yearsBetween : 0;
                $monthsBetween = date('m', $from) * 1 + ($yearsCount * 12) - date('m', $dtStart);
                $monthsCount = $monthsBetween > 0 ? $monthsBetween : 0;
                $addMonth = $monthsCount >= $interval ? $monthsCount - ($monthsCount % $interval) : 0;
                $currentStartDate = strtotime('+' . $addMonth . ' month', $dtStart);

                $actualMont = date('m', strtotime('first day of +' . $addMonth . ' month', $dtStart));
                if (date('m', $currentStartDate) != $actualMont) {
                    $currentStartDate = strtotime(date('Y-' . $actualMont . '-d  H:i:s', $currentStartDate));
                }

                if (date("d", $dtStart) !== date("d", $currentStartDate)) {
                    $loopCount = 0;
                    $storedStartDate = $currentStartDate;
                    while (date("d", $dtStart) !== date("d", $currentStartDate)) {
                        $loopCount++;
                        $currentStartDate = strtotime('+' . $interval * $loopCount . ' month', $storedStartDate);
                        $currentStartDate = strtotime(date('Y-m-' . date('d', $dtStart) . ' H:i:s', $currentStartDate));
                    }
                }
            } else if ($freq === 'year') {
                $yearsBetween = date('Y', $from) - date('Y', $dtStart);
                $yearsCount = $yearsBetween > 0 ? $yearsBetween : 0;
                $addYears = $yearsCount >= $interval ? $yearsCount - ($yearsCount % $interval) : 0;
                $currentStartDate = strtotime('+' . $addYears . ' year', $dtStart);
            }
        }

        return $currentStartDate;
    }

    public static function checkCalendarEnds($endType, $endValue, $repeatCount, $calendarDate, $duration)
    {
        $calendarEnded = false;
        $calendarEndDate = $calendarDate + ($duration * 86400);

        if ($endType === 'after') {
            if ($repeatCount >= $endValue)
                $calendarEnded = true;
        } else if ($endType === 'date') {
            if (strtotime('today', strtotime($endValue)) < strtotime('today', $calendarDate))
                $calendarEnded = true;
        } else if ($endType === 'last_date') {
            if (strtotime('today', strtotime($endValue)) < strtotime('today', $calendarEndDate))
                $calendarEnded = true;
        }

        return $calendarEnded;
    }

    public static function getResultDates($eventStartDate, $eventEndDate, $dtEnd, $from, $exceptDays = [])
    {
        $result = [];
        $eventStartDateString = date('Y-m-d H:i:s', $eventStartDate);
        $dataEndDateString = date('Y-m-d', $eventEndDate) . ' ' . date('H:i:s', $dtEnd);

        if ($eventEndDate >= $from && !in_array(date('Y-m-d', $eventStartDate), $exceptDays)) {
            $result[] = [
                'start' => $eventStartDateString,
                'end' => $dataEndDateString,
            ];
        }

        return $result;
    }

    public static function seperateEventDatesDayByDay($eventStartDate, $eventEndDate, $from, $until)
    {
        $result = [];

        $eventStartDate = strtotime($eventStartDate);
        $eventEndDate = strtotime($eventEndDate);
        $from = strtotime($from);
        $until = strtotime($until);

        if ($until != 0 && $from != 0) {
            while ($eventStartDate <= $eventEndDate && $eventStartDate < $until) {
                if ($eventStartDate >= $from) {
                    $dataEndTime = strtotime('tomorrow', $eventStartDate) - 1;
                    if (strtotime('today', $eventStartDate) === strtotime('today', $eventEndDate)) {
                        $dataEndTime = $eventEndDate;
                    }

                    $result[] = [
                        'start' => date('Y-m-d H:i:s', $eventStartDate),
                        'end' => date('Y-m-d H:i:s', $dataEndTime),
                    ];
                }
                $eventStartDate = strtotime('today', $eventStartDate);
                $eventStartDate += self::$oneDayUnixTime;
            }
        }

        return $result;
    }




    public static function safeMonthAdd($currentStart,$start,$interval,$option = 0)
    {
        $repeatStartDay = date("d", $start);

        $storedStartDate = $currentStart;
        $i = strtotime('+' . $interval . ' month', $currentStart);

        if ($option == 0){
            if ($repeatStartDay !== date("d", $i)) {
                $loopCount = 0;
                while ($repeatStartDay !== date("d", $i)){
                    $loopCount++;
                    $i = strtotime('+' . $loopCount*$interval . ' month', $storedStartDate);
                }
            }
        }else if($option == 1){
            $weekNumberOfStart = self::weekOfMonth($start);

            $nextMonth = strtotime('first day of next month',$currentStart);
            $nextMonthWeekNum = self::weekOfMonth($nextMonth);

            $day = date('w',$start);
            $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday');

            while ($weekNumberOfStart !== $nextMonthWeekNum){
                $checkNextMonth = strtotime('monday next week',$nextMonth);

                if (date('m',$checkNextMonth) == date('m',$nextMonth)){
                    $nextMonth = $checkNextMonth;
                    $nextMonthWeekNum = self::weekOfMonth($nextMonth);
                }else{
                    break;
                }
            }

            $i = strtotime($days[$day], $nextMonth);

        }else if ($option == 2){
            if ($repeatStartDay !== date("d", $i)) {
                $i = strtotime('last day of '. $interval .' month', $storedStartDate);
            }
        }
        return $i;
    }


    public static function weekOfMonth($date) {
        //Get the first day of the month.
        $firstOfMonth = strtotime(date("Y-m-01", $date));
        //Apply above formula.
        return self::weekOfYear($date) - self::weekOfYear($firstOfMonth) + 1;
    }

    public static function weekOfYear($date) {
        $weekOfYear = intval(date("W", $date));
        if (date('n', $date) == "1" && $weekOfYear > 51) {
            // It's the last week of the previos year.
            $weekOfYear = 0;
        }
        return $weekOfYear;
    }



}
