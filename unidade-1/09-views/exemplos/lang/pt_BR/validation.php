<?php

// Mensagens das regras utilizadas nos Controllers deste exemplo.
return [
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'boolean' => 'O campo :attribute deve indicar sim ou não.',
    'exists' => 'O valor selecionado para :attribute não existe.',
    'unique' => 'Já existe um registro com esse valor de :attribute.',
    'between' => [
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
    ],
    'max' => [
        'numeric' => 'O campo :attribute deve ser no máximo :max.',
        'string' => 'O campo :attribute deve ter no máximo :max caracteres.',
    ],
    'size' => [
        'string' => 'O campo :attribute deve ter :size caracteres.',
    ],
    'attributes' => [
        'name' => 'nome',
        'title' => 'título',
        'isbn' => 'ISBN',
        'published_year' => 'ano de publicação',
        'author_id' => 'autor',
        'pages' => 'páginas',
        'summary' => 'resumo',
        'category_id' => 'categoria',
        'featured' => 'destaque',
        'position' => 'posição',
    ],
];
