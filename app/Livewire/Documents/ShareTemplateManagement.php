<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\ShareTemplate;
use Illuminate\Support\Facades\Auth;

class ShareTemplateManagement extends Component
{
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingTemplate = null;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $expiryDurationDays = 30;
    public $defaultMessage = '';
    public $isDefault = false;

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->resetForm();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($templateId)
    {
        $template = ShareTemplate::where('user_id', Auth::id())->findOrFail($templateId);
        $this->editingTemplate = $template;
        $this->name = $template->name;
        $this->description = $template->description ?? '';
        $this->expiryDurationDays = $template->expiry_duration_days;
        $this->defaultMessage = $template->default_message ?? '';
        $this->isDefault = $template->is_default;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTemplate = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->expiryDurationDays = 30;
        $this->defaultMessage = '';
        $this->isDefault = false;
    }

    public function createTemplate()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'expiryDurationDays' => 'required|integer|min:1|max:3650',
            'defaultMessage' => 'nullable|string|max:1000',
            'isDefault' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($this->isDefault) {
            ShareTemplate::forUser(Auth::id())
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        ShareTemplate::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description,
            'expiry_duration_days' => $this->expiryDurationDays,
            'default_message' => $this->defaultMessage,
            'is_default' => $this->isDefault,
            'document_criteria' => [],
            'permissions' => [],
        ]);

        session()->flash('message', 'Template created successfully!');
        $this->closeCreateModal();
    }

    public function updateTemplate()
    {
        if (!$this->editingTemplate) {
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'expiryDurationDays' => 'required|integer|min:1|max:3650',
            'defaultMessage' => 'nullable|string|max:1000',
            'isDefault' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($this->isDefault) {
            ShareTemplate::forUser(Auth::id())
                ->where('id', '!=', $this->editingTemplate->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $this->editingTemplate->update([
            'name' => $this->name,
            'description' => $this->description,
            'expiry_duration_days' => $this->expiryDurationDays,
            'default_message' => $this->defaultMessage,
            'is_default' => $this->isDefault,
        ]);

        session()->flash('message', 'Template updated successfully!');
        $this->closeEditModal();
    }

    public function deleteTemplate($templateId)
    {
        $template = ShareTemplate::where('user_id', Auth::id())->findOrFail($templateId);
        $template->delete();
        session()->flash('message', 'Template deleted successfully!');
    }

    public function getTemplatesProperty()
    {
        return ShareTemplate::forUser(Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.documents.share-template-management', [
            'templates' => $this->templates,
        ])->layout('layouts.app-laravel');
    }
}
