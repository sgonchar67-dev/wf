<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * BIpolDpdLocation
 */
#[Table(name: 'b_ipol_dpd_location', indexes: ['(name="b_ipol_dpd_location_city", columns={"CITY_ID"})', '(name="b_ipol_dpd_location_search_text", columns={"ORIG_NAME_LOWER"})', '(name="b_ipol_dpd_location_crc", columns={"CITY_NAME", "REGION_NAME", "COUNTRY_NAME"})', '(name="b_ipol_dpd_location_is_city", columns={"IS_CITY"})'])]
#[Entity]
class BIpolDpdLocation
{
    #[Column(name: 'ID', type: 'integer', nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'COUNTRY_CODE', type: 'string', length: 255, nullable: true)]
    private ?string $countryCode = null;
    #[Column(name: 'COUNTRY_NAME', type: 'string', length: 255, nullable: true)]
    private ?string $countryName = null;
    #[Column(name: 'REGION_CODE', type: 'string', length: 255, nullable: true)]
    private ?string $regionCode = null;
    #[Column(name: 'REGION_NAME', type: 'string', length: 255, nullable: true)]
    private ?string $regionName = null;
    #[Column(name: 'CITY_ID', type: 'string', length: 255, nullable: true)]
    private ?string $cityId = null;
    #[Column(name: 'CITY_CODE', type: 'string', length: 255, nullable: true)]
    private ?string $cityCode = null;
    #[Column(name: 'CITY_NAME', type: 'string', length: 255, nullable: true)]
    private ?string $cityName = null;
    #[Column(name: 'CITY_ABBR', type: 'string', length: 255, nullable: true)]
    private ?string $cityAbbr = null;
    #[Column(name: 'LOCATION_ID', type: 'integer', nullable: false)]
    private string|int $locationId = '0';
    #[Column(name: 'IS_CASH_PAY', type: 'string', length: 1, nullable: false, options: ['default' => 'N', 'fixed' => true])]
    private string $isCashPay = 'N';
    #[Column(name: 'ORIG_NAME', type: 'string', length: 255, nullable: true)]
    private ?string $origName = null;
    #[Column(name: 'ORIG_NAME_LOWER', type: 'string', length: 255, nullable: true)]
    private ?string $origNameLower = null;
    #[Column(name: 'IS_CITY', type: 'boolean', nullable: true)]
    private ?bool $isCity = null;
}
