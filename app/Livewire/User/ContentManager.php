<?php

namespace App\Livewire\User;

use App\Models\Content;
use App\Models\ContentImage;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.user')]
#[Title('Konten Saya')]
class ContentManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    // Form fields
    public string $title = '';
    public string $caption = '';
    public ?int $template_id = null;
    public array $slotFiles = [];
    public array $slotTransforms = [];

    // Font settings - Title
    public string $titleFontFamily = 'Arial';
    public int $titleFontSize = 24;
    public bool $titleFontBold = false;
    public bool $titleFontItalic = false;
    public bool $titleFontUnderline = false;
    public string $titleFontColor = '#000000';

    // Font settings - Caption
    public string $captionFontFamily = 'Arial';
    public int $captionFontSize = 16;
    public bool $captionFontBold = false;
    public bool $captionFontItalic = false;
    public bool $captionFontUnderline = false;
    public string $captionFontColor = '#000000';

    // Template text positions
    public float $titleXPercent = 50;
    public float $titleYPercent = 85;
    public float $captionXPercent = 50;
    public float $captionYPercent = 92;

    // Modal state
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteConfirm = false;
    public bool $showDetailModal = false;

    // Editing
    public ?int $editingContentId = null;
    public ?int $deletingContentId = null;
    public ?Content $viewingContent = null;

    // Template info
    public array $templateSlots = [];
    public array $existingImages = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function updatedTemplateId(): void
    {
        if ($this->template_id) {
            $template = Template::with('slots')->find($this->template_id);
            if ($template) {
                $this->templateSlots = $template->slots->map(fn($s) => [
                    'slot_number' => $s->slot_number,
                    'x_percent' => $s->x_percent,
                    'y_percent' => $s->y_percent,
                    'width_percent' => $s->width_percent,
                    'height_percent' => $s->height_percent,
                ])->toArray();

                $this->slotTransforms = [];
                foreach ($template->slots as $slot) {
                    $this->slotTransforms[$slot->slot_number] = [
                        'offset_x' => 0,
                        'offset_y' => 0,
                        'scale' => 1,
                    ];
                }

                $this->titleXPercent = (float) $template->title_x_percent;
                $this->titleYPercent = (float) $template->title_y_percent;
                $this->captionXPercent = (float) $template->caption_x_percent;
                $this->captionYPercent = (float) $template->caption_y_percent;
            } else {
                $this->templateSlots = [];
                $this->slotTransforms = [];
            }
        } else {
            $this->templateSlots = [];
            $this->slotTransforms = [];
        }
        $this->slotFiles = [];
    }

    public function openEditModal(int $contentId): void
    {
        $this->resetForm();
        $content = Content::with(['images', 'template.slots'])->where('user_id', Auth::id())->findOrFail($contentId);
        $this->editingContentId = $content->id;
        $this->title = $content->title;
        $this->caption = $content->caption ?? '';
        $this->template_id = $content->template_id;

        $this->templateSlots = $content->template->slots->map(fn($s) => [
            'slot_number' => $s->slot_number,
            'x_percent' => $s->x_percent,
            'y_percent' => $s->y_percent,
            'width_percent' => $s->width_percent,
            'height_percent' => $s->height_percent,
        ])->toArray();

        $this->existingImages = $content->images->mapWithKeys(fn($img) => [
            $img->slot_number => $img->image,
        ])->toArray();

        // Load font settings
        $this->titleFontFamily = $content->title_font_family ?? 'Arial';
        $this->titleFontSize = $content->title_font_size ?? 24;
        $this->titleFontBold = (bool) $content->title_font_bold;
        $this->titleFontItalic = (bool) $content->title_font_italic;
        $this->titleFontUnderline = (bool) $content->title_font_underline;
        $this->titleFontColor = $content->title_font_color ?? '#000000';
        $this->captionFontFamily = $content->caption_font_family ?? 'Arial';
        $this->captionFontSize = $content->caption_font_size ?? 16;
        $this->captionFontBold = (bool) $content->caption_font_bold;
        $this->captionFontItalic = (bool) $content->caption_font_italic;
        $this->captionFontUnderline = (bool) $content->caption_font_underline;
        $this->captionFontColor = $content->caption_font_color ?? '#000000';

        // Load text positions from content
        $this->titleXPercent = (float) $content->title_x_percent;
        $this->titleYPercent = (float) $content->title_y_percent;
        $this->captionXPercent = (float) $content->caption_x_percent;
        $this->captionYPercent = (float) $content->caption_y_percent;

        $this->slotTransforms = [];
        foreach ($content->template->slots as $slot) {
            $existingImg = $content->images->where('slot_number', $slot->slot_number)->first();
            $this->slotTransforms[$slot->slot_number] = [
                'offset_x' => $existingImg ? $existingImg->offset_x : 0,
                'offset_y' => $existingImg ? $existingImg->offset_y : 0,
                'scale' => $existingImg ? $existingImg->scale : 1,
            ];
        }

        $this->showEditModal = true;
    }

    public function showDetail(int $contentId): void
    {
        $this->viewingContent = Content::with(['images', 'template.slots', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($contentId);
        $this->showDetailModal = true;
    }

    public function confirmDelete(int $contentId): void
    {
        $this->deletingContentId = $contentId;
        $this->showDeleteConfirm = true;
    }

    public function create(): void
    {
        $template = Template::with('slots')->findOrFail($this->template_id);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:300'],
            'template_id' => ['required', 'exists:templates,id'],
        ];

        $messages = [];
        foreach ($template->slots as $slot) {
            $sn = $slot->slot_number;
            $rules["slotFiles.{$sn}"] = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'];
            $messages["slotFiles.{$sn}.required"] = "Foto slot {$sn} harus diupload.";
            $messages["slotFiles.{$sn}.image"] = "Foto slot {$sn} harus berupa gambar.";
            $messages["slotFiles.{$sn}.max"] = "Foto slot {$sn} maksimal 5MB.";
        }

        $this->validate($rules, $messages);

        $content = Content::create([
            'user_id' => Auth::id(),
            'template_id' => $this->template_id,
            'title' => $this->title,
            'caption' => $this->caption ?: null,
            'status' => 'draft',
            'title_font_family' => $this->titleFontFamily,
            'title_font_size' => $this->titleFontSize,
            'title_font_bold' => $this->titleFontBold,
            'title_font_italic' => $this->titleFontItalic,
            'title_font_underline' => $this->titleFontUnderline,
            'title_font_color' => $this->titleFontColor,
            'caption_font_family' => $this->captionFontFamily,
            'caption_font_size' => $this->captionFontSize,
            'caption_font_bold' => $this->captionFontBold,
            'caption_font_italic' => $this->captionFontItalic,
            'caption_font_underline' => $this->captionFontUnderline,
            'caption_font_color' => $this->captionFontColor,
            'title_x_percent' => $this->titleXPercent,
            'title_y_percent' => $this->titleYPercent,
            'caption_x_percent' => $this->captionXPercent,
            'caption_y_percent' => $this->captionYPercent,
        ]);

        foreach ($this->slotFiles as $slotNumber => $file) {
            $path = $file->store('contents/' . $content->id, 'public');
            $transform = $this->slotTransforms[$slotNumber] ?? ['offset_x' => 0, 'offset_y' => 0, 'scale' => 1];
            ContentImage::create([
                'content_id' => $content->id,
                'image' => $path,
                'slot_number' => $slotNumber,
                'offset_x' => $transform['offset_x'],
                'offset_y' => $transform['offset_y'],
                'scale' => $transform['scale'],
            ]);
        }

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('message', 'Konten berhasil dibuat.');
    }

    public function update(): void
    {
        $content = Content::with('images')->where('user_id', Auth::id())->findOrFail($this->editingContentId);
        $template = Template::with('slots')->findOrFail($this->template_id);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:300'],
        ];

        $messages = [];
        foreach ($template->slots as $slot) {
            $sn = $slot->slot_number;
            if (isset($this->slotFiles[$sn])) {
                $rules["slotFiles.{$sn}"] = ['image', 'mimes:jpg,jpeg,png', 'max:5120'];
                $messages["slotFiles.{$sn}.image"] = "Foto slot {$sn} harus berupa gambar.";
                $messages["slotFiles.{$sn}.max"] = "Foto slot {$sn} maksimal 5MB.";
            }
        }

        $this->validate($rules, $messages);

        $content->update([
            'title' => $this->title,
            'caption' => $this->caption ?: null,
            'title_font_family' => $this->titleFontFamily,
            'title_font_size' => $this->titleFontSize,
            'title_font_bold' => $this->titleFontBold,
            'title_font_italic' => $this->titleFontItalic,
            'title_font_underline' => $this->titleFontUnderline,
            'title_font_color' => $this->titleFontColor,
            'caption_font_family' => $this->captionFontFamily,
            'caption_font_size' => $this->captionFontSize,
            'caption_font_bold' => $this->captionFontBold,
            'caption_font_italic' => $this->captionFontItalic,
            'caption_font_underline' => $this->captionFontUnderline,
            'caption_font_color' => $this->captionFontColor,
            'title_x_percent' => $this->titleXPercent,
            'title_y_percent' => $this->titleYPercent,
            'caption_x_percent' => $this->captionXPercent,
            'caption_y_percent' => $this->captionYPercent,
        ]);

        // Update transforms and images
        foreach ($template->slots as $slot) {
            $sn = $slot->slot_number;
            $existing = $content->images->where('slot_number', $sn)->first();
            $transform = $this->slotTransforms[$sn] ?? ['offset_x' => 0, 'offset_y' => 0, 'scale' => 1];

            if (isset($this->slotFiles[$sn])) {
                if ($existing) {
                    Storage::disk('public')->delete($existing->image);
                    $existing->update([
                        'image' => $this->slotFiles[$sn]->store('contents/' . $content->id, 'public'),
                        'offset_x' => $transform['offset_x'],
                        'offset_y' => $transform['offset_y'],
                        'scale' => $transform['scale'],
                    ]);
                } else {
                    ContentImage::create([
                        'content_id' => $content->id,
                        'image' => $this->slotFiles[$sn]->store('contents/' . $content->id, 'public'),
                        'slot_number' => $sn,
                        'offset_x' => $transform['offset_x'],
                        'offset_y' => $transform['offset_y'],
                        'scale' => $transform['scale'],
                    ]);
                }
            } elseif ($existing) {
                // Update only transforms
                $existing->update([
                    'offset_x' => $transform['offset_x'],
                    'offset_y' => $transform['offset_y'],
                    'scale' => $transform['scale'],
                ]);
            }
        }

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('message', 'Konten berhasil diperbarui.');
    }

    public function delete(): void
    {
        $content = Content::with('images')->where('user_id', Auth::id())->findOrFail($this->deletingContentId);

        foreach ($content->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        Storage::disk('public')->deleteDirectory('contents/' . $content->id);
        $content->delete();

        $this->showDeleteConfirm = false;
        $this->deletingContentId = null;
        session()->flash('message', 'Konten berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->caption = '';
        $this->template_id = null;
        $this->slotFiles = [];
        $this->slotTransforms = [];
        $this->templateSlots = [];
        $this->existingImages = [];
        $this->editingContentId = null;
        $this->viewingContent = null;
        $this->titleFontFamily = 'Arial';
        $this->titleFontSize = 24;
        $this->titleFontBold = false;
        $this->titleFontItalic = false;
        $this->titleFontUnderline = false;
        $this->titleFontColor = '#000000';
        $this->captionFontFamily = 'Arial';
        $this->captionFontSize = 16;
        $this->captionFontBold = false;
        $this->captionFontItalic = false;
        $this->captionFontUnderline = false;
        $this->captionFontColor = '#000000';
        $this->titleXPercent = 50;
        $this->titleYPercent = 85;
        $this->captionXPercent = 50;
        $this->captionYPercent = 92;
        $this->resetValidation();
    }

    public function render()
    {
        $contents = Content::with(['template.slots', 'images'])
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $templates = Template::with('slots')->where('status', 'active')->orderBy('name')->get();

        return view('livewire.user.content-manager', [
            'contents' => $contents,
            'templates' => $templates,
            'header' => 'Konten Saya',
        ]);
    }
}
