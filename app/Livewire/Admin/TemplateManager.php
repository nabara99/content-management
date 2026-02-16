<?php

namespace App\Livewire\Admin;

use App\Models\Template;
use App\Models\TemplateSlot;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Manage Templates')]
class TemplateManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    // Form fields
    public string $name = '';
    public $imageFile = null;
    public array $slotConfigs = [];

    // Text positions
    public float $title_x_percent = 50;
    public float $title_y_percent = 85;
    public float $caption_x_percent = 50;
    public float $caption_y_percent = 92;

    // Modal state
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteConfirm = false;

    // Editing
    public ?int $editingTemplateId = null;
    public ?int $deletingTemplateId = null;
    public ?string $existingImage = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function addSlot(): void
    {
        if (count($this->slotConfigs) >= 3) return;

        $this->slotConfigs[] = [
            'x_percent' => 10,
            'y_percent' => 10,
            'width_percent' => 30,
            'height_percent' => 40,
        ];
    }

    public function removeSlot(int $index): void
    {
        unset($this->slotConfigs[$index]);
        $this->slotConfigs = array_values($this->slotConfigs);
    }

    public function openEditModal(int $templateId): void
    {
        $this->resetForm();
        $template = Template::with('slots')->findOrFail($templateId);
        $this->editingTemplateId = $template->id;
        $this->name = $template->name;
        $this->existingImage = $template->image;
        $this->title_x_percent = (float) $template->title_x_percent;
        $this->title_y_percent = (float) $template->title_y_percent;
        $this->caption_x_percent = (float) $template->caption_x_percent;
        $this->caption_y_percent = (float) $template->caption_y_percent;
        $this->slotConfigs = $template->slots->map(fn($s) => [
            'x_percent' => $s->x_percent,
            'y_percent' => $s->y_percent,
            'width_percent' => $s->width_percent,
            'height_percent' => $s->height_percent,
        ])->toArray();
        $this->showEditModal = true;
    }

    public function confirmDelete(int $templateId): void
    {
        $this->deletingTemplateId = $templateId;
        $this->showDeleteConfirm = true;
    }

    public function create(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'imageFile' => ['required', 'image', 'mimes:png', 'max:5120'],
            'slotConfigs' => ['required', 'array', 'min:1', 'max:3'],
            'slotConfigs.*.x_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'slotConfigs.*.y_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'slotConfigs.*.width_percent' => ['required', 'numeric', 'min:5', 'max:100'],
            'slotConfigs.*.height_percent' => ['required', 'numeric', 'min:5', 'max:100'],
            'title_x_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'title_y_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'caption_x_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'caption_y_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'imageFile.required' => 'File gambar harus diupload.',
            'imageFile.mimes' => 'File harus berformat PNG.',
            'imageFile.max' => 'Ukuran file maksimal 5MB.',
            'slotConfigs.required' => 'Tambahkan minimal 1 slot foto.',
            'slotConfigs.min' => 'Tambahkan minimal 1 slot foto.',
        ]);

        $path = $this->imageFile->store('templates', 'public');

        $template = Template::create([
            'name' => $this->name,
            'image' => $path,
            'title_x_percent' => $this->title_x_percent,
            'title_y_percent' => $this->title_y_percent,
            'caption_x_percent' => $this->caption_x_percent,
            'caption_y_percent' => $this->caption_y_percent,
        ]);

        foreach ($this->slotConfigs as $index => $slot) {
            TemplateSlot::create([
                'template_id' => $template->id,
                'slot_number' => $index + 1,
                'x_percent' => $slot['x_percent'],
                'y_percent' => $slot['y_percent'],
                'width_percent' => $slot['width_percent'],
                'height_percent' => $slot['height_percent'],
            ]);
        }

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('message', 'Template berhasil dibuat.');
    }

    public function update(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slotConfigs' => ['required', 'array', 'min:1', 'max:3'],
            'slotConfigs.*.x_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'slotConfigs.*.y_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'slotConfigs.*.width_percent' => ['required', 'numeric', 'min:5', 'max:100'],
            'slotConfigs.*.height_percent' => ['required', 'numeric', 'min:5', 'max:100'],
            'title_x_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'title_y_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'caption_x_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'caption_y_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ];

        if ($this->imageFile) {
            $rules['imageFile'] = ['image', 'mimes:png', 'max:5120'];
        }

        $this->validate($rules, [
            'imageFile.mimes' => 'File harus berformat PNG.',
            'imageFile.max' => 'Ukuran file maksimal 5MB.',
            'slotConfigs.required' => 'Tambahkan minimal 1 slot foto.',
            'slotConfigs.min' => 'Tambahkan minimal 1 slot foto.',
        ]);

        $template = Template::findOrFail($this->editingTemplateId);
        $data = [
            'name' => $this->name,
            'title_x_percent' => $this->title_x_percent,
            'title_y_percent' => $this->title_y_percent,
            'caption_x_percent' => $this->caption_x_percent,
            'caption_y_percent' => $this->caption_y_percent,
        ];

        if ($this->imageFile) {
            Storage::disk('public')->delete($template->image);
            $data['image'] = $this->imageFile->store('templates', 'public');
        }

        $template->update($data);

        // Sync slots
        $template->slots()->delete();
        foreach ($this->slotConfigs as $index => $slot) {
            TemplateSlot::create([
                'template_id' => $template->id,
                'slot_number' => $index + 1,
                'x_percent' => $slot['x_percent'],
                'y_percent' => $slot['y_percent'],
                'width_percent' => $slot['width_percent'],
                'height_percent' => $slot['height_percent'],
            ]);
        }

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('message', 'Template berhasil diperbarui.');
    }

    public function delete(): void
    {
        $template = Template::findOrFail($this->deletingTemplateId);
        Storage::disk('public')->delete($template->image);
        $template->delete();

        $this->showDeleteConfirm = false;
        $this->deletingTemplateId = null;
        session()->flash('message', 'Template berhasil dihapus.');
    }

    public function toggleStatus(int $templateId): void
    {
        $template = Template::findOrFail($templateId);
        $template->update([
            'status' => $template->status === 'active' ? 'inactive' : 'active',
        ]);
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->slotConfigs = [];
        $this->imageFile = null;
        $this->editingTemplateId = null;
        $this->existingImage = null;
        $this->title_x_percent = 50;
        $this->title_y_percent = 85;
        $this->caption_x_percent = 50;
        $this->caption_y_percent = 92;
        $this->resetValidation();
    }

    public function render()
    {
        $templates = Template::with('slots')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.template-manager', [
            'templates' => $templates,
            'header' => 'Templates',
        ]);
    }
}
