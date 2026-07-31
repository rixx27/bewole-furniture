<?php

namespace App\Enums;

enum ShippingMethod: string
{
    case Expedition = 'expedition';
    case InternalDelivery = 'internal_delivery';
    case SelfPickup = 'self_pickup';

    public function label(): string
    {
        return match ($this) {
            self::Expedition => 'Ekspedisi',
            self::InternalDelivery => 'Antar Sendiri',
            self::SelfPickup => 'Ambil Sendiri',
        };
    }
}
