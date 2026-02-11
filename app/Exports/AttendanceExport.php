<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use DB;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Attendance::selectRaw('"Batticaloa West",count(id) as noschools,sum(principal) as prprincipal,"0" as virprin,sum(principal) as totprincipal, round(sum(principal)/count(id)*100,2) as percprincipal,
        sum(tottea) as tea,sum(prtea) as prtea,"0" as virtea,sum(prtea) as tottea,round(sum(prtea)/sum(tottea)*100,2) as perctea,
        sum(totstu) as stu,sum(prstu) as prstu,"0" as virstu,sum(prstu) as totstu,round(sum(prstu)/sum(totstu)*100,2) as percstu,
        (sum(tottrainee)+sum(totnonacademic)) as totnonac,(sum(prtrainee)+sum(prnonacademic)) as prnonac,round((sum(prtrainee)+sum(prnonacademic))/(sum(tottrainee)+sum(totnonacademic))*100,2) as percnonac')
                ->groupBy(DB::Raw('Date(created_at)'))->wheredate('created_at', Carbon::now()->format('Y-m-d'))->get();
    }
    public function headings(): array
    {
        return ["Name of the zone ", "Total no.of schools", "No of incharge principals attended physically","No of incharge principals attended virtually","Total no of incharge princiapls attended","Percentage of attendenace",
        "Total no.of teachers","No of teachers attended physically","No of teachers attended virtually","Total no.of teachers attended","Percentage of attendenace",
        "Total no.of students","No of Students attended physically","No of students attended virtually","Total no.of Students attended","Percentage of attendenace",
        "Total no.of non-academic staff","Total no.of non-cademic staff attended","Percentage of attendenace"];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->getAlignment()->setWrapText(true);
    }

}