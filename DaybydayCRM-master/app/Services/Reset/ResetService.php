<?php

namespace App\Services\Reset;

use Illuminate\Support\Facades\DB;

class ResetService
{
    public static function resetDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        $tablesToReset = [
            'mails',
            'comments',
            'absences',
            'leads',
            'projects',
            'tasks',
            'contacts',
            'appointments',
            'clients',
            'offers',
            'invoices',
            'invoice_lines',
            'payments'
        ];
    
        foreach (DB::select('SHOW TABLES') as $table) {
            $tableName = array_values((array) $table)[0];
    
            if (in_array($tableName, $tablesToReset)) {
                DB::table($tableName)->truncate();
            }
        }
    
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
}
