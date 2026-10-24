<?php

return [
    /*
    | Dados do PIX estático (BR Code). Estes valores eram fixos no JS do popup;
    | agora ficam aqui para o popup e a página /apoie usarem a mesma fonte.
    */
    'pix' => [
        'key'  => env('DONATION_PIX_KEY', 'leialivresite@gmail.com'),
        'name' => 'Leia livre',
        'city' => 'Porto Alegre',
    ],

    'default_amount' => 15,

    // Valor usado como exemplo nas frases de custo ("R$ 50 pagam cerca de 7
    // dias"). Separado do valor padrão de propósito: com R$ 15 o exemplo dá
    // 2 dias e faz o custo parecer pequeno demais para valer a doação.
    'example_amount' => 50,
    'amounts'        => [5, 15, 30, 50, 100],
    'min_amount'     => 1,
    'max_amount'     => 1000,

    /*
    | Custos reais de manter o site, em reais. Itens com valor nulo (ou zero)
    | são ignorados: enquanto nenhum for preenchido, nada de custo aparece no
    | popup nem em /apoie, e as frases que dependem do custo ficam de fora do
    | teste. Use 'monthly' para custo mensal ou 'yearly' (dividido por 12).
    */
    'costs' => [
        ['label' => 'Servidor (VPS)',                     'monthly' => 80],
        ['label' => 'Recursos de IA',                     'monthly' => 50],
        ['label' => 'Energia, internet e outros custos',  'monthly' => 70],
    ],

    // Trecho entre parênteses depois do custo mensal, no popup. Manter
    // coerente com os itens de 'costs'.
    'cost_summary' => 'servidor, recursos de IA, energia e internet',

    /*
    | Barra de progresso do mês, atualizada À MÃO.
    |   raised     quanto já entrou neste mês, em reais.
    |   month      mês a que 'raised' se refere (AAAA-MM).
    |   updated_at dia da última atualização (AAAA-MM-DD), exibido na barra.
    | Se 'month' não for o mês atual, a barra volta a zero sozinha: um valor
    | velho nunca é mostrado como se fosse deste mês. A meta é o custo mensal.
    | Para atualizar: edite estes três campos e faça o deploy.
    */
    'goal' => [
        'raised'     => 0,
        'month'      => '2026-10',
        'updated_at' => '2026-10-24',
    ],

    /*
    | Frases do popup pós-download, sorteadas por renderização (teste A/B).
    | O id e o texto sorteados vão para o Mixpanel em todos os eventos de
    | doação. Placeholders: {monthly} custo mensal, {summary} itens de custo,
    | {amount}/{days} e {example}/{example_days}: quanto o valor padrão e o
    | valor de exemplo cobrem. 'needs_cost' => só entra se houver custo
    | preenchido, e o popup omite a linha de custo abaixo da frase.
    | Não reutilize ids de frases removidas (d1, d2, d3, d4, d5, d7, d8): os dados antigos
    | no Mixpanel pertencem a outros textos. Frases novas entram como novos
    | itens deste array, com id novo.
    */
    'variants' => [
        ['id' => 'd6', 'text' => 'Uma doação ajuda a pagar o servidor e a manter o site rápido e no ar.'],
    ],
];
