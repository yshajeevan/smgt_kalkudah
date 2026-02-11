<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CadreDataExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $structuredData;
    protected $cadres;

    public function __construct($structuredData, $cadres)
    {
        $this->structuredData = $structuredData;
        $this->cadres = $cadres;
    }

    public function array(): array
    {
        $exportData = [];

        foreach ($this->structuredData as $data) {
            $row = [$data['census'], $data['institute_name']];
            
            foreach ($data['cadres'] as $cadreData) {
                $row[] = $cadreData['approved'];
                $row[] = $cadreData['available'];
                $row[] = $cadreData['ex_de'];
            }

            $exportData[] = $row;
        }

        return $exportData;
    }

    public function headings(): array
    {
        $categoryHeadings = ['Census', 'School Name'];
        $subjectHeadings = ['', ''];
        $subHeadings = ['', ''];

        foreach ($this->cadres as $cadre) {
            $categoryHeadings[] = $cadre->category;
            $subjectHeadings[] = $cadre->cadre_code;
            $subjectHeadings[] = '';
            $subjectHeadings[] = '';
            $subHeadings[] = 'Approved';
            $subHeadings[] = 'Available';
            $subHeadings[] = 'Ex/DE';

            // Merge the "Category" heading across its corresponding "subject_code" columns
            $categoryHeadings = array_merge($categoryHeadings, ['', '']);
        }

        return [$categoryHeadings, $subjectHeadings, $subHeadings];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for the category and subject_code headers
        $currentColumn = 'C'; // Starting from column C since columns A and B are 'Census' and 'School Name'
        foreach ($this->cadres as $cadre) {
            // Merging the category
            $startColumn = $currentColumn;
            $endColumn = $this->incrementColumn($currentColumn, 2); // Move by 2 columns (Approved, Available, Ex/DE)
            $sheet->mergeCells("{$startColumn}1:{$endColumn}1"); // Merge cells for Category from start to end

            // Merging the subject_code
            $sheet->mergeCells("{$startColumn}2:{$endColumn}2"); // Merge cells for subject_code from start to end

            // Increment to the next set of columns
            $currentColumn = $this->incrementColumn($endColumn, 1);
        }

        // Center align the headers and subheadings
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Center align and bold headers
        $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()->setVertical('center');
        $sheet->getStyle("A1:{$lastColumn}3")->getFont()->setBold(true);

        // Fill blank cells with 0
        foreach ($sheet->getRowIterator(4, $lastRow) as $row) { // Start from row 4 (data rows)
            $cellIterator = $row->getCellIterator('A', $lastColumn);
            $cellIterator->setIterateOnlyExistingCells(false); // Include blank cells
            foreach ($cellIterator as $cell) {
                if (is_null($cell->getValue()) || $cell->getValue() === '') {
                    $cell->setValue(0);
                }
            }
        }
    }


    private function incrementColumn($column, $steps)
    {
        for ($i = 0; $i < $steps; $i++) {
            $column++;
            if (strlen($column) > 1 && ord($column[0]) > ord('Z')) {
                $column = 'A' . $column[1];
            }
        }
        return $column;
    }
}