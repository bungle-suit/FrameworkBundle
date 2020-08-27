<?php
declare(strict_types=1);

namespace Bungle\Framework\Export\ExcelWriter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Base class of ExcelReader, ExcelWriter.
 */
class ExcelOperator
{
    protected Spreadsheet $book;
    protected Worksheet $sheet;
    protected int $row = 1;

    /**
     * Use the active worksheet as current worksheet.
     */
    public function __construct(Spreadsheet $book)
    {
        $this->book = $book;
        $this->sheet = $book->getActiveSheet();
    }

    /**
     * @return int current row no start from 1
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * Goto specific $row.
     */
    public function setRow(int $row): void
    {
        $this->row = $row;
    }

    /**
     * Goto next row.
     */
    public function nextRow(int $n = 1): void
    {
        $this->row += $n;
    }

    public function getSheet(): Worksheet
    {
        return $this->sheet;
    }

    public function getBook(): Spreadsheet
    {
        return $this->book;
    }
}
