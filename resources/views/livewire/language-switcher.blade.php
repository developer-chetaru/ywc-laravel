<div class="p-2">
    <div class="flex items-center space-x-2">
        <img src="{{ asset('images/language-iocn.png') }}" alt="Language" class="w-5 h-5">
        
        <select wire:model="locale" wire:change="changeLanguage($event.target.value)"
                class="border rounded px-5 py-1 bg-white text-black">
            <option value="en">English</option>
            <option value="sp">Española</option>
            <option value="fr">Français</option>
        </select>
    </div>
</div>

