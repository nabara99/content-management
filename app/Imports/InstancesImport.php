<?php

namespace App\Imports;

use App\Models\Instance;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InstancesImport implements ToModel, WithHeadingRow
{
    private int $successCount = 0;
    private array $failures = [];
    private int $currentRow = 1;

    public function model(array $row)
    {
        $this->currentRow++;

        $name = $row['name'] ?? null;

        if (empty($name)) {
            return null;
        }

        $validator = Validator::make([
            'name' => $name,
        ], [
            'name' => ['required', 'string', 'max:255', 'unique:instance,name'],
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            $this->failures[] = "Baris {$this->currentRow} ({$name}): {$errors}";
            return null;
        }

        $this->successCount++;

        return new Instance(['name' => $name]);
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}
