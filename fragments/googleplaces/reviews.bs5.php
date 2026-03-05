<?php

use FriendsOfRedaxo\GooglePlaces\Place;
use FriendsOfRedaxo\GooglePlaces\Review;

/** @var rex_fragment $this */
/** @var Place $place */
$place = $this->getVar('place', Place::query()->orderBy('id')->findOne());
$limit = $this->getVar('limit', 3);

if ($place === null) {
    return;
}

$reviews = $place->getReviews($limit, 0, 5, 'publishdate', 'DESC');

?>
<section data-googleplaces-reviews class="container">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<h2><?= rex_i18n::msg('googleplaces_fragment_reviews_title') ?></h2>
				<p class="sm-text mb-0">
					<?= rex_i18n::msg('googleplaces_fragment_rating') ?>: <?= rex_escape($place->getAvgRatingApi()) ?> / 5
					(<?= rex_escape($place->countReviews()) ?> <?= rex_i18n::msg('googleplaces_fragment_reviews') ?>)
				</p>
			</div>
		</div>

		<?php
        foreach ($reviews as $review) {
            /** @var Review $review */
            $profile_photo = $review->getProfilePhotoSrc();
            ?>
		<div class="col-12">
			<div class="card">
				<?php if ($profile_photo): ?>
				<div class="author-image">
					<img src="<?= rex_escape($profile_photo) ?>"
						alt="<?= rex_escape($review->getAuthorName()) ?>"
						width="60" height="60">
				</div>
				<?php endif; ?>
				<?= rex_escape($review->getAuthorName()) ?>
				<p class="publishdate">
					<?= rex_formatter::intlDate($review->getPublishdate()) ?>
				</p>

				<div data-googleplaces-review-stars="container">

					<div data-googleplaces-review-stars="background"></div>
					<div
						data-googleplaces-review-stars="<?= (int) $review->getRating() ?>">
					</div>
				</div>
				<p><?= nl2br(rex_escape($review->getText(true))) ?></p>
				<a href="<?= rex_escape($review->getAuthorUrl()) ?>"
					target="_blank" rel="nofollow noopener"><?= rex_i18n::msg('googleplaces_fragment_via_google') ?></a>
			</div>
		</div>

		<?php
        }
?>

	</div>
</section>
