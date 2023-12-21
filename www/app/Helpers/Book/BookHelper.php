<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class BookHelper
{
    public function up($customers_services_id): void
    {  
        DB::beginTransaction(); // Inicia a transação

        try {
            Schema::create('books.book_' . strval($customers_services_id), function (Blueprint $table) {
                $table->uuid();
                $table->jsonb("attributes")->default("[]");
                $table->timestamp("appointment_time");
                $table->timestamp("canceled_at")->nullable();
                $table->timestamp("realized_at")->nullable();
                $table->timestamp("confirmed_at")->nullable();
                $table->timestamps();
            });

            DB::commit(); // Realiza o commit da transação
        } catch (\Exception $e) {
            DB::rollback(); // Realiza o rollback da transação em caso de erro
            throw $e; // Lança a exceção para tratamento posterior
        }
    }
};