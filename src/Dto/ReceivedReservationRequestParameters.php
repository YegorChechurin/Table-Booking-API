<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\Constraints as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\GroupSequence({
 *     "ValidDate",
 *     "ExistentTableId",
 *     "ReceivedReservationRequestParameters",
 *     "ReservationInterval"
 * })
 */
final class ReceivedReservationRequestParameters
{
    /**
     * @Assert\NotNull(
     *     message = "Required parameter",
     *     groups = {"ValidDate"}
     * )
     * @Assert\NotBlank(
     *     message = "Required parameter",
     *     groups = {"ValidDate"}
     * )
     * @Assert\Date(
     *     message = "Invalid date format, supported date format is YYYY-MM-DD",
     *     groups = {"ValidDate"}
     * )
     * @CustomAssert\CurrentDateOrGreater(groups = {"ValidDate"})
     */
    private ?string $date;

    /**
     * @Assert\NotNull(message = "Required parameter")
     * @Assert\NotBlank(message = "Required parameter")
     * @Assert\DateTime(
     *     format = "Y-m-d H:i",
     *     message = "Invalid time format, supported time format is YYYY-MM-DD HH:MM"
     * )
     * @CustomAssert\FromDateIsSameAsDate(propertyPath = "date")
     */
    private ?string $from;

    /**
     * @Assert\NotNull(message = "Required parameter")
     * @Assert\NotBlank(message = "Required parameter")
     * @Assert\DateTime(
     *     format = "Y-m-d H:i",
     *     message = "Invalid time format, supported time format is YYYY-MM-DD HH:MM"
     * )
     * @CustomAssert\GreaterByAtLeastHalfAnHour(
     *     propertyPath = "from",
     *     groups = {"ReservationInterval"}
     * )
     */
    private ?string $to;

    /**
     * @var int|null
     *
     * @CustomAssert\ExistentTableId(groups = "ExistentTableId")
     */
    private $table_id;

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(?string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getTableId(): ?int
    {
        return $this->table_id;
    }

    public function setTableId(?int $table_id): self
    {
        $this->table_id = $table_id;

        return $this;
    }
}
