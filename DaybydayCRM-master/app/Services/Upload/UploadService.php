<?php

namespace App\Services\Upload;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Absence;
use App\Models\Activity;
// use App\Models\Appointment;
// use App\Models\BusinessHour;
// use App\Models\Client;
// use App\Models\Comment;
// use App\Models\Contact;
// use App\Models\Department;
// use App\Models\Document;
// use App\Models\Integration;
// use App\Models\Invoice;
// use App\Models\InvoiceLine;
// use App\Models\Lead;
// use App\Models\Mail;
// use App\Models\Offer;
// use App\Models\Payment;
// use App\Models\PermissionRole;
// use App\Models\Product;
// use App\Models\Project;
// use App\Models\Role;
// use App\Models\RoleUser;
// use App\Models\Setting;
// use App\Models\Status;
// use App\Models\Task;
// use App\Models\User;
use Exception;

class UploadService {

    public static function importFromCsv($filePath)
    {
        DB::beginTransaction();  

        try {
            $data = array_map('str_getcsv', file($filePath));

            
            $data = array_filter($data, function ($row) {
                return array_filter($row);  
            });

            $groupedData = [];
            $currentTable = null;
            $headers = [];

            foreach ($data as $row) {
                if (empty(array_filter($row))) {
                    continue; 
                }

                if (strtolower(trim($row[0])) == 'table_name') {
                     
                    $currentTable = strtolower(trim($row[1]));
                    $headers = []; 
                    continue;
                }

                if (empty($headers)) {
                    
                    $headers = array_map('trim', $row);
                    continue;
                }

                 
                $rowData = array_combine($headers, $row);

                if (!isset($groupedData[$currentTable])) {
                    $groupedData[$currentTable] = [];
                }
                $groupedData[$currentTable][] = $rowData;
            }

            
            if (!empty($groupedData['absences'])) {
                self::importAbsences($groupedData['absences']);
            }

            
            if (!empty($groupedData['activities'])) {
                self::importActivities($groupedData['activities']);
            }

            
            DB::commit();

            return "Importation terminée avec succès !";
        } catch (Exception $e) {
            
            DB::rollBack();
            return "Erreur lors de l'importation : " . $e->getMessage();
        }
    }

    private static function importAbsences($data)
    {   
        foreach ($data as $row) {
            $validator = Validator::make($row, [
                'external_id' => 'required|string|unique:absences,external_id',
                'reason'      => 'required|string|max:255',
                'start_at'    => 'required|date|before_or_equal:end_at',
                'end_at'      => 'required|date|after_or_equal:start_at',
                'user_id'     => 'required|exists:users,id',
                'comment'     => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                throw new Exception("Erreur de validation (Absences) : " . implode(', ', $validator->errors()->all()));
            }

            Absence::create($row);
        }
    }

    private static function importActivities($data)
    {
        foreach ($data as $row) {
            $validator = Validator::make($row, [
                'causer_id'   => 'required|integer|exists:users,id',
                'causer_type' => 'required|string',
                'text'        => 'required|string|max:255',
                'source_type' => 'required|string',
                'source_id'   => 'required|integer',
                'properties'  => 'nullable|json',
            ]);

            if ($validator->fails()) {
                throw new Exception("Erreur de validation (Activities) : " . implode(', ', $validator->errors()->all()));
            }

            Activity::create($row);
        }
    }

}