<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Holiday\CreateAnnualHolidayRequest;
use App\Http\Requests\Holiday\CreateWeeklyHolidayRequest;
use App\Http\Requests\Holiday\GetHolidaysRequest;
use App\Http\Requests\Holiday\UpdateAnnualHolidayRequest;
use App\Http\Requests\Holiday\UpdateWeeklyHolidayRequest;
use App\Http\Resources\Holiday\AnnualHolidayResource;
use App\Http\Resources\Holiday\HolidayRrsource;
use App\Http\Resources\Holiday\WeeklyHolidayResource;
use App\Http\Resources\PaginationResource;
use App\Services\Holiday\HolidayService;

/**
 * @group Holidays
 *
 * @authenticated
 *
 * APIs for managing Holidays
 */
class HolidayController extends Controller
{
    public function __construct(private HolidayService $holidayService)
    {
    }
    /**
     * Create Weekly Holiday
     *
     * This endpoint is used to Create Weekly Holiday and  Admin Or Hr Can Access To This Api.
     *
     * @bodyParam day int required Bust Be From 1 to 7 Custom Example: 5
     * @bodyParam date date required Custom Example: 2023-08-27
     * @response 200 scenario="Create Weekly Holiday"{
     *"data": {
     *"id": 1,
     *"day": 6,
     * "date": "2023-06-01"
     *}
     *}
     */
    public function create_weekly_holiday(CreateWeeklyHolidayRequest $request)
    {
        $createdData =  $this->holidayService->create_weekly_holiday($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = WeeklyHolidayResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Create Annual Holiday
     *
     * This endpoint is used to Create Annual Holiday and  Admin Or Hr Can Access To This Api.
     *
     * @bodyParam holiday_name string Custom Example: Eid Aladha
     * @bodyParam start_date date Custom Example: 2023-08-27
     * @bodyParam end_date date Custom Example: 2023-08-29
     * @response 200 scenario="Create Annual Holiday"{
     *"data": {
     * "id": 3,
     * "holiday_name": "Eid Aladha",
     * "start_date": "2023-06-01",
     * "end_date": "2023-06-04"
     * }
     */
    public function create_annual_holiday(CreateAnnualHolidayRequest $request)
    {
        $createdData =  $this->holidayService->create_annual_holiday($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = AnnualHolidayResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Update Weekly Holiday
     *
     * This endpoint is used to Update Weekly Holiday and  Admin Or Hr Can Access To This Api.
     *
     * @bodyParam holiday_id int required Must Be Exists In Holidays Table
     *
     * @bodyParam day int required Bust Be From 1 to 7 Custom Example: 5
     *
     * @bodyParam date date required Custom Example: 2023-08-27
     *
     * @response 200 scenario="Update Weekly Holiday"{
     *"data": {
     *"id": 1,
     *"day": 6,
     * "date": "2023-06-01"
     *}
     *}
     */
    public function update_weekly_holiday(UpdateWeeklyHolidayRequest $request)
    {
        $createdData =  $this->holidayService->update_weekly_holiday($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = WeeklyHolidayResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Update Annual Holiday
     *
     * This endpoint is used to Upadate Annual Holiday and  Admin Or Hr Can Access To This Api.
     *
     * @bodyParam holiday_id int required Must Be Exists In Holidays Table
     *
     * @bodyParam holiday_name string Custom Example: Eid Aladha
     *
     * @bodyParam start_date date Custom Example: 2023-08-27
     *
     * @bodyParam end_date date Custom Example: 2023-08-29
     *
     * @response 200 scenario="Update Annual Holiday"{
     *"data": {
     * "id": 3,
     * "holiday_name": "Eid Aladha almobarak",
     * "start_date": "2023-06-03",
     * "end_date": "2023-06-06"
     * }
     */
    public function update_annual_holiday(UpdateAnnualHolidayRequest $request)
    {
        $createdData =  $this->holidayService->update_annual_holiday($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = AnnualHolidayResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * List Of Holidays
     *
     * This endpoint is used to Show Holidays List in the Company and Admin Or Hr Can Access To This Api.
     *
     * @response 200 scenario="Show Holidays"{
     *"data": [
     * {
     * "id": 2,
     * "type": 1,
     *  "day": 6,
     *  "date": "2023-06-01"
     *},
     *{
     *  "id": 3,
     * "type": 2,
     * "holiday_name": "Eid Aladha almobarak",
     * "start_date": "2023-06-02",
     * "end_date": "2023-06-06"
     *}
     *]
     * }
     */
    public function list_of_holidays(GetHolidaysRequest $request)
    {
        $data = $this->holidayService->list_of_holidays($request->generateFilter());
        $returnData = HolidayRrsource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }
}
