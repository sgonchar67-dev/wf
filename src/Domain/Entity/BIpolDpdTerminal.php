<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * BIpolDpdTerminal
 */
#[Table(name: 'b_ipol_dpd_terminal')]
#[Entity]
class BIpolDpdTerminal
{
    #[Column(name: 'ID', type: 'integer', nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'LOCATION_ID', type: 'string', length: 255, nullable: true)]
    private ?string $locationId = null;
    #[Column(name: 'CODE', type: 'string', length: 255, nullable: true)]
    private ?string $code = null;
    #[Column(name: 'NAME', type: 'string', length: 255, nullable: true)]
    private ?string $name = null;
    #[Column(name: 'ADDRESS_FULL', type: 'string', length: 255, nullable: true)]
    private ?string $addressFull = null;
    #[Column(name: 'ADDRESS_SHORT', type: 'string', length: 255, nullable: true)]
    private ?string $addressShort = null;
    #[Column(name: 'ADDRESS_DESCR', type: 'text', length: 65535, nullable: true)]
    private ?string $addressDescr = null;
    #[Column(name: 'PARCEL_SHOP_TYPE', type: 'string', length: 255, nullable: true)]
    private ?string $parcelShopType = null;
    #[Column(name: 'SCHEDULE_SELF_PICKUP', type: 'string', length: 255, nullable: true)]
    private ?string $scheduleSelfPickup = null;
    #[Column(name: 'SCHEDULE_SELF_DELIVERY', type: 'string', length: 255, nullable: true)]
    private ?string $scheduleSelfDelivery = null;
    #[Column(name: 'SCHEDULE_PAYMENT_CASH', type: 'string', length: 255, nullable: true)]
    private ?string $schedulePaymentCash = null;
    #[Column(name: 'SCHEDULE_PAYMENT_CASHLESS', type: 'string', length: 255, nullable: true)]
    private ?string $schedulePaymentCashless = null;
    #[Column(name: 'IS_LIMITED', type: 'string', length: 1, nullable: false, options: ['default' => 'N', 'fixed' => true])]
    private string $isLimited = 'N';
    #[Column(name: 'LIMIT_MAX_SHIPMENT_WEIGHT', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitMaxShipmentWeight = '0';
    #[Column(name: 'LIMIT_MAX_WEIGHT', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitMaxWeight = '0';
    #[Column(name: 'LIMIT_MAX_LENGTH', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitMaxLength = '0';
    #[Column(name: 'LIMIT_MAX_WIDTH', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitMaxWidth = '0';
    #[Column(name: 'LIMIT_MAX_HEIGHT', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitMaxHeight = '0';
    #[Column(name: 'LIMIT_MAX_VOLUME', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitMaxVolume = '0';
    #[Column(name: 'LIMIT_SUM_DIMENSION', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $limitSumDimension = '0';
    #[Column(name: 'LATITUDE', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $latitude = '0';
    #[Column(name: 'LONGITUDE', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $longitude = '0';
    #[Column(name: 'NPP_AMOUNT', type: 'float', precision: 10, scale: 0, nullable: false)]
    private string|float $nppAmount = '0';
    #[Column(name: 'NPP_AVAILABLE', type: 'string', length: 1, nullable: false, options: ['default' => 'N', 'fixed' => true])]
    private string $nppAvailable = 'N';
    #[Column(name: 'SERVICES', type: 'text', length: 65535, nullable: true)]
    private ?string $services = null;
}
