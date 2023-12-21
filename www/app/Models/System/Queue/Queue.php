<?php

namespace App\Models\System\Queue;

use App\Models\CoreModel;

class Queue extends CoreModel
{
    protected $table = 'queues';
    protected $fillable = [
        'id', 'attendance_units_id', 'description', 'is_active', 'type', 'point_name', 'point_quantity', 'ticket_prefix', 'ticket_sequence',
        'ticket_withdrawal', 'ticket_reset', 'reset_tickets_counter', 'max_daily_tickets', 'provided_daily_tickets', 'last_tickets',
    ];
}
