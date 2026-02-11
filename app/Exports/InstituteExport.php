<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class InstituteExport implements FromCollection, WithHeadings
{
    protected $institutes;

    public function __construct($institutes)
    {
       $this->institutes = $institutes;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
       return $this->institutes;
    }

    public function headings(): array
    {
        return array_keys($this->collection()->first());
    }
}