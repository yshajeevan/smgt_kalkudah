<?php

namespace App\Imports;

use App\Models\Institute;
use App\Models\SalaryTeacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use DB;
class SalaryImport implements ToCollection, WithChunkReading, WithStartRow
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
            $import = new SalaryTeacher;
            $import->nic = $row[48];
            $import->empno = $row[0];
            $import->name = $row[1].$row[3].$row[2];
            $import->institute = $row[13];
            $import->designation = $row[8];
            $import->service = $row[9];
            $import->sal_month = $row[8];
            $import->paid = $row[29];
            $import->sal_banked = $row[41];
            $import->bankcode = $row[42];
            $import->status = $row[54];
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
