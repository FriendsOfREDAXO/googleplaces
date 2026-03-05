<?php

namespace FriendsOfRedaxo\GooglePlaces\Api;

use FriendsOfRedaxo\GooglePlaces\Review;
use rex_api_function;
use rex_api_result;
use rex_api_exception;
use rex;

/**
 * API-Funktion zum Umschalten des Review-Status (sichtbar/versteckt).
 */
class rex_api_review_status extends rex_api_function
{
    protected $published = false;

    public function execute(): rex_api_result
    {
        if (!rex::isBackend() || !rex::getUser()) {
            throw new rex_api_exception('Backend login required');
        }

        $reviewId = rex_request('review_id', 'int', 0);
        if ($reviewId === 0) {
            throw new rex_api_exception('Missing review_id');
        }

        $review = Review::get($reviewId);
        if (!$review) {
            throw new rex_api_exception('Review not found');
        }

        $newStatus = $review->getStatus() === Review::STATUS_VISIBLE
            ? Review::STATUS_HIDDEN
            : Review::STATUS_VISIBLE;

        $review->setStatus($newStatus);
        if ($review->save()) {
            return new rex_api_result(true);
        }

        throw new rex_api_exception('Status could not be saved');
    }

    protected function requiresCsrfProtection(): bool
    {
        return true;
    }
}
