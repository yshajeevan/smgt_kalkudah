<?php

namespace App\Imports;

use App\Models\Institute;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use DB;
class StudentsImport implements ToCollection, WithChunkReading, WithStartRow
{
    /**
    * @param array $rows
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $row) {
            $import = new Student;
            $inst = Institute::where('id', $row[0])->value('institute');
            $import->institute_id = $inst;
            $import->admision_number = $row[1];
            $import->birth_number = $row[2];
            $import->name = $row[3];
            $import->dob = $row[4];
            $import->gender = $row[5];
            $import->ethnicity = $row[6];
            $import->religion = $row[7];
            $import->grade_id = $row[8];
            $import->photo = $row[9];
            $import->cadresubject1_id = $row[10];
            $import->cadresubject2_id = $row[11];
            $import->cadresubject3_id = $row[12];
            $import->address = $row[13];
            $import->dsdivision_id = $row[14];
            $import->gndivision_id = $row[15];
            $import->mobile = $row[16];
            $import->father_name = $row[17];
            $import->father_nic = $row[18];
            $import->father_mobile = $row[19];
            $import->mother_name = $row[20];
            $import->mother_nic = $row[21];
            $import->mother_mobile = $row[22];
            $import->status = $row[23];
            $import->save();
        }

    }
    
    public function chunkSize(): int
    {
        return 100;
    }
    
    public function startRow(): int
    {
        return 2;
    }
}
