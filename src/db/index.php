<?php
/**
 * Connect to DB
 */
require_once './pdo_ini.php';

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
define('AIRPORTS_ALL_COLUMNS', 'a.name , a.code, a.address, a.timezone, c.name as city_name, s.name as state_name');
define('COUNT_COLUMN', 'count(*) as pages');
define('SORTING_PARAM_NAME', 'name');
define('SORTING_PARAM_CODE', 'code');
define('SORTING_PARAM_STATE', 'state');
define('SORTING_PARAM_CITY', 'city');
define('NAME_COLUMN', 'a.name');
define('CODE_COLUMN', 'a.code');
define('STATE_COLUMN', 's.name');
define('CITY_COLUMN', 'c.name');
define('SQL_FILTERING_PARAMETER', 'filter');
define('SQL_SORTING_PARAMETER', 'sort');

$SQLQueryParams = [
    'pagination' => '',
    'params' => ''
];


/**
 * SELECT the list of unique first letters using https://www.w3resource.com/mysql/string-functions/mysql-left-function.php
 * and https://www.w3resource.com/sql/select-statement/queries-with-distinct.php
 * and set the result to $uniqueFirstLetters variable
 */


function getUniqueLetters($pdo, $columns, $filterParams)
{

    $sth = $pdo->prepare(sprintf("SELECT DISTINCT LEFT(o.name, 1) as letter  FROM 
                                (SELECT %s
                                FROM airports as a 
                                LEFT JOIN cities as c ON a.city_id = c.id 
                                LEFT JOIN states as s ON a.state_id = s.id
                                %s) as o  
                                ORDER BY letter",$columns,$filterParams));
    $sth->setFetchMode(\PDO::FETCH_ASSOC);
    $sth->execute();
    $lettersArr = $sth->fetchAll();

    $uniqueFirstLetters = [];

    foreach ($lettersArr as $letter) {
        $uniqueFirstLetters[] = $letter['letter'];
    }

    return $uniqueFirstLetters;
}




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
 *
 * For filtering by first_letter use LIKE 'A%' in WHERE statement
 * For filtering by state you will need to JOIN states table and check if states.name = A
 * where A - requested filter value
 */

if (isset($_GET[FILTER_BY_STATE_PARAMETER])) {

    $_SESSION[FILTER_BY_STATE_PARAMETER] =  $_GET[FILTER_BY_STATE_PARAMETER];

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
 *
 * For sorting use ORDER BY A
 * where A - requested filter value
 */

if (isset($_GET[SORT_PARAMETER])) {

    $_SESSION[SORT_PARAMETER] = $_GET[SORT_PARAMETER];

}

// Pagination
/**
 * Here you need to check $_GET request if it has pagination key
 * and apply pagination logic
 * (see Pagination task below)
 *
 * For pagination use LIMIT
 * To get the number of all airports matched by filter use COUNT(*) in the SELECT statement with all filters applied
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

/* Setting filtering and sorting parameters */

$SQLQueryParams['params'] = prepareParameters(parseUrl());


/** Builds filtering and sorting parameters
 * for SQL query
 *
 *
 * @param array $parameters
 * @return string
 */


function prepareParameters($parameters)
{

    if (count($parameters) > 0) {

        $preparedParameters = 'WHERE ';
        $prevKey = '';

        foreach ($parameters as $parameter) {

            if (array_key_exists(SQL_FILTERING_PARAMETER, $parameter)) {

                $preparedParameters .= $prevKey == SQL_FILTERING_PARAMETER ? ' AND ' . $parameter[SQL_FILTERING_PARAMETER] : $parameter[SQL_FILTERING_PARAMETER];
                $prevKey = SQL_FILTERING_PARAMETER;

            } else if (array_key_exists(SQL_SORTING_PARAMETER, $parameter)) {

                if (strlen($preparedParameters) < 7){

                    $preparedParameters = '';

                }

                $preparedParameters .= ' ' . $parameter[SQL_SORTING_PARAMETER];

            }
        }

        return $preparedParameters;
    }

    return '';
}

/**
 * Number of airports
 */

$numberOfAirports = makeQuery($pdo, COUNT_COLUMN, $SQLQueryParams['params'], '')[0]['pages'];


/** Builds pagination parameter
 * for SQL query
 *
 * @param int $numberOfAirports
 * @param int $currentPage
 * @return string
 */

function getPagination($numberOfAirports, $currentPage)
{

    if ($numberOfAirports > 5) {
        return ' LIMIT ' . ($currentPage - 1) * 5 . ',5';
    }

    return '';
}



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
 * Creates array of indexes for pagination buttons
 *
 * @param number $page
 * @param number $numberOfPages
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
 * @param number $firstPageNum
 * @param number $lastPageNum
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
 * @return  array
 */

function parseUrl()
{

    $urlFilters = '';
    $params = array();

    $fields = [
        SORTING_PARAM_NAME                   => NAME_COLUMN,
        SORTING_PARAM_CODE                   => CODE_COLUMN,
        SORTING_PARAM_STATE                  => STATE_COLUMN,
        SORTING_PARAM_CITY                   => CITY_COLUMN,
        FILTER_BY_FIRST_LETTER_PARAMETER     => NAME_COLUMN,
        FILTER_BY_STATE_PARAMETER            => STATE_COLUMN,
    ];

    $filters = [
        FILTER_BY_FIRST_LETTER_PARAMETER => SQL_FILTERING_PARAMETER,
        FILTER_BY_STATE_PARAMETER => SQL_FILTERING_PARAMETER,
        SORT_PARAMETER => SQL_SORTING_PARAMETER
    ];

    foreach ($filters as $filter => $type) {
        if (isset($_SESSION[$filter])) {
            if ($type == SQL_FILTERING_PARAMETER) {
                $params[] = array($type => $fields[$filter] . " LIKE '" . $_SESSION[$filter] . "%'");

            } elseif ($type == SQL_SORTING_PARAMETER) {

                $params[] = array($type => "ORDER BY " . $fields[$_SESSION[$filter]]);

            }

            $urlFilters .= '&' . $filter . '=' . $_SESSION[$filter];
            if ($filter != SORT_PARAMETER and !isset($_GET[PAGE_NUMBER_PARAMETER])) {
                $_SESSION[PAGE_NUMBER_PARAMETER] = 1;
            }
        }
    }

    buildUrl($urlFilters);
    return $params;
}


/**
 * Builds url
 * if reset parameter set resets url to first page
 * else if the url is different from the url which exist in session
 * saves it to headers and session variable
 *
 * @param string $urlFilters
 */

function buildUrl($urlFilters)
{
    $url = 'LOCATION: http://' . $_SERVER['HTTP_HOST'] . '/?' . PAGE_NUMBER_PARAMETER .
        '=' . $_SESSION[PAGE_NUMBER_PARAMETER] . $urlFilters;

    if (isset($_GET[RESET_PARAMETER])) {

        header('LOCATION: http://' . $_SERVER['HTTP_HOST'] . '/?' . PAGE_NUMBER_PARAMETER .
            '=' . $_SESSION[PAGE_NUMBER_PARAMETER]);

    } elseif ($_SESSION['url'] != $url) {

        header($url);
        $_SESSION['url'] = $url;

    } 


}


/**
 * Build a SELECT query to DB with all filters / sorting / pagination
 * and set the result to $airports variable
 *
 * For city_name and state_name fields you can use alias https://www.mysqltutorial.org/mysql-alias/
 */



$airports = makeQuery($pdo, AIRPORTS_ALL_COLUMNS, $SQLQueryParams['params'], getPagination($numberOfAirports, $_SESSION[PAGE_NUMBER_PARAMETER]));


function makeQuery($pdo, $columns, $parameters, $pagination)
{
    $sth = $pdo->prepare(sprintf('SELECT %s
                                FROM airports as a 
                                LEFT JOIN cities as c ON a.city_id = c.id 
                                LEFT JOIN states as s ON a.state_id = s.id
                                %s %s       
                         ;', $columns, $parameters, $pagination));

    $sth->setFetchMode(\PDO::FETCH_ASSOC);
    $sth->execute();
    return $sth->fetchAll();

}

$uniqueFirstLetters = getUniqueLetters($pdo,AIRPORTS_ALL_COLUMNS, $SQLQueryParams['params']);

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

        <?php foreach ($uniqueFirstLetters as $letter): ?>
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
        <?php foreach ($airports as $airport): ?>
            <tr>
                <td><?= $airport['name'] ?></td>
                <td><?= $airport['code'] ?></td>
                <td><a href="/?filter_by_state=<?= $airport['state_name'] ?>"><?= $airport['state_name'] ?></a></td>
                <td><?= $airport['city_name'] ?></td>
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
