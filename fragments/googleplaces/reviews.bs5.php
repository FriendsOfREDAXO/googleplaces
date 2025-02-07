<?php

use FriendsOfRedaxo\GooglePlaces\Place;

/** @var rex_fragment $this */
/** @var Place $place */
$place = $this->getVar('place');
$place_detail = $place->getApiResponseAsArray();

date_default_timezone_set('Europe/Berlin');

$reviews = $place->getReviews(5, 0, 5, 'publishdate', 'DESC');
$googleLogo =
    '
<svg viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
    <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
        <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
        <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
        <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
        <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
    </g>
</svg>
';
?>

<section class="ratings">
    <div class="container px-0 py-5 mx-auto">
        <div class="row justify-content-center mx-0 mx-md-auto">
            <div class="col-lg-10 col-md-11 px-1 px-sm-2">
                <div class="card border-0 px-3 shadow">
                    <!-- top row -->
                    <div class="d-flex row pt-3 pb-0 px-5 bg-light">
                        <h2><?= $googleLogo ?> Echte Google-Bewertungen</h2>
                    </div>
                    <div class="d-flex row pt-3 pb-5 px-5 bg-light">
                        <div class="green-tab p-2 px-3 mx-2">
                            <p class="sm-text mb-0">&Oslash; Bewertung</p>
                            <h4><?=  $place->getAvgRatingApi() ?> / 5</h4>
                        </div>
                        <div class="white-tab p-2 mx-2 text-muted">
                            <p class="sm-text mb-0">Anzahl</p>
                            <h4><?=  $place->countReviews() ?></h4>
                        </div>
                        <div class="white-tab p-2 mx-2">
                            <p class="sm-text mb-0 text-muted">Gesamt-Bewertung</p>
                            <div class="review-stars">
                                <?php
                                $avg = $place->getAvgRatingApi();
switch ($avg) {
    case ($avg == 5):
        echo '⭐⭐⭐⭐⭐';
        break;
    case (($avg > 4.0) && ($avg < 5.0)):
        echo '⭐⭐⭐⭐⭐';
        break;
    case (($avg > 3.0) && ($avg < 4.0)):
        echo '⭐⭐⭐⭐';
        break;
    case (($avg > 2.0) && ($avg < 3.0)):
        echo '⭐⭐⭐';
        break;
    case (($avg > 1.0) && ($avg <= 2.0)):
        echo '⭐⭐';
        break;
    default:
        echo '⭐';
}
?>
                            </div>
                        </div>
                        <div class="ms-md-auto p-2 mx-md-2 pt-4 pt-md-3"><a href="" class="btn btn-red px-4">Eigene Bewertung verfassen</a></div>
                    </div>

                    <?php
                    foreach ($reviews as $review) {
                        $profile_photo = $review['profile_photo_url'];
                        if ($review['profile_photo_base64'] != "") {
                            $profile_photo = 'data:image/jpg;base64,'.$review['profile_photo_base64'];
                        }
                        $stars = intval($review['rating']);
                        $starList = '';
                        $timestamp = $review['time'];
                        do {
                            $starList .= '<li><i class="fa fa-star"></i></li>';
                            $stars--;
                        } while ($stars > 0);
                        echo '
                <div class="row">
                <div class="col-12">
                <div class="review p-5 border-bottom">
                    <div class="row d-flex">
                        <div class="profile-pic"><img src="' . $profile_photo . '" alt="' . $review['author_name'] . '" width="60px" height="60px"></div>
                        <div class="d-flex flex-column ps-3">
                            <h4>' . $review['author_name'] . '</h4>
                            <p class="grey-text">' . date('d.m.Y', $timestamp) . '</p>
                        </div>
                    </div>
                    <div class="row pb-3 review-stars">
                        <ul>
                        ' . $starList . '
                        </ul>
                        <!--<div class="green-text">
                            <h5 class="mb-0 ps-3">Exzellent</h5>
                        </div>-->
                    </div>
                    <div class="row pb-3">
                        <p>' . $review['text'] . '</p>
                    </div>
                    <div class="row ms-1">
                        <div class="row bg-light via">
                            <div class="px-2">
                            ' . $googleLogo . '
                            </div>
                            <p class="grey-text mb-0 px-3"><a class="stretched-link" href="' . $review['author_url'] . '" target="_blank" rel="nofollow noopener">via Google</a></p>
                        </div>
                    </div>
                </div>
                </div>
                </div>';
                    }
?>

                </div>
            </div>
        </div>
    </div>
</section>
