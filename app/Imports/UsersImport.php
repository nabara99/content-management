<?php

namespace App\Imports;

use App\Models\Instance;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    private int $successCount = 0;
    private array $failures = [];
    private int $currentRow = 1;

    public function model(array $row)
    {
        $this->currentRow++;

        $name = $row['name'] ?? null;
        $email = $row['email'] ?? null;
        $password = $row['password'] ?? null;
        $instanceName = $row['instance'] ?? null;

        if (empty($name) && empty($email) && empty($password)) {
            return null;
        }

        // Resolve instance by name
        $instance = null;
        if (!empty($instanceName)) {
            $instance = Instance::where('name', $instanceName)->first();
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'instance' => $instanceName,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
            ],
            'instance' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            $this->failures[] = "Baris {$this->currentRow} ({$email}): {$errors}";
            return null;
        }

        if (!$instance) {
            $this->failures[] = "Baris {$this->currentRow} ({$email}): Instansi '{$instanceName}' tidak ditemukan.";
            return null;
        }

        $this->successCount++;

        return new User([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'user',
            'instance_id' => $instance->id,
        ]);
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
