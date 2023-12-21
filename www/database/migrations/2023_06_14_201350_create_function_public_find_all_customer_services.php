<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE FUNCTION public.findAllCustomerServices()
                RETURNS TABLE (
                    customer_service_id BIGINT, 
                    queue_id BIGINT, 
                    status VARCHAR,
                    queues_matters_id BIGINT,
                    book_ticket TEXT,
                    book_id BIGINT, 
                    activity JSON, 
                    form_data JSONB, 
                    tags JSONB, 
                    attachments_catalog_id BIGINT, 
                    users_id_responsibility INT, 
                    transferred_at TIMESTAMP, completed_at TIMESTAMP,
                    created_at TIMESTAMP, updated_at TIMESTAMP) AS $$
            DECLARE
                queue RECORD;
                table_name TEXT;
                query TEXT;
            BEGIN
                FOR queue IN 
                    SELECT id, type 
                    FROM queues
                LOOP
                query := '

                    WITH 
                        activities AS (
                            SELECT 
                                TCS.id,
                                jsonb_array_elements(TCS.activity || COALESCE(TBOOK.activity,''[]'')) AS activity
                            FROM customer_services.queue_' || queue.id || ' TCS
                            LEFT JOIN books.queue_' || queue.id || ' TBOOK ON TBOOK.id = TCS.book_id
                        ),
                    
                        customer_service_activities AS(
                            SELECT 
                                activities.id,
                                JSON_AGG(
                                    JSON_BUILD_OBJECT(
                                        ''time'',activity->>''time'',
                                        ''user_id'',activity->>''users_id'',
                                        ''user_name'',users.name,
                                        ''user_social_name'',users.social_name,
                                        ''activity'',activity
                                    ) 
                                    ORDER BY activity->>''time'' DESC
                                ) activity
                            FROM activities
                            LEFT JOIN users ON users.id = (activity->>''users_id'')::BIGINT
                            GROUP BY activities.id
                        )
                SELECT 
                    CustomerServiceTable.id AS customer_service_id, ''' || queue.id || '''::BIGINT AS queue_id, 
                    CustomerServiceTable.status,
                    TBOOKS.queues_matters_id::BIGINT, 
                    (CASE
                        WHEN TBOOKS.ticket::text ~ ''^[0-9\.]+$'' THEN LPAD(TBOOKS.ticket::text,3,''0'')::TEXT
                        ELSE TBOOKS.ticket::TEXT 
                        END             
                    ) AS book_ticket,
                    CustomerServiceTable.book_id::BIGINT, 
                    customer_service_activities.activity,
                    CustomerServiceTable.form_data, CustomerServiceTable.tags, CustomerServiceTable.attachments_catalog_id,
                    CustomerServiceTable.users_id_responsibility,
                    CustomerServiceTable.transferred_at, CustomerServiceTable.completed_at,
                    CustomerServiceTable.created_at, CustomerServiceTable.updated_at 
                FROM customer_services.queue_' || queue.id || ' AS CustomerServiceTable
                LEFT JOIN books.queue_' || queue.id || ' AS TBOOKS ON TBOOKS.id = CustomerServiceTable.book_id
                JOIN customer_service_activities ON customer_service_activities.id = CustomerServiceTable.id'
                ;
                RETURN QUERY EXECUTE query;
                END LOOP;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    public function down(): void
    {
        DB::statement("DROP FUNCTION public.findallcustomerservices();");
    }
};
