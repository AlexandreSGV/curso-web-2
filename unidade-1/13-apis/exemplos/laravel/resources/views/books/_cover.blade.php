<img src="{{ $book->cover === null
    ? asset('images/cover-default.svg')
    : asset('storage/' . $book->cover) }}"
    alt="{{ $book->cover === null ? 'Capa padrão' : 'Capa de ' . $book->title }}"
    class="{{ $size ?? 'w-32' }} aspect-[2/3] rounded bg-slate-100 object-contain">
