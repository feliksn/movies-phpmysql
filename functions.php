<?php

// ---------------------------- GLOBAL

// Создаем функцию подключения к базе данных и получения данных по sql запросу, который передаем как аргумент функции
function getDBdata($sql)
{
    $servername = "localhost";
    $username = "db_movies_user";
    $password = "db_movies_pass";
    $dbname = "db_movies";
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Не удалось подлючиться к базе данных: " . $conn->connect_error);
    }

    $data = $conn->query($sql);
    
    if ($data->num_rows > 0) {
        $result = [];
        while ($row = $data->fetch_assoc()) {
            array_push($result, $row);
        }
    } else {
        $result = "Каких-либо записей не найдено!";
    }
    
    mysqli_close($conn);
    
    return $result;
}

// Функция показывает данные, записанные в переменной, которую можно передевать как параметр функции
function showRawData($data)
{
    print("<pre>" . print_r($data, true) . "</pre>");
}

// Функция возвращает обрезанную строку с "..." если ее длина превышает определенное кол-во символов 
function getShortStr($str, $maxLen)
{
    return strlen($str) > $maxLen ? substr($str, 0, $maxLen) . "..." : $str;
}

// Разделяет массив, переданный как 1-й параметр, на кол-во колонок, переданные как 2-й параметр
// По умолчанию кол-во колонок = 4. Если не указвать второй параметр при вызове функции, то кол-во колонок всегда будет = 4
function getArrCols($arr, $colsLen=4)
{
    $rowsLen = ceil(count($arr) / $colsLen);
    $arrCols = array_chunk($arr, $rowsLen);
    return $arrCols;
}

// Функция пределывает обычную строку со значениями через запятую на строку со значениями для sql запроса
function getSqlFromStr($str)
{
    return '"' . str_replace(',', '","', $str) . '"';
}

// Функция возвращает данные для каждого элемента пагинации
// Функция принимает два параметра. $page - номер актуальной страницы. $pages - кол-во всех страниц
function getPagination($page, $pages)
{
    // Функция создает ссылку с данными, передавая ей аргумент номера актуальной страницы
    function getLink($page="")
    {
        return array(
            "class" => "", 
            "text" => $page,
            "link" => "/?page=" . $page
        );
    }

    // Заполнить переменные данными
    $prevPageArrow;
    $firstPage;
    $emptyLeft;
    
    $page1;
    $page2;
    $page3;
    
    $emptyRight;
    $lastPage;
    $nextPageArrow;
    
    // Заполнить условия case данными
    switch ($page) {
        case 1:

            break;
        case 2:

            break;
        case 3:

            break;
        case $pages - 2:

            break;
        case $pages - 1:

            break;
        case $pages:

            break;
    }

    // Функция возвращает ассоциированный массив ключ=значение, по которым будем получать данные и заполнять пагинацию
    return array(
        "prevPageArrow" => $prevPageArrow,
        "firstPage" => $firstPage,
        "emptyRight" => $emptyRight,

        "page1" => $page1,
        "page2" => $page2,
        "page3" => $page3,
        
        "emptyLeft" => $emptyLeft,
        "lastPage" => $lastPage,
        "nextPageArrow" => $nextPageArrow,
    );
}


// ---------------------------- MOVIES

// Функция возвращает массив с 4 типами данных о фильмах. Данные из этой функции можно использовать в для построения пагинации страниц
function getMovies()
{
    // Кол-во фильмов на одной странице
    $moviesOnPage = 8;
    // Номер актуальной страницы
    $page = isset($_GET["page"]) && !empty($_GET["page"]) ? $_GET["page"] : 1;
    // Позиция фильма с которой надо получить 8 фильмов
    $firstMoviePos = $page * $moviesOnPage - $moviesOnPage;
    // Кол-во всех фильмов по запросу sql
    $length = getDBdata("SELECT COUNT(id) as total FROM movies")[0]["total"];
    // Кол-во страниц в зависимости от кол-ва фильмов на одной странице
    $pages = ceil($length / $moviesOnPage);
    // Все данные 8 фильмов для определенной страницы
    $movies = getDBdata("SELECT * FROM movies ORDER BY id LIMIT $firstMoviePos, $moviesOnPage");
    foreach ($movies as $movieIndex => $movie) {
        $movie["genres"] = getShortStr($movie["genres"], 30);
        $movie["cast"] = getShortStr($movie["cast"], 30);
        $movie["extract"] = getShortStr($movie["extract"], 90);
        $movies[$movieIndex] = $movie;
    }
    return array(
        "movies" => $movies,
        "length" => $length,
        "page"   => $page,
        "pages"  => $pages
    );
}

// Функция возвращает данные фильма по id параметру
function getSingleMovie()
{
    $id = $_GET["id"];
    $movies = getDBdata("SELECT * FROM movies WHERE id = '$id'");
    $movie = $movies[0];
    $genresSql = getSqlFromStr($movie["genres"]);
    $castSql = getSqlFromStr($movie["cast"]);
    $genres = getDBdata("SELECT name, id FROM genres WHERE name IN ($genresSql)");
    $cast = getDBdata("SELECT * FROM actors WHERE name IN ($castSql)");
    $movie["genres"] = $genres;
    $movie["cast"] = $cast;
    return $movie;
}

  
// ---------------------------- GENRES

// Функция возвращает данные отдельного жанра по id параметру
function getSingleGenre()
{
    $id = $_GET["id"];
    $genres = getDBdata("SELECT * FROM genres WHERE id = '$id'");
    return $genres[0];
}

// Функция возвращает данные всех жанров
function getGenres()
{
    $genres = getDBdata("SELECT * FROM genres ORDER BY name ASC");
    return $genres;
}

// Функция возвращает данные всех жанров в разделенные на колонки
function getGenresCols()
{
    $genres = getGenres();
    $genresCols = getArrCols($genres);
    return $genresCols;
}


// ----------------------------- ACTORS

// Функция возвращает данные отдельного аткера по id параметру
function getSingleActor()
{
    $id = $_GET["id"];
    $actors = getDBdata("SELECT * FROM actors WHERE id = '$id'");
    return $actors[0];
}

// Функция возвращает уникальные буквы из БД actors (для менюшки поиска по буквам)
function getUniqueActorsLetters()
{
    $letters = getDBdata("SELECT DISTINCT letter FROM actors ORDER BY letter ASC");
    foreach($letters as $letterIndex => $letter)
    {
        $letters[$letterIndex] = $letter["letter"];
    }
    return $letters;
}

// Функция возвращает всех актеров по первой букве имени
function getActors()
{
    $letter = $_GET["letter"];
    $actors = getDBdata("SELECT * FROM actors WHERE letter = '$letter' ORDER BY name ASC");
    return $actors;
}

// Функция возвращает данные всех актеров разделенные на колонки
function getActorsCols()
{
    $actors = getActors();
    $actorsCols = getArrCols($actors);
    return $actorsCols;
}

// Функция возвращает данные фильмов по переданному списку id в параметр функции
function getMoviesByIdList($list)
{
    $movies = getDBdata("SELECT * FROM movies WHERE id IN ($list) LIMIT 8");
    foreach ($movies as $movieIndex => $movie) {
        $movie["genres"] = getShortStr($movie["genres"], 30);
        $movie["cast"] = getShortStr($movie["cast"], 30);
        $movie["extract"] = getShortStr($movie["extract"], 90);
        $movies[$movieIndex] = $movie;
    };
    return $movies;
}
