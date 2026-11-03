<div class="grid gap-1">
    <label for="name" class="font-medium">Nome</label>
    <input id="name" name="name" value="{{ old('name', $category->name ?? '') }}" maxlength="255" required
        class="w-full rounded border border-slate-300 p-2">
    @error('name')
        <p class="text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
