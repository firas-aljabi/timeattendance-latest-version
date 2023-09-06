<?php

namespace App\Repository\Alerts;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Events\AddAlertEvent;
use App\Filter\Alerts\AlertFilter;
use App\Models\Alert;
use App\Models\User;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\UserTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlertRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = Alert::query()->where('email', '=', Auth::user()->email);
        if ($filter instanceof AlertFilter) {

            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });


            return $records->get();
        }
        return $records->get();
    }

    public function create_alert($data)
    {
        DB::beginTransaction();
        try {

            if (auth()->user()->type == UserTypes::ADMIN) {

                $alert = new Alert();
                $alert->email = $data['email'];
                $alert->content = $data['content'];
                $alert->type = $data['type'];
                $alert->save();

                $user = Auth::user();
                $alert = Alert::findOrFail($alert->id);
                $notifier = User::where('email', $alert->email)->first();
                event(new AddAlertEvent($notifier, $alert, $user));

                DB::commit();

                if ($alert === null) {
                    return ['success' => false, 'message' => "Alert was not created"];
                }
                return ['success' => true, 'data' => $alert];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return Alert::class;
    }
}
