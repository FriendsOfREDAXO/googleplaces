<?php

use FriendsOfRedaxo\GooglePlaces\Place;
use FriendsOfRedaxo\GooglePlaces\Review;

/** @var rex_fragment $this */
/** @var Place $place */
$place = $this->getVar('place', Place::query()->orderBy('id')->findOne());

if ($place === null) {
    return;
}

$reviews = $place->getReviews(5, 0, 5, 'publishdate', 'DESC');

?>
<section data-googleplaces-reviews class="container">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<h2>Bewertungen auf Google</h2>
				<p class="sm-text mb-0">
					Bewertung: <?= $place->getAvgRatingApi() ?> / 5
					(<?= $place->countReviews() ?> Bewertungen)
				</p>
				<a href="<?= '' ?>"
					class="btn">Eigene Bewertung verfassen</a>
			</div>
		</div>

		<?php
    foreach ($reviews as $review) {
        /** @var Review $review */
        $profile_photo = 'data:image/jpg;base64,' . $review->getProfilePhotoBase64();
        ?>
		<div class="col-12">
			<div class="card">
				<div class="author-image">
					<img src="<?= $profile_photo ?>"
						alt="<?= $review->getAuthorName() ?>"
						width="60" height="60">
				</div>
				<?= $review->getAuthorName() ?>
				<p class="publishdate">
					<?= rex_formatter::intlDate($review->getPublishdate()) ?>
				</p>
				<div class="review-stars">
					<div data-googleplaces-review-stars="background">
						<div
							data-googleplaces-review-stars="<?= $review->getRating() ?>">
							<?= $review->getRating() ?>
						</div>
					</div>
				</div>
				<p><?= $review->getText() ?></p>
				<a href="<?= $review->getAuthorUrl()  ?>"
					target="_blank" rel="nofollow noopener">via Google</a>
			</div>
		</div>

		<?php
    }
?>

	</div>
	</div>
</section>
