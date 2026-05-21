<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conquistas únicas
    |--------------------------------------------------------------------------
    */

    'primeiro_livro_favoritado' => [
        'titulo' => 'Primeiro Livro Favoritado',
        'descricao' => 'Favoritou seu primeiro livro na biblioteca.',
        'mensagem_desbloqueio' => 'Você acabou de favoritar seu primeiro livro. Sua coleção literária começou oficialmente!',
        'categoria' => 'progresso',
        'nivel' => 'bronze',
        'icone' => 'images/conquistas/progresso/primeiro_favorito.svg',
        'xp' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Conquistas com progressão
    |--------------------------------------------------------------------------
    */

    'colecionador_literario' => [
        'titulo' => 'Colecionador Literário',
        'categoria' => 'progresso',
        'possui_niveis' => true,
        'niveis' => [
            'bronze' => [
                'meta' => 10,
                'descricao' => 'Adicionou 10 livros à estante.',
                'mensagem_desbloqueio' => 'Sua estante já possui 10 livros. Sua biblioteca pessoal está começando a ganhar forma!',
                'icone' => 'images/conquistas/progresso/colecionador_literario/colecionador_bronze.svg',
                'xp' => 100,
            ],
            'prata' => [
                'meta' => 25,
                'descricao' => 'Adicionou 25 livros à estante.',
                'mensagem_desbloqueio' => 'Sua coleção está crescendo rapidamente. Você já possui 25 livros na estante!',
                'icone' => 'images/conquistas/progresso/colecionador_literario/colecionador_prata.svg',
                'xp' => 250,
            ],
            'ouro' => [
                'meta' => 50,
                'descricao' =>'Adicionou 50 livros à estante.',
                'mensagem_desbloqueio' => 'Uma verdadeira biblioteca pessoal! Você alcançou 50 livros na estante.',
                'icone' => 'images/conquistas/progresso/colecionador_literario/colecionador_ouro.svg',
                'xp' => 500,
            ],
        ]
    ],
];