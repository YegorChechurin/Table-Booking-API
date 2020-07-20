<?php

declare(strict_types=1);

namespace App\Dto;

final class ReservationParameters
{
    private string $tableId;

    private string $startTime;

    private string $endTime;

    private string $price;

    public function __construct(string $tableId, string $startTime, string $endTime, string $price)
    {
        $this->tableId = $tableId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->price = $price;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getTableId(): string
    {
        return $this->tableId;
    }
}
