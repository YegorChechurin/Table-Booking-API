<?php

declare(strict_types=1);

namespace App\Dto;

final class ValidReservationRequestParameters
{
    private string $date;

    private string $from;

    private string $to;

    private int $tableId;

    public function __construct(string $date, string $from, string $to, int $tableId)
    {
        $this->date = $date;
        $this->from = $from;
        $this->to = $to;
        $this->tableId = $tableId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getTableId(): int
    {
        return $this->tableId;
    }
}
