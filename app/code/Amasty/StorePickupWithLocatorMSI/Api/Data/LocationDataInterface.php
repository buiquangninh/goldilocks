<?php

declare(strict_types=1);

namespace Amasty\StorePickupWithLocatorMSI\Api\Data;

interface LocationDataInterface
{
    public const ID = 'id';
    public const NAME = 'name';
    public const COUNTRY = 'country';
    public const CITY = 'city';
    public const ZIP = 'zip';
    public const ADDRESS = 'address';
    public const LAT = 'lat';
    public const LNG = 'lng';
    public const PHOTO = 'photo';
    public const MARKER = 'marker';
    public const STATE = 'state';
    public const DESCRIPTION = 'description';
    public const PHONE = 'phone';
    public const EMAIL = 'email';
    public const WEBSITE = 'website';
    public const STORE_IMG = 'store_img';
    public const MARKER_IMG = 'marker_img';
    public const SHORT_DESCRIPTION = 'short_description';
    public const CURBSIDE_ENABLED = 'curbside_enabled';
    public const CURBSIDE_CONDITIONS_TEXT = 'curbside_conditions_text';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @param string|null $country
     * @return void
     */
    public function setCountry(?string $country): void;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string|null $city
     * @return void
     */
    public function setCity(?string $city): void;

    /**
     * @return string|null
     */
    public function getZip(): ?string;

    /**
     * @param string|null $zip
     * @return void
     */
    public function setZip(?string $zip): void;

    /**
     * @return string|null
     */
    public function getAddress(): ?string;

    /**
     * @param string|null $address
     * @return void
     */
    public function setAddress(?string $address): void;

    /**
     * @return string|null
     */
    public function getLat(): ?string;

    /**
     * @param string|null $lat
     * @return void
     */
    public function setLat(?string $lat): void;

    /**
     * @return string|null
     */
    public function getLng(): ?string;

    /**
     * @param string|null $lng
     * @return void
     */
    public function setLng(?string $lng): void;

    /**
     * @return string|null
     */
    public function getPhoto(): ?string;

    /**
     * @param string|null $photo
     * @return void
     */
    public function setPhoto(?string $photo): void;

    /**
     * @return string|null
     */
    public function getMarker(): ?string;

    /**
     * @param string|null $marker
     * @return void
     */
    public function setMarker(?string $marker): void;

    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @param string|null $state
     * @return void
     */
    public function setState(?string $state): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * @param string|null $phone
     * @return void
     */
    public function setPhone(?string $phone): void;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void;

    /**
     * @return string|null
     */
    public function getWebsite(): ?string;

    /**
     * @param string|null $website
     * @return void
     */
    public function setWebsite(?string $website): void;

    /**
     * @return string|null
     */
    public function getStoreImg(): ?string;

    /**
     * @param string|null $storeImg
     * @return void
     */
    public function setStoreImg(?string $storeImg): void;

    /**
     * @return string|null
     */
    public function getMarkerImg(): ?string;

    /**
     * @param string|null $markerImg
     * @return void
     */
    public function setMarkerImg(?string $markerImg): void;

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string;

    /**
     * @param string|null $shortDescription
     * @return void
     */
    public function setShortDescription(?string $shortDescription): void;

    /**
     * @return bool
     */
    public function getCurbsideEnabled(): bool;

    /**
     * @param bool $curbsideEnabled
     * @return void
     */
    public function setCurbsideEnabled(bool $curbsideEnabled): void;

    /**
     * @return string|null
     */
    public function getCurbsideConditionsText(): ?string;

    /**
     * @param string|null $curbsideConditionsText
     * @return void
     */
    public function setCurbsideConditionsText(?string $curbsideConditionsText): void;
}
