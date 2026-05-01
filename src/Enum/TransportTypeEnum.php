<?php

namespace App\Enum;

enum TransportTypeEnum: string
{
    case FLIGHT = 'FLIGHT';
    case TRAIN = 'TRAIN';
    case BUS = 'BUS';
    case CAR = 'CAR';
    case TAXI = 'TAXI';
    case AVION = 'Avion';
    case BUS_FR = 'Bus';
    case TRAIN_FR = 'Train';
    case TAXI_FR = 'Taxi';
    case VELOCATION = 'Vélocation';
}
