<?php
require_once './functions.php';

$airports = require './airports.php';

/** Start the session */

session_start();


/** @const Global constant description */

define('PAGE_NUMBER_PARAMETER', 'page');
define('SORT_PARAMETER', 'sort');
define('FILTER_BY_STATE_PARAMETER', 'filter_by_state');
define('FILTER_BY_FIRST_LETTER_PARAMETER', 'filter_by_first_letter');
define('RESET_PARAMETER', 'reset');
define('AIRPORTS_PER_PAGE', 5);
define('FILTER_BY_FIRST_LETTER_FUNCTION', 'filterByFirstLetter');
define('FILTER_BY_STATE_FUNCTION', 'filterByState');
define('SORT_FUNCTION', 'sortBy');
define('MAX_PAGE_BUTTONS_PER_PAGE', 3);
define('TWO_PAGES', 2);
define('ONE_PAGE', 1);


//Clear Filters

/**
 * If reset parameter set in url removes
 * all parameters saved in session
 */

if (isset($_GET[RESET_PARAMETER])) {
    unset($_SESSION[FILTER_BY_FIRST_LETTER_PARAMETER]);
    unset($_SESSION[FILTER_BY_STATE_PARAMETER]);
    unset($_SESSION[SORT_PARAMETER]);
    unset($_SESSION[PAGE_NUMBER_PARAMETER]);
}


// Filtering
/**
 * Here you need to check $_GET request if it has any filtering
 * and apply filtering by First Airport Name Letter and/or Airport State
 * (see Filtering tasks 1 and 2 below)
 */

/**
 * If filter by State set in url saves it to session variable
 */

if (isset($_GET["filter_by_state"])) {

    $_SESSION[FILTER_BY_STATE_PARAMETER] = $_GET[FILTER_BY_STATE_PARAMETER];

}

/**
 * If filter by First Letter set in url saves it to session variable
 */

if (isset($_GET[FILTER_BY_FIRST_LETTER_PARAMETER])) {

    $_SESSION[FILTER_BY_FIRST_LETTER_PARAMETER] = $_GET[FILTER_BY_FIRST_LETTER_PARAMETER];

}


// Sorting
/**
 * Here you need to check $_GET request if it has sorting key
 * and apply sorting
 * (see Sorting task below)
 */

/**
 * If sorting type set in url saves it to session variable
 */

if (isset($_GET[SORT_PARAMETER])) {

    $_SESSION[SORT_PARAMETER] = $_GET[SORT_PARAMETER];

}


// Pagination
/**
 * Here you need to check $_GET request if it has pagination key
 * and apply pagination logic
 * (see Pagination task below)
 */


/**
 * If page number set in url saves it to session variable
 * else if page number isn't set in url
 * and page number isn't set in session variable
 * sets it to 1 (first page)
 */

if (isset($_GET[PAGE_NUMBER_PARAMETER])) {

    $_SESSION[PAGE_NUMBER_PARAMETER] = $_GET[PAGE_NUMBER_PARAMETER];

} elseif (!isset($_SESSION[PAGE_NUMBER_PARAMETER])) {

    $_SESSION[PAGE_NUMBER_PARAMETER] = 1;

}

/**
 * Sorted and filtered airports
 */

$airportsAll = prepareData($airports);

/**
 * Number of airports
 */

$numberOfAirports = count($airportsAll);

/**
 * Number of pages
 */

$numberOfPages = $numberOfAirports % AIRPORTS_PER_PAGE > 0 ?
                 intdiv($numberOfAirports, AIRPORTS_PER_PAGE) + ONE_PAGE :
                 intdiv($numberOfAirports, AIRPORTS_PER_PAGE);

/**
 * Array of indexes for pagination buttons
 */

$pagesBtn = getPagesBtnArr($_SESSION[PAGE_NUMBER_PARAMETER], $numberOfPages);

/**
 * Airports for current page
 */

$airports = array_chunk($airportsAll, AIRPORTS_PER_PAGE)[$_SESSION[PAGE_NUMBER_PARAMETER] - 1];

/**
 * Creates array of indexes for pagination buttons
 *
 * @param  number  $page
 * @param  number  $numberOfPages
 * @return number[]
 */

function getPagesBtnArr($page, $numberOfPages)
{

    if (!(($page + ONE_PAGE) > $numberOfPages) and !(($page - ONE_PAGE) <= 0)) {

        return buildPagesBtnArr($page - ONE_PAGE, $page + ONE_PAGE);

    } elseif ($page == ONE_PAGE and $numberOfPages > MAX_PAGE_BUTTONS_PER_PAGE) {

        return buildPagesBtnArr($page, $page + TWO_PAGES);

    } elseif ($page == $numberOfPages and $numberOfPages > MAX_PAGE_BUTTONS_PER_PAGE) {

        return buildPagesBtnArr($numberOfPages - TWO_PAGES, $numberOfPages);

    } elseif ($numberOfPages == TWO_PAGES) {

        return buildPagesBtnArr(ONE_PAGE, $numberOfPages);

    } else {

        return [ONE_PAGE];

    }
}

/**
 * Creates array of numbers
 *
 * @param  number  $firstPageNum
 * @param  number  $lastPageNum
 * @return number[]
 */

function buildPagesBtnArr($firstPageNum, $lastPageNum)
{

    $pagesBtn = [];

    for ($i = $firstPageNum; $i <= $lastPageNum; $i++) {

        $pagesBtn[] = $i;
    }

    return $pagesBtn;

}

/**
 * Applies filters and sorting and creates
 * query parameters for the url
 *
 * Calls the buildUrl() function
 *
 * @param  array  $airports
 * @return array  $airports
 */

function prepareData($airports)
{

    $urlFilters = '';

    $filters = [
                FILTER_BY_FIRST_LETTER_PARAMETER     => FILTER_BY_FIRST_LETTER_FUNCTION,
                FILTER_BY_STATE_PARAMETER            => FILTER_BY_STATE_FUNCTION,
                SORT_PARAMETER                       => SORT_FUNCTION
                ];


    foreach ($filters as $filter => $func) {
        if (isset($_SESSION[$filter])) {
            $airports = call_user_func($func, $airports, $_SESSION[$filter]);
            $urlFilters .= '&' . $filter . '=' . $_SESSION[$filter];
            if ($filter != SORT_PARAMETER and !isset($_GET[PAGE_NUMBER_PARAMETER])) {
                $_SESSION[PAGE_NUMBER_PARAMETER] = 1;
            }
        }
    }

    buildUrl($urlFilters);

    return $airports;
}

/**
 * Builds url
 * if reset parameter set resets url to first page
 * else if the url is different from the url which exist in session
 * saves it to headers and session variable
 *
 * @param  string  $urlFilters
 */

function buildUrl($urlFilters)
{
    $url = 'LOCATION: http://' . $_SERVER['HTTP_HOST'] . '/?' . PAGE_NUMBER_PARAMETER .
            '=' . $_SESSION[PAGE_NUMBER_PARAMETER] . $urlFilters;

    if (isset($_GET[RESET_PARAMETER])){

        header('LOCATION: http://' . $_SERVER['HTTP_HOST'] . '/?' . PAGE_NUMBER_PARAMETER .
            '=' . $_SESSION[PAGE_NUMBER_PARAMETER]);


    } elseif ($_SESSION['url'] != $url) {

        header($url);
        $_SESSION['url'] = $url;

    }
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Airports</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<main role="main" class="container">

    <h1 class="mt-5">US Airports</h1>

    <!--
        Filtering task #1
        Replace # in HREF attribute so that link follows to the same page with the filter_by_first_letter key
        i.e. /?filter_by_first_letter=A or /?filter_by_first_letter=B

        Make sure, that the logic below also works:
         - when you apply filter_by_first_letter the page should be equal 1
         - when you apply filter_by_first_letter, than filter_by_state (see Filtering task #2) is not reset
           i.e. if you have filter_by_state set you can additionally use filter_by_first_letter
    -->
    <div class="alert alert-dark">
        Filter by first letter:

        <?php foreach (getUniqueFirstLetters($airportsAll) as $letter): ?>
            <a href="/?filter_by_first_letter=<?= $letter ?>"><?= $letter ?></a>
        <?php endforeach; ?>

        <a href="/?reset" class="float-right">Reset all filters</a>
    </div>

    <!--
        Sorting task
        Replace # in HREF so that link follows to the same page with the sort key with the proper sorting value
        i.e. /?sort=name or /?sort=code etc

        Make sure, that the logic below also works:
         - when you apply sorting pagination and filtering are not reset
           i.e. if you already have /?page=2&filter_by_first_letter=A after applying sorting the url should looks like
           /?page=2&filter_by_first_letter=A&sort=name
    -->



    <?php

    /**
     * Sorts (ASC) airports by field
     *
     * @param  array  $airports
     * @param  string  $field
     * @return array  $airports
     */

    function sortBy($airports, $field)
    {

        $field = array_column($airports, $field);

        array_multisort($field, SORT_ASC, $airports);

        return $airports;

    } ?>

    <table class="table">
        <thead>
        <tr>
            <th scope="col"><a href="/?sort=name">Name</a></th>
            <th scope="col"><a href="/?sort=code">Code</a></th>
            <th scope="col"><a href="/?sort=state">State</a></th>
            <th scope="col"><a href="/?sort=city">City</a></th>
            <th scope="col">Address</th>
            <th scope="col">Timezone</th>
        </tr>
        </thead>
        <tbody>
        <!--
            Filtering task #2
            Replace # in HREF so that link follows to the same page with the filter_by_state key
            i.e. /?filter_by_state=A or /?filter_by_state=B

            Make sure, that the logic below also works:
             - when you apply filter_by_state the page should be equal 1
             - when you apply filter_by_state, than filter_by_first_letter (see Filtering task #1) is not reset
               i.e. if you have filter_by_first_letter set you can additionally use filter_by_state
        -->

        <?php

        /**
         * Filters airports by state
         *
         * @param  array  $airports
         * @param  string  $state
         * @return array  $airports
         */

        function filterByState($airports, $state) {

            return array_filter($airports, function ($el) use ($state) {

                return $el['state'] == $state;

            });

        }

        /**
         * Filters airports by first letter of name
         *
         * @param  array  $airports
         * @param  string  $letter
         * @return array  $airports
         */

        function filterByFirstLetter($airports, $letter) {

            return array_filter($airports, function ($el) use ($letter) {

                return substr($el['name'], 0, 1) == $letter;

            });

        }

         foreach ($airports as $airport): ?>
            <tr>
                <td><?= $airport['name'] ?></td>
                <td><?= $airport['code'] ?></td>
                <td><a href="/?filter_by_state=<?= $airport['state'] ?>"><?= $airport['state'] ?></a></td>
                <td><?= $airport['city'] ?></td>
                <td><?= $airport['address'] ?></td>
                <td><?= $airport['timezone'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!--
        Pagination task
        Replace HTML below so that it shows real pages dependently on number of airports after all filters applied

        Make sure, that the logic below also works:
         - show 5 airports per page
         - use page key (i.e. /?page=1)
         - when you apply pagination - all filters and sorting are not reset
    -->
    <nav aria-label="Navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" href="/?page=1">&laquo;</a>
            </li>

            <?php foreach ($pagesBtn as $btn): ?>
                <li class="page-item <?= $btn == $_SESSION['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="/?page=<?= $btn ?>"><?= $btn ?></a>
                </li>
            <?php endforeach; ?>

            <li class="page-item">
                <a class="page-link" href="/?page=<?= $numberOfPages ?>">&raquo;</a>
            </li>
        </ul>
    </nav>

</main>
</html>
