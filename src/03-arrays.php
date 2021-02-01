<?php
/**
 * The $input variable contains an array of digits
 * Return an array which will contain the same digits but repetitive by its value
 * without changing the order.
 * Example: [1,3,2] => [1,3,3,3,2,2]
 *
 * @param array $input
 * @return array
 */
function repeatArrayValues(array $input)
{
    $newArr = [];

    foreach ($input as $item) {
        for ($i = 0; $i < $item; $i++) {
            $newArr[] = $item;
        }
    }

    return $newArr;
}

/**
 * The $input variable contains an array of digits
 * Return the lowest unique value or 0 if there is no unique values or array is empty.
 * Example: [1, 2, 3, 2, 1, 5, 6] => 3
 *
 * @param array $input
 * @return int
 */
function getUniqueValue(array $input)
{
    $uniqueValuesArr = array_diff($input, array_diff_assoc($input, array_unique($input)));

    if (count($uniqueValuesArr) == 0) {
        return 0;
    } else {
        return min($uniqueValuesArr);
    }
}

/**
 * The $input variable contains an array of arrays
 * Each sub array has keys: name (contains strings), tags (contains array of strings)
 * Return the list of names grouped by tags
 * !!! The 'names' in returned array must be sorted ascending.
 *
 * Example:
 * [
 *  ['name' => 'potato', 'tags' => ['vegetable', 'yellow']],
 *  ['name' => 'apple', 'tags' => ['fruit', 'green']],
 *  ['name' => 'orange', 'tags' => ['fruit', 'yellow']],
 * ]
 *
 * Should be transformed into:
 * [
 *  'fruit' => ['apple', 'orange'],
 *  'green' => ['apple'],
 *  'vegetable' => ['potato'],
 *  'yellow' => ['orange', 'potato'],
 * ]
 *
 * @param array $input
 * @return array
 */
function groupByTag(array $input)
{

    array_multisort($input);

    $newArr =[];

    foreach ($input as $item){

        $newArr = array_merge_recursive($newArr, array_combine ( $item['tags'] , array_fill(0, count($item['tags']), [$item['name']])));

    }

    ksort($newArr);

    return $newArr;

}