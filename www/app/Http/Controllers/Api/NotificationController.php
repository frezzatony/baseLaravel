<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\System\WebsocketService;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function fetchResumeUnreadNotifications(Request $request)
    {
        if ($request->get('show_notifications')) {
            $notifications =  auth()->user()->unreadNotificationsByUpdatedAtDesc->map(function ($notification) {
                return [
                    'id'        =>  $notification->id,
                    'time'      =>  $notification->updated_at,
                    'author'    =>  $notification->data['author'],
                    'title'     =>  $notification->data['title'],
                ];
            });
        }

        return response([
            'status'            =>  'success',
            'count'             =>  auth()->user()->unreadNotifications->count(),
            'notifications'     =>  !empty($notifications) ? $notifications : null,
        ], 200);
    }

    public function fetchNotification(Request $request)
    {
        $notification = DatabaseNotification::where([
            ['id', $request->get('id')],
            ['notifiable_id',  auth()->user()->id],
        ])
            ->limit(1)
            ->get()
            ->first();

        if (empty($notification)) {
            return response([
                'status'        =>  'error',
            ], 400);
        }
        $notification->markAsRead();
        return response([
            'status'        =>  'success',
            'notification'  =>  collect([
                'id'        =>  $notification->id,
                'time'      =>  $notification->updated_at,
                'author'    =>  $notification->data['author'],
                'title'     =>  $notification->data['title'],
                'text'      =>  $notification->data['text'],
            ])
        ], 200);
    }

    public function fetchMarkNotificationAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        WebsocketService::message('notifications.user.' . auth()->user()->api_token, json_encode([
            'action'    =>  'new_message',
        ]));
        return response([
            'status'    =>  'success',
        ], 200);
    }

    public function fetchMarkAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        WebsocketService::message('notifications.user.' . auth()->user()->api_token, json_encode([
            'action'    =>  'new_message',
        ]));
        return response([
            'status'    =>  'success',
        ], 200);
    }
}
