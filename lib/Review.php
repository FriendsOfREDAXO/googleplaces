<?php

namespace FriendsOfRedaxo\GooglePlaces;

use rex_i18n;
use rex_yform_manager_dataset;

class Review extends rex_yform_manager_dataset
{

    // Status-Werte definieren
    public const STATUS_VISIBLE = 1;
    public const STATUS_HIDDEN = 0;

    /* Place */
    /** @api */
    public function getPlace(): ?Place
    {
        return $this->getRelatedDataset("place_detail_id");
    }

    /* Place ID */
    /** @api */
    public function getPlaceId(): ?int
    {
        return $this->getValue("place_detail_id");
    }
    /** @api */
    public function setPlaceId(int $value): self
    {
        $this->setValue("place_detail_id", $value);
        return $this;
    }

    /* Google Place ID */
    /** @api */
    public function getGooglePlaceId(): ?string
    {
        return $this->getValue("google_place_id");
    }
    /** @api */
    public function setGooglePlaceId(mixed $value): self
    {
        $this->setValue("google_place_id", $value);
        return $this;
    }

    /* Autor*in */
    /** @api */
    public function getAuthorName(): ?string
    {
        return $this->getValue("author_name");
    }
    /** @api */
    public function setAuthorName(mixed $value): self
    {
        $this->setValue("author_name", $value);
        return $this;
    }

    /* Bewertung */
    /** @api */
    public function getRating(): int
    {
        return (int) $this->getValue("rating");
    }
    /** @api */
    public function setRating(int $value): self
    {
        $this->setValue("rating", $value);
        return $this;
    }

    /* Autor*in URL */
    /** @api */
    public function getAuthorUrl(): ?string
    {
        return $this->getValue("author_url");
    }
    /** @api */
    public function setAuthorUrl(mixed $value): self
    {
        $this->setValue("author_url", $value);
        return $this;
    }

    /* Sprache */
    /** @api */
    public function getLanguage(): ?string
    {
        return $this->getValue("language");
    }
    /** @api */
    public function setLanguage(mixed $value): self
    {
        $this->setValue("language", $value);
        return $this;
    }

    /* Text */
    /** @api */
    public function getText(bool $asPlaintext = false): ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue("text"));
        }
        return $this->getValue("text");
    }
    /** @api */
    public function setText(mixed $value): self
    {
        $this->setValue("text", $value);
        return $this;
    }

    /* Profilbild URL */
    /** @api */
    public function getProfilePhotoUrl(): ?string
    {
        return $this->getValue("profile_photo_url");
    }
    /** @api */
    public function setProfilePhotoUrl(mixed $value): self
    {
        $this->setValue("profile_photo_url", $value);
        return $this;
    }

    /* Profilbild Base64 */
    /** @api */
    public function getProfilePhotoBase64(bool $asPlaintext = false): ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue("profile_photo_base64"));
        }
        return $this->getValue("profile_photo_base64");
    }
    /** @api */
    public function setProfilePhotoBase64(mixed $value): self
    {
        $this->setValue("profile_photo_base64", $value);
        return $this;
    }

    /* Erstellt am... */
    /** @api */
    public function getCreatedate(): ?string
    {
        return $this->getValue("createdate");
    }
    /** @api */
    public function setCreatedate(string $value): self
    {
        $this->setValue("createdate", $value);
        return $this;
    }

    /* Veröffenhtlicht am... */
    /** @api */
    public function getPublishdate(): ?string
    {
        return $this->getValue("publishdate");
    }
    /** @api */
    public function setPublishdate(string $value): self
    {
        $this->setValue("publishdate", $value);
        return $this;
    }

    /* Zuletzt aktualisiert am... */
    /** @api */
    public function getUpdatedate(): ?string
    {
        return $this->getValue("updatedate");
    }
    /** @api */
    public function setUpdatedate(string $value): self
    {
        $this->setValue("updatedate", $value);
        return $this;
    }

    /* UUID */
    /** @api */
    public function getUuid(): mixed
    {
        return $this->getValue("uuid");
    }
    /** @api */
    public function setUuid(mixed $value): self
    {
        $this->setValue("uuid", $value);
        return $this;
    }

    public static function epYformDataList(\rex_extension_point $ep)
    {
        if ($ep->getParam('table')->getTableName() !== self::table()->getTableName()) {
            return;
        }

        /** @var \rex_yform_list $list */
        $list = $ep->getSubject();

        $list->setColumnFormat(
            'place_detail_id',
            'custom',
            static function ($a) {
                if ($a['value'] === 0 || $a['value'] === null) {
                    return "";
                }
                $place = Place::get($a['value']);
                if ($place !== null) {
                    $place_details = $place->getApiResponseAsArray();
                    $place_name = "Unbekannt";
                    if (isset($place_details['name'])) {
                        $place_name = $place_details['name'];
                    }
                    return '<a href="index.php?page=googleplaces/place/detail&google_place_id='.$place->getId().'" target="_blank">'.$place_name.'</a>';
                }
                return "<code>".$a['list']->getValue('place_id')."</code>";
            },
        );
        // Profilbild bei Autor ausgeben
        $list->setColumnFormat(
            'author_name',
            'custom',
            static function ($a) {
                $profile_photo_base64 = $a['list']->getValue('profile_photo_base64');
                $output = $a['value'];
                if ($profile_photo_base64) {
                    $output = '<img src="data:image/jpeg;base64,' . $profile_photo_base64 . '" alt="' . $a['value'] . '" style="max-width: 30px; max-height: 30px; border-radius: 50%;"> <span class="text-nowrap">' . $a['value'] . '</span>';
                }
                return $output;
            }
        );

        // updatedate formatiert ausgeben mit rex_formatter
        $list->setColumnFormat(
            'updatedate',
            'custom',
            static function ($a) {
                if ($a['value'] === "0000-00-00 00:00:00") {
                    return "";
                }
                return \rex_formatter::intlDateTime($a['value']);
            },
        );
        // updatedate formatiert ausgeben mit rex_formatter
        $list->setColumnFormat(
            'createdate',
            'custom',
            static function ($a) {
                if ($a['value'] === "0000-00-00 00:00:00") {
                    return "";
                }
                return \rex_formatter::intlDateTime($a['value']);
            },
        );
        // publishdate formatierrt ausgeben mit rex_formatter
        $list->setColumnFormat(
            'publishdate',
            'custom',
            static function ($a) {
                if ($a['value'] === "0000-00-00 00:00:00") {
                    return "";
                }
                return '<span class="text-nowrap">'. \rex_formatter::intlDateTime($a['value']) .'</span>';
            },
        );
    }
    
    /** @api */
    public static function findFilter(string $place_id = null, int $limit = 5, int $offset = 0, int $minRating = 5, string $orderByField = 'publishdate', string $orderBy = 'DESC', int $status = self::STATUS_VISIBLE): \rex_yform_manager_collection | null
    {

        $query = self::query();
        if ($place_id !== null) {
            $query->where('place_id', $place_id);
        }
        if ($minRating >= 0) {
            $query->where('rating', $minRating, '>=');
        }
        $query->where('status', $status);
        if ($limit >= 0) {
            $query->limit($offset, $limit);
        }
        return $query
        ->orderBy($orderByField, $orderBy)
        ->find();
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_VISIBLE => rex_i18n::msg('googleplaces_review_status_visible'),
            self::STATUS_HIDDEN => rex_i18n::msg('googleplaces_review_status_hidden'),
        ];
    }

}
