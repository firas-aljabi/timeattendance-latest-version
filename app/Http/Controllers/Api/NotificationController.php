<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;


/**
 * @group Notifications
 * @authenticated
 * APIs for managing Notifications
 */
class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Show My Notifications List And Number Of Unread Notification
     *
     * This endpoint is used to display the list of my Notifications And Number Of Unread Notification and authenticate employee access to this API. It will show the Notifications specific to the authenticated employee.
     *
     * @response 200 scenario="Show My Notifications List And Number Of Unread Notification"{
     *"data": [
     *  {
     *  "id": 6,
     *  "notifier_id": 3,
     *  "post_id": 1,
     *  "user_id": 3,
     *   "message": "New Like Added To Your Post By mouaz alkhateeb",
     *   "read_at": null,
     *    "company_id": 1,
     *    "created_at": "2023-08-27T19:01:39.000000Z",
     *    "updated_at": "2023-08-27T19:01:39.000000Z"
     *},
     * {
     *     "id": 5,
     *     "notifier_id": 3,
     *    "post_id": 1,
     *     "user_id": 3,
     *     "message": "New Comment Added To Your Post By mouaz alkhateeb",
     *     "read_at": null,
     *     "company_id": 1,
     *     "created_at": "2023-08-27T19:01:26.000000Z",
     *     "updated_at": "2023-08-27T19:01:26.000000Z"
     * },
     * {
     * "id": 4,
     * "notifier_id": 3,
     * "post_id": null,
     * "user_id": 2,
     * "message": "You Have New Alert From Admin For Many Reasons",
     * "read_at": null,
     * "company_id": 1,
     * "created_at": "2023-08-27T18:59:17.000000Z",
     * "updated_at": "2023-08-27T18:59:17.000000Z"
     * },
     * {
     *   "id": 3,
     *    "notifier_id": 3,
     *   "post_id": null,
     *    "user_id": 2,
     *    "message": "You Have New Alert From Admin Because FABRICATE PROBLEMS",
     *    "read_at": null,
     *    "company_id": 1,
     *    "created_at": "2023-08-27T15:33:04.000000Z",
     *    "updated_at": "2023-08-27T15:33:04.000000Z"
     * }
     *],
     *"unread_notification": 4
     *}
     */
    public function index()
    {
        $notifications = Notification::where('notifier_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $unread_notification = Notification::where('notifier_id', auth()->user()->id)->where('read_at', null)
            ->count();
        return response()->json([
            'data' => $notifications,
            "unread_notification" => $unread_notification
        ]);
    }
}
