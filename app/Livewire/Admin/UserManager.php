<?php

namespace App\Livewire\Admin;

use App\Imports\UsersImport;
use App\Models\Instance;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
#[Title('Manage Users')]
class UserManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $instance_id = null;

    // Modal state
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showImportModal = false;
    public bool $showDeleteConfirm = false;

    // Editing
    public ?int $editingUserId = null;
    public ?int $deletingUserId = null;

    // Import
    public $excelFile = null;
    public array $importErrors = [];
    public ?string $importSuccess = null;

    protected function passwordRules(bool $required = true): array
    {
        $rules = [
            'string',
            'min:8',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*?&#]/',
            'confirmed',
        ];

        if ($required) {
            array_unshift($rules, 'required');
        }

        return $rules;
    }

    protected function passwordMessages(): array
    {
        return [
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial (@$!%*?&#).',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $this->resetForm();
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->instance_id = $user->instance_id;
        $this->showEditModal = true;
    }

    public function openImportModal(): void
    {
        $this->excelFile = null;
        $this->importErrors = [];
        $this->importSuccess = null;
        $this->showImportModal = true;
    }

    public function confirmDelete(int $userId): void
    {
        $this->deletingUserId = $userId;
        $this->showDeleteConfirm = true;
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);
    }

    public function create(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'instance_id' => ['required', 'exists:instance,id'],
        ], array_merge($this->passwordMessages(), [
            'instance_id.required' => 'Instansi wajib dipilih.',
        ]));

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => 'user',
            'instance_id' => $this->instance_id,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('message', 'User berhasil dibuat.');
    }

    public function update(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->editingUserId],
            'instance_id' => ['required', 'exists:instance,id'],
        ];

        if (!empty($this->password)) {
            $rules['password'] = $this->passwordRules();
        }

        $this->validate($rules, array_merge($this->passwordMessages(), [
            'instance_id.required' => 'Instansi wajib dipilih.',
        ]));

        $user = User::findOrFail($this->editingUserId);
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'instance_id' => $this->instance_id,
        ];

        if (!empty($this->password)) {
            $data['password'] = $this->password;
        }

        $user->update($data);

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('message', 'User berhasil diperbarui.');
    }

    public function delete(): void
    {
        User::findOrFail($this->deletingUserId)->delete();
        $this->showDeleteConfirm = false;
        $this->deletingUserId = null;
        session()->flash('message', 'User berhasil dihapus.');
    }

    public function importExcel(): void
    {
        $this->validate([
            'excelFile' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $this->importErrors = [];
        $this->importSuccess = null;

        try {
            $import = new UsersImport();
            Excel::import($import, $this->excelFile->getRealPath());

            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();

            if (count($failures) > 0) {
                $this->importErrors = $failures;
            }

            if ($successCount > 0) {
                $this->importSuccess = "{$successCount} user berhasil diimport.";
            }

            if (count($failures) === 0) {
                $this->showImportModal = false;
                session()->flash('message', "{$successCount} user berhasil diimport.");
            }
        } catch (\Exception $e) {
            $this->importErrors = ['File tidak valid atau format salah: ' . $e->getMessage()];
        }

        $this->excelFile = null;
    }

    public function downloadTemplate()
    {
        $instances = Instance::pluck('name')->implode(', ');
        $hint = $instances ?: 'Belum ada instansi';

        $callback = function () use ($hint) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password', 'instance']);
            fputcsv($file, ['John Doe', 'john@example.com', 'Pass@123', 'Contoh Instansi']);
            fputcsv($file, ['# Instansi tersedia: ' . $hint, '', '', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-template.csv"',
        ]);
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->instance_id = null;
        $this->editingUserId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::with('instance')
            ->where('role', 'user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        $instances = Instance::orderBy('name')->get();

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'instances' => $instances,
            'header' => 'Users',
        ]);
    }
}
