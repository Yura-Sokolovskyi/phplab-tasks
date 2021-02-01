<?php
/**
 * The $input variable contains text in snake case (i.e. hello_world or this_is_home_task)
 * Transform it into a camel-cased string and return (i.e. helloWorld or thisIsHomeTask)
 * @see http://xahlee.info/comp/camelCase_vs_snake_case.html
 *
 * @param string $input
 * @return string
 */
function snakeCaseToCamelCase(string $input)
{
    $newStr = '';

    foreach (explode('_', $input) as $key => $str) {
        if ($key == 0) {
            $newStr .= lcfirst($str);
        } else {
            $newStr .= ucfirst($str);
        }
    }

    return $newStr;
}

/**
 * The $input variable contains multibyte text like 'ФЫВА олдж'
 * Mirror each word individually and return transformed text (i.e. 'АВЫФ ждло')
 * !!! do not change words order
 *
 * @param string $input
 * @return string
 */
function mirrorMultibyteString(string $input)
{
    $stringsArr = explode(' ', mb_convert_encoding($input, "windows-1251", "UTF-8"));

    $reversedStringsArr = [];

    foreach ($stringsArr as $str) {
        $reversedStringsArr[] = strrev($str);
    }

    return mb_convert_encoding(implode(' ', $reversedStringsArr), "UTF-8", "windows-1251");
}

/**
 * My friend wants a new band name for her band.
 * She likes bands that use the formula: 'The' + a noun with the first letter capitalized.
 * However, when a noun STARTS and ENDS with the same letter,
 * she likes to repeat the noun twice and connect them together with the first and last letter,
 * combined into one word like so (WITHOUT a 'The' in front):
 * dolphin -> The Dolphin
 * alaska -> Alaskalaska
 * europe -> Europeurope
 * Implement this logic.
 *
 * @param string $noun
 * @return string
 */
function getBrandName(string $noun)
{
    if (substr($noun, 0, 1) == substr($noun, -1, 1)) {
        return substr(ucfirst($noun),0,-1) . lcfirst($noun) ;
    } else {
        return "The " . ucfirst($noun);
    }
}