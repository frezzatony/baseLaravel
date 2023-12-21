<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->updateTimestampsColumns();

        $this->call(ModulesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(RoutinesTableSeeder::class);
        $this->call(RoutinesActionsTableSeeder::class);
        $this->call(ProfileRoutinesActionsTableSeeder::class);
        $this->call(ModulesMenusTableSeeder::class);
        $this->call(HolidaysTableSeeder::class);

        $this->updateSequences();
    }

    private function updateSequences()
    {
        $sql = "
            SELECT 'SELECT SETVAL(' ||
                quote_literal(quote_ident(PGT.schemaname) || '.' || quote_ident(S.relname)) ||
                ', COALESCE(MAX(' ||quote_ident(C.attname)|| '), 1) ) FROM ' ||
                quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';' as q
            FROM pg_class AS S,
                pg_depend AS D,
                pg_class AS T,
                pg_attribute AS C,
                pg_tables AS PGT
            WHERE S.relkind = 'S'
                AND S.oid = D.objid
                AND D.refobjid = T.oid
                AND D.refobjid = C.attrelid
                AND D.refobjsubid = C.attnum
                AND T.relname = PGT.tablename
            ORDER BY S.relname;
        ";

        foreach (DB::select($sql) as $update) {
            DB::select($update->q);
        }
    }

    private function updateTimestampsColumns()
    {
        $columns = DB::table('information_schema.columns')
            ->selectRaw('
            table_schema, table_name, column_name
        ')
            ->where('column_name', '=', 'created_at')
            ->orWhere('column_name', '=', 'updated_at')
            ->get();
        foreach ($columns as $column) {
            $sql = "
                ALTER TABLE {$column->table_schema}.{$column->table_name} ALTER COLUMN {$column->column_name} SET DEFAULT CURRENT_TIMESTAMP;
            ";
            DB::select($sql);
        }
    }
}
