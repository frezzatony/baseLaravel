<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;

class FirstComeManualBook extends CoreModel
{
    public $table = "";

    protected $fillable = [
        'id', 'id_parent', 'ticket', 'authentication_code', 'queues_matters_id', 'queues_call_orders_id', 'calls', 'status', 'users_id_started', 'users_id_completed',
        'activity', 'started_at', 'transferred_at', 'completed_at',
    ];

    public function sqlCountCalls($queue)
    {
        return "
            SELECT
                COUNT(*) AS calls_count, id
            FROM
                books.queue_{$queue->id}
            CROSS JOIN jsonb_array_elements(activity) AS activity_item
            WHERE
                activity_item->>'action' = 'call'
            GROUP BY id
        ";
    }
}
