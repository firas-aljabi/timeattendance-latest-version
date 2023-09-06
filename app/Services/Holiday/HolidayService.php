<?php

namespace App\Services\Holiday;

use App\Filter\Holiday\HolidayFilter;
use App\Interfaces\Holiday\HolidayServiceInterface;
use App\Repository\Holiday\HolidayRepository;

class HolidayService implements HolidayServiceInterface
{

    public function __construct(private HolidayRepository $holidayRepository)
    {
    }


    public function create_weekly_holiday($data)
    {
        return $this->holidayRepository->create_weekly_holiday($data);
    }


    public function create_annual_holiday($data)
    {
        return $this->holidayRepository->create_annual_holiday($data);
    }
    public function update_weekly_holiday($data)
    {
        return $this->holidayRepository->update_weekly_holiday($data);
    }
    public function update_annual_holiday($data)
    {
        return $this->holidayRepository->update_annual_holiday($data);
    }

    public function list_of_holidays(HolidayFilter $holidayFilter = null)
    {
        if ($holidayFilter != null)
            return $this->holidayRepository->list_of_holidays($holidayFilter);
        else
            return $this->holidayRepository->paginate();
    }
}
