<div class="grid gap-1">
    <label for="pages" class="font-medium">Quantidade de páginas (opcional)</label>
    <input id="pages" name="pages" type="number" min="1" max="4294967295"
        value="{{ old('pages', $detail->pages ?? '') }}" class="w-full rounded border border-slate-300 p-2">
</div>

<div class="grid gap-1">
    <label for="summary" class="font-medium">Resumo (opcional, até 5.000 caracteres)</label>
    <textarea id="summary" name="summary" rows="5" maxlength="5000"
        class="w-full rounded border border-slate-300 p-2">{{ old('summary', $detail->summary ?? '') }}</textarea>
</div>
