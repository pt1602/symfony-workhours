<?php

namespace App\Entity;

use App\Repository\WeekRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeekRepository::class)]
class Week
{
    /**
     * Contractual hours to reach in a full week.
     */
    public const float WEEKLY_TARGET_HOURS = 40.0;


    /**
     * Default hours per day.
     */
    public const array DEFAULT_WORK_HOURS = [8.75, 8.75, 8.5, 8.5, 6];

    /**
     * Days that count towards the weekly target in calendar order.
     *
     * @var list<string>
     */
    public const array WORKDAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $calenderWeek = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $workHours = null;

    #[ORM\Column]
    private ?int $year = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalenderWeek(): ?int
    {
        return $this->calenderWeek;
    }

    public function setCalenderWeek(int $calenderWeek): static
    {
        $this->calenderWeek = $calenderWeek;

        return $this;
    }

    public function getWorkHours(): ?array
    {
        return $this->workHours;
    }

    public function setWorkHours(?array $workHours): static
    {
        $this->workHours = $workHours;

        return $this;
    }

    public function getTotalHours(): float
    {
        return array_sum($this->workHours ?? []);
    }

    /**
     * Hours still to be worked to reach the weekly target (never negative).
     */
    public function getRemainingHours(): float
    {
        return max(0.0, self::WEEKLY_TARGET_HOURS - $this->getTotalHours());
    }

    public function isTargetReached(): bool
    {
        return $this->getTotalHours() >= self::WEEKLY_TARGET_HOURS;
    }

    /**
     * Workdays that have no hours logged yet.
     *
     * @return list<string>
     */
    public function getOpenWorkdays(): array
    {
        $hours = $this->workHours ?? [];

        return array_values(array_filter(
            self::WORKDAYS,
            static fn(string $day): bool => (float)($hours[$day] ?? 0) <= 0.0,
        ));
    }

    /**
     * Forecast: hours per remaining workday needed to still hit the target,
     * or null when the target is met or no workday is left.
     */
    public function getForecastPerOpenWorkday(): ?array
    {
        $openDays = $this->getOpenWorkdays();
        $openDaysCount = \count($openDays);

        if ($openDaysCount === 0 || $this->isTargetReached()) {
            return null;
        }

        $defaultWorkHours = self::DEFAULT_WORK_HOURS;
        $workHoursOnFriday = end($defaultWorkHours);
        $remainingHours = $this->getRemainingHours();
        $forecast = [];

        if (in_array('friday', $openDays, true) && array_first($openDays) !== 'friday') {
            $remainingHours -= $workHoursOnFriday;
            $openDaysCount--;

            foreach ($openDays as $day) {
                if ($day !== 'friday') {
                    $forecast += [$day => $remainingHours / $openDaysCount];
                } else {
                    $forecast += [$day => $workHoursOnFriday];
                }
            }
        } else {
            foreach ($openDays as $day) {
                $forecast += [$day => $remainingHours / $openDaysCount];
            }
        }

        return $forecast;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }
}
