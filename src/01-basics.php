<?php
/**
 * The $minute variable contains a number from 0 to 59 (i.e. 10 or 25 or 60 etc).
 * Determine in which quarter of an hour the number falls.
 * Return one of the values: "first", "second", "third" and "fourth".
 * Throw InvalidArgumentException if $minute is negative of greater than 60.
 * @see https://www.php.net/manual/en/class.invalidargumentexception.php
 *
 * @param int $minute
 * @return string
 * @throws InvalidArgumentException
 */
function getMinuteQuarter(int $minute)
{
    if ($minute < 0 || $minute > 60) {
        throw new InvalidArgumentException('getMinuteQuarter function only accepts minutes from 0 to 59. 
                                                     Input minute was: ' . $minute);
    } else if ($minute > 0 && $minute <= 15) {
        return "first";
    } else if ($minute > 15 && $minute <= 30) {
        return "second";
    } else if ($minute > 30 && $minute <= 45) {
        return "third";
    } else {
        return "fourth";
    }
}

/**
 * The $year variable contains a year (i.e. 1995 or 2020 etc).
 * Return true if the year is Leap or false otherwise.
 * Throw InvalidArgumentException if $year is lower than 1900.
 * @see https://en.wikipedia.org/wiki/Leap_year
 * @see https://www.php.net/manual/en/class.invalidargumentexception.php
 *
 * @param int $year
 * @return boolean
 * @throws InvalidArgumentException
 */
function isLeapYear(int $year)
{
    if ($year < 1900) {
        throw new InvalidArgumentException('isLeapYear function only accepts year grater then 1900. 
                                                     Input year was: ' . $year);

    } else if ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0) {
  //} else if (date('L', mktime(0, 0, 0, 1, 1, $year))) { Another solution

        return true;

    }

    return false;


}

/**
 * The $input variable contains a string of six digits (like '123456' or '385934').
 * Return true if the sum of the first three digits is equal with the sum of last three ones
 * (i.e. in first case 1+2+3 not equal with 4+5+6 - need to return false).
 * Throw InvalidArgumentException if $input contains more or less than 6 digits.
 * @see https://www.php.net/manual/en/class.invalidargumentexception.php
 *
 * @param string $input
 * @return boolean
 * @throws InvalidArgumentException
 */
function isSumEqual(string $input)
{
    if (strlen($input) != 6) {
        throw new InvalidArgumentException('isSumEqual function only accepts a string containing less than 
                                                     6 characters. Input was: ' . $input);
    } else {
        if (array_sum(str_split(substr($input, 0, 3))) === array_sum(str_split(substr($input, 3)))) {
            return true;
        } else {
            return false;
        }
    }
}