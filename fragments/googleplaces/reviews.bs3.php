<?php

use FriendsOfRedaxo\GooglePlaces\Helper;

date_default_timezone_set('Europe/Berlin');
$reviews    = Helper::getAllReviews('time DESC');
$gplace     = Helper::getPlaceDetails();
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
$googleLogoBig =
    '
    <svg width="85px" height="36px" viewBox="0 0 85 36"
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g><rect x="0" y="0" width="85" height="36"></rect><path d="M20.82051,13.8285403 L10.9585121,13.8285403 L10.9585121,16.7549491 L17.9526292,16.7549491 C17.6071093,20.8585601 14.1930357,22.6085804 10.9703375,22.6085804 C6.8472338,22.6085804 3.24942855,19.3645384 3.24942855,14.8175829 C3.24942855,10.3881993 6.67907946,6.97693356 10.9794447,6.97693356 C14.2972206,6.97693356 16.2522225,9.09196401 16.2522225,9.09196401 L18.3012295,6.97010295 C18.3012295,6.97010295 15.6712777,4.0423 10.8747175,4.0423 C4.76744864,4.0423 0.0422999999,9.19733415 0.0422999999,14.7654363 C0.0422999999,20.2215356 4.48712612,25.5420753 11.0306691,25.5420753 C16.7860807,25.5420753 20.9990888,21.5993904 20.9990888,15.7694496 C20.9990888,14.5394417 20.82051,13.8285403 20.82051,13.8285403 Z" fill="#4285F4" fill-rule="nonzero"></path><path d="M28.899228,11.7094728 C24.8526602,11.7094728 21.9526644,14.8730534 21.9526644,18.5627572 C21.9526644,22.3070153 24.7654943,25.5294394 28.9467825,25.5294394 C32.7319827,25.5294394 35.8329889,22.6363394 35.8329889,18.6432338 C35.8329889,14.0665692 32.2257972,11.7094728 28.899228,11.7094728 L28.899228,11.7094728 Z M28.9394662,14.4237174 C30.9294664,14.4237174 32.8151293,16.0325192 32.8151293,18.6249432 C32.8151293,21.1623705 30.9373934,22.8170247 28.930321,22.8170247 C26.7249386,22.8170247 24.9851565,21.0507416 24.9851565,18.6048245 C24.9851565,16.2113628 26.7033664,14.4237174 28.9394662,14.4237174 L28.9394662,14.4237174 Z" fill="#EA4335" fill-rule="nonzero"></path><path d="M44.0076424,11.7094728 C39.9610739,11.7094728 37.0610789,14.8730534 37.0610789,18.5627572 C37.0610789,22.3070153 39.8739088,25.5294394 44.0551961,25.5294394 C47.8403964,25.5294394 50.9414026,22.6363394 50.9414026,18.6432338 C50.9414026,14.0665692 47.3342109,11.7094728 44.0076424,11.7094728 Z M44.0478799,14.4237174 C46.03788,14.4237174 47.9235429,16.0325192 47.9235429,18.6249432 C47.9235429,21.1623705 46.0458079,22.8170247 44.0387355,22.8170247 C41.8333522,22.8170247 40.0935701,21.0507416 40.0935701,18.6048245 C40.0935701,16.2113628 41.8117801,14.4237174 44.0478799,14.4237174 L44.0478799,14.4237174 Z" fill="#FBBC05" fill-rule="nonzero"></path><path d="M58.8254221,11.716789 C55.1111158,11.716789 52.1916154,14.9699517 52.1916154,18.6212851 C52.1916154,22.7804935 55.5763625,25.5385847 58.7614036,25.5385847 C60.7308069,25.5385847 61.7780052,24.7568358 62.5511037,23.8595572 L62.5511037,25.2221671 C62.5511037,27.6062053 61.1035554,29.0338142 58.9186986,29.0338142 C56.8080082,29.0338142 55.7493001,27.4643717 55.3814018,26.5738019 L52.7256851,27.6840085 C53.667833,29.6760466 55.5643306,31.753546 58.9406464,31.753546 C62.6335642,31.753546 65.4482484,29.4274402 65.4482484,24.5490929 L65.4482484,12.1319728 L62.5511037,12.1319728 L62.5511037,13.3025365 C61.6609019,12.3426691 60.442749,11.716789 58.8254221,11.716789 Z M59.0942826,14.4255465 C60.9153348,14.4255465 62.7852164,15.9804564 62.7852164,18.6359176 C62.7852164,21.334963 60.9193203,22.822511 59.0540451,22.822511 C57.0738721,22.822511 55.2314229,21.2146516 55.2314229,18.6615234 C55.2314229,16.0085943 57.1449784,14.4255465 59.0942826,14.4255465 L59.0942826,14.4255465 Z" fill="#4285F4" fill-rule="nonzero"></path><path d="M78.3299385,11.7003275 C74.8262836,11.7003275 71.8845225,14.4878831 71.8845225,18.6011664 C71.8845225,22.9535598 75.1635695,25.5349266 78.6664756,25.5349266 C81.5900976,25.5349266 83.3844636,23.9354976 84.4552796,22.5024353 L82.0665902,20.9130298 C81.4467429,21.8749842 80.4104978,22.8151956 78.6811072,22.8151956 C76.7385042,22.8151956 75.8453177,21.7514775 75.2919567,20.7209835 L84.5576964,16.8764135 L84.0766693,15.7497463 C83.1811952,13.5433004 81.0930015,11.7003275 78.3299385,11.7003275 L78.3299385,11.7003275 Z M78.4506466,14.3578732 C79.7131527,14.3578732 80.621744,15.0291065 81.0076029,15.833881 L74.820074,18.4200945 C74.5533141,16.4178248 76.450195,14.3578732 78.4506466,14.3578732 Z" fill="#EA4335" fill-rule="nonzero"></path><polygon fill="#34A853" fill-rule="nonzero" points="67.4674711 25.1244795 70.5109366 25.1244795 70.5109366 4.7566717 67.4674711 4.7566717"></polygon></g></g></svg>
    ';
?>

<section class="ratings">
    <div class="container">
        <div class="row ratings-header">
            <div class="col-12">
                <div class="media">
                    <div class="media-left">
                        <?= $googleLogoBig ?>
                    </div>
                    <div class="media-body media-middle">
                        <h4 class="media-heading">Bewertungen</h4>
                    </div>
                </div><!-- /. media -->
                <div class="avg-rating">
                    <?= Helper::getAvgRating() ?> / 5
                </div>
                <div class="review-stars">
                    <ul>
                    <?php
                    $avg = Helper::getAvgRating();
switch ($avg) {
    case ($avg == 5):
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li>';
        break;
    case (($avg > 4.0) && ($avg < 5.0)):
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star-half"></i></li>';
        break;
    case 4:
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li>';
        break;
    case (($avg > 3.0) && ($avg < 4.0)):
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star-half"></i></li>';
        break;
    case 3:
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li>';
        break;
    case (($avg > 2.0) && ($avg < 3.0)):
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li><li><i class="fa fa-star-half"></i></li>';
        break;
    case 2:
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star"></i></li>';
        break;
    case (($avg > 1.0) && ($avg < 2.0)):
        echo '<li><i class="fa fa-star"></i></li><li><i class="fa fa-star-half"></i></li>';
        break;
    case 1:
        echo '<li><i class="fa fa-star"></i></li>';
        break;
}
?>
                    </ul>
                    <span class="grey-text">
                                <?= Helper::getTotalRatings() ?> Bewertungen gesamt
                    </span>
                </div>
            </div>
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
            echo
            '
            <div class="row single-review">
                <div class="col-12">
                    
                    <div class="media">
                      <div class="media-left">
                        <a href="'.$review['author_url'].'" target="_blank" rel="nofollow noopener">
                          <img class="media-object" src="'.$profile_photo.'" width="44" alt="'.$review['author_name'].'">
                        </a>
                      </div>
                      <div class="media-body">
                        <h4 class="media-heading">'.$review['author_name'].' <span class="grey-text">' . date('d.m.Y', $timestamp) . '</span></h4>
                        <div class="review-stars">
                            <ul>
                            '.$starList.'
                            </ul>
                        </div>
                      </div>
                    </div><!-- /. media -->
                    <div class="rating-text">
                      <p>'.$review['text'].'</p>
                    </div><!-- /. rating-text -->
                    <div class="media">
                      <div class="media-left">
                        '.$googleLogo.'
                      </div>
                      <div class="media-body media-middle">
                        <span class="google-heading">Gepostet auf
                        <a href="'.$review['author_url'].'" target="_blank" rel="nofollow noopener">Google</a>
                        </span>
                      </div>
                    </div><!-- /. media -->

                </div>
            </div>
            ';
        }
?>
    </div>
</section>
