<?php
include "header.php";
$movies = getMovies();
?>

<!-- главный контейнер -->
<div class="container">
    <!-- Нзвание страницы -->
    <h3>Found all movies <u><b>Movies</b></u> : <?php echo $movies["length"]; ?></h3>

    <!-- контейнер для фильмов -->
    <div id="movies-container" class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 mb-3 g-3">
        <?php foreach ($movies["movies"] as $movie) { ?>
            <div class="col">
                <div class="card border border-0 shadow-sm">
                    <img src="./content/movies-thumbnails/<?php echo $movie["thumbnail"]; ?>" onError="this.src='./images/movie-default.png'" class="card-img-top" alt="Movie thumbnail">
                    <div class="card-header border border-0"><?php echo $movie["genres"]; ?></div>
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <span><?php echo $movie["title"] ?></span>
                            <small class="text-body-tertiary">(<?php echo $movie["year"]; ?>)</small>
                        </h5>
                        <h6 class="card-text mb-3 text-secondary"><em><?php echo $movie["cast"]; ?></em></h6>
                        <p class="card-text"><?php echo $movie["extract"] ?></p>
                        <a href="./single-movie.php?id=<?php echo $movie["id"]; ?>" class="btn btn-primary">
                            Read more...
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
include "pagination.php";
?>

<!-- Поиск страницы -->
<form class="row justify-content-center g-1" action="/" method="GET" id="#formGoToPage">
    <div class="col-auto">
        <button type="submit" class="btn btn-primary mb-3" id="#btnGoToPage">Go to page</button>
    </div>
    <div class="col-auto">
        <input type="number" class="form-control input-go-to-page" name="page" id="#inputGoToPage" required min="1" max="<?php echo $movies["pages"]; ?>">
    </div>
</form>


<?php
include "footer.php";
?>