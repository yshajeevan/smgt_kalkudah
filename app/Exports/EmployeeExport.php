<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class EmployeeExport implements FromCollection, WithHeadings
{
    protected $employees;

    public function __construct($employees)
    {
       $this->employees = $employees;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
       return $this->employees;
    }

    public function headings(): array
    {
        return array_keys($this->collection()->first());
    }
}