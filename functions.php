<?php

// Создаем функцию подключения к базе данных и получения данных по sql запросу, который передаем как аргумент функции
function getDBdata($sql)
{
    $servername = "localhost";
    $username = "db_movies_user";
    $password = "db_movies_pass";
    $dbname = "db_movies";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die ("Не удалось подлючиться к базе данных: " . mysqli_connect_error());
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
    print ("<pre>" . print_r($data, true) . "</pre>");
}

// Функция возвращает обрезанную строку с "..." если ее длина превышает определенное кол-во символов 
function getShortStr($str, $maxLen){
    return strlen($str) > $maxLen ? substr($str, 0, $maxLen) . "..." : $str;
}

// Функция возвращает данные всех фильмов для главной страницы
function getMoviesData(){
    $rows = getDBdata("SELECT * FROM data ORDER BY id LIMIT 8");
    $rowIndex = 0;
    $result = [];
    foreach ($rows as $row) {
        $result[$rowIndex] = array(
            "id" => $row["id"],
            "title" => $row["title"],
            "year" => $row["year"],
            "genres" => getShortStr($row["genres"], 30),
            "cast" => getShortStr($row["cast"], 30),
            "extract" => getShortStr($row["extract"], 90),
            "thumbnail" => $row["thumbnail"],
        );
        $rowIndex++;
    }
    return $result;
}

// Функция возвращает данные фильма по id параметру
function getMovieData(){
    $id = $_GET["id"];
    $movie = getDBdata("SELECT * FROM data WHERE id = '$id'")[0];
    $genresSqlStr = '"' . str_replace(',', '","', $movie["genres"]) . '"';
    $castsSqlStr = '"' . str_replace(',', '","', $movie["cast"]) . '"';
    $genresArray = getDBdata("SELECT name, id FROM genres WHERE name IN ($genresSqlStr)");
    $castsArray = getDBdata("SELECT name, id FROM cast WHERE name IN ($castsSqlStr)");
    $movie["genres"] = $genresArray;
    $movie["cast"] = $castsArray;
    return $movie; 
} 

// Функция возвращает genre фильма по id параметру
function getGenreData(){
    $id = $_GET["id"];
    $rows = getDBdata("SELECT * FROM genres WHERE id = '$id'");
    return $rows[0];
} 

// Функция возвращает genre фильма из БД genres
function getGenresData(){
    $rows = getDBdata("SELECT * FROM genres ORDER BY name ASC");
    return $rows;
}

// Делаем отдельную фукнцию для сетки жанров чтобы не запутаться в действии каждой функции
// Фукния возвращает массив из 4 колонок. Каждая колонка это массив из 11 элементов (41/4 = 10.25 = сeil(10.25) = 11)
function getGenresColsData(){
    $data = getGenresData();
    $colsLen = 4;
    $rowsLen = ceil(count($data) / $colsLen);
    $result = array_chunk($data, $rowsLen);
    return $result;
}

// Функция возвращает genre фильма по id параметру
function getCastData(){
    $id = $_GET["id"];
    $rows = getDBdata("SELECT * FROM cast WHERE id = '$id'");
    return $rows[0];
} 