<?php

// ---------------------------- GLOBAL

// Создаем функцию подключения к базе данных и получения данных по sql запросу, который передаем как аргумент функции
function getDBdata($sql)
{
    $servername = "localhost";
    $username = "db_movies_user";
    $password = "db_movies_pass";
    $dbname = "db_movies";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Не удалось подлючиться к базе данных: " . mysqli_connect_error());
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


// ---------------------------- MOVIES

// Функция возвращает все данные первых 8 фильмов
function getMovies()
{
    $movies = getDBdata("SELECT * FROM movies ORDER BY id LIMIT 8");
    $result = [];
    foreach ($movies as $movieIndex => $movie) {
        $result[$movieIndex] = array(
            "id" => $movie["id"],
            "title" => $movie["title"],
            "year" => $movie["year"],
            "genres" => getShortStr($movie["genres"], 30),
            "cast" => getShortStr($movie["cast"], 30),
            "extract" => getShortStr($movie["extract"], 90),
            "thumbnail" => $movie["thumbnail"],
        );
    }
    return $result;
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
    return $letters;
}

// Функция возвращает всех актеров по первой букве имени
function getActors()
{
    $letter = $_GET["letter"];
    $actors = getDBdata("SELECT * FROM actors WHERE letter = '$letter'");
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
    $movies = getDBdata("SELECT * FROM movies WHERE id IN ($list)");
    $result = [];
    foreach ($movies as $movie) {
        array_push($result, [
            "id" => $movie["id"],
            "title" => $movie["title"],
            "year" => $movie["year"],
            "genres" => getShortStr($movie["genres"], 30),
            "cast" => getShortStr($movie["cast"], 30),
            "extract" => getShortStr($movie["extract"], 90),
            "thumbnail" => $movie["thumbnail"],
        ]);
    };
    return $result;
}
