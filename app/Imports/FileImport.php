<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use App\Models\Manufacturer;
use App\Models\Rubric;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class FileImport implements ToModel, WithStartRow, WithChunkReading, WithUpserts, WithBatchInserts
{
    private $rows = 0;
    private $wasRecentlyCreated = 0;
    private $wasNotRecentlyCreated = 0;
    private $rowsWithInvalidData = 0;
    private $headingsCount;

    public function __construct($headingsCount)
    {
        $this->headingsCount = $headingsCount;
    }

    public function model(array $row)
    {
        ++$this->rows;

        return $this->save($row);
    }

    public function save($row)
    {
        $row = array_values(array_filter($row));

        if  (count($row) == $this->headingsCount) {
            $rubric1 = Rubric::firstOrCreate([
                'name' => $row[0],
                'parent_id' => null
            ]);

            $rubric2 = Rubric::firstOrCreate([
                'name' =>$row[1],
                'parent_id' => $rubric1->id
            ]);

            $category = Category::firstOrCreate([
                'name' => $row[2]
            ]);

            $manufacturer = Manufacturer::firstOrCreate([
                'name' => $row[3]
            ]);

            $item = Item::firstOrCreate([
                'name' => $row[4],
                'manufacturer_part_number' => $row[5],
                'description' => $row[6],
                'price' => $row[7],
                'warranty' => $row[8],
                'in_stock' => $row[9],
                'rubric_id' => $rubric2->id,
                'category_id' => $category->id,
                'manufacturer_id' => $manufacturer->id,
            ]);

            $item->wasRecentlyCreated ? ++$this->wasRecentlyCreated : ++$this->wasNotRecentlyCreated;

            return $item;
        } else {
            ++$this->rowsWithInvalidData;
        }
    }

    public function getRowCount(): object
    {
        $rows = (object)[];

        $rows->count = $this->rows;
        $rows->newRows = $this->wasRecentlyCreated;
        $rows->duplicatedRows = $this->wasNotRecentlyCreated;
        $rows->inwalidRows = $this->rowsWithInvalidData;

        return $rows;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function uniqueBy(): string
    {
        return 'name';
    }

}
