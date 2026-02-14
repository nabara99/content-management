<?php

namespace App\Livewire\Admin;

use App\Imports\InstancesImport;
use App\Models\Instance;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
#[Title('Manage Instansi')]
class InstanceManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    // Form fields
    public string $name = '';

    // Modal state
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showImportModal = false;
    public bool $showDeleteConfirm = false;

    // Editing
    public ?int $editingInstanceId = null;
    public ?int $deletingInstanceId = null;

    // Import
    public $excelFile = null;
    public array $importErrors = [];
    public ?string $importSuccess = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $instanceId): void
    {
        $this->resetForm();
        $instance = Instance::findOrFail($instanceId);
        $this->editingInstanceId = $instance->id;
        $this->name = $instance->name;
        $this->showEditModal = true;
    }

    public function openImportModal(): void
    {
        $this->excelFile = null;
        $this->importErrors = [];
        $this->importSuccess = null;
        $this->showImportModal = true;
    }

    public function confirmDelete(int $instanceId): void
    {
        $this->deletingInstanceId = $instanceId;
        $this->showDeleteConfirm = true;
    }

    public function create(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:instance,name'],
        ], [
            'name.unique' => 'Nama instansi sudah ada.',
        ]);

        Instance::create(['name' => $this->name]);

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('message', 'Instansi berhasil dibuat.');
    }

    public function update(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:instance,name,' . $this->editingInstanceId],
        ], [
            'name.unique' => 'Nama instansi sudah ada.',
        ]);

        Instance::findOrFail($this->editingInstanceId)->update(['name' => $this->name]);

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('message', 'Instansi berhasil diperbarui.');
    }

    public function delete(): void
    {
        $instance = Instance::withCount('users')->findOrFail($this->deletingInstanceId);

        if ($instance->users_count > 0) {
            session()->flash('error', "Tidak dapat menghapus instansi \"{$instance->name}\" karena masih memiliki {$instance->users_count} user.");
            $this->showDeleteConfirm = false;
            $this->deletingInstanceId = null;
            return;
        }

        $instance->delete();
        $this->showDeleteConfirm = false;
        $this->deletingInstanceId = null;
        session()->flash('message', 'Instansi berhasil dihapus.');
    }

    public function importExcel(): void
    {
        $this->validate([
            'excelFile' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $this->importErrors = [];
        $this->importSuccess = null;

        try {
            $import = new InstancesImport();
            Excel::import($import, $this->excelFile->getRealPath());

            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();

            if (count($failures) > 0) {
                $this->importErrors = $failures;
            }

            if ($successCount > 0) {
                $this->importSuccess = "{$successCount} instansi berhasil diimport.";
            }

            if (count($failures) === 0) {
                $this->showImportModal = false;
                session()->flash('message', "{$successCount} instansi berhasil diimport.");
            }
        } catch (\Exception $e) {
            $this->importErrors = ['File tidak valid atau format salah: ' . $e->getMessage()];
        }

        $this->excelFile = null;
    }

    public function downloadTemplate()
    {
        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name']);
            fputcsv($file, ['Dinas Pendidikan']);
            fputcsv($file, ['Dinas Kesehatan']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="instances-template.csv"',
        ]);
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->editingInstanceId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $instances = Instance::withCount('users')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.instance-manager', [
            'instances' => $instances,
            'header' => 'Instansi',
        ]);
    }
}
