<?php
/**
 * The $airports variable contains array of arrays of airports (see airports.php)
 * What can be put instead of placeholder so that function returns the unique first letter of each airport name
 * in alphabetical order
 *
 * Create a PhpUnit test (GetUniqueFirstLettersTest) which will check this behavior
 *
 * @param  array  $airports
 * @return string[]
 */
function getUniqueFirstLetters(array $airports)
{

    $stateArr = array_column($airports, 'name');

    $stateFirstLetter = array_map( fn($el) => substr($el,0,1), $stateArr);

    $uniqueFirstLetters = array_unique($stateFirstLetter);

    array_multisort($uniqueFirstLetters);

    return $uniqueFirstLetters ;

}