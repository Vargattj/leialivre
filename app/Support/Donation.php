<?php

namespace App\Support;

/**
 * Regras de apresentação da doação (custos, frases sorteadas), concentradas
 * aqui para o popup pós-download e a página /apoie não divergirem.
 */
class Donation
{
    /** Custos preenchidos em config/donation.php, normalizados para valor mensal. */
    public static function costs(): array
    {
        return collect(config('donation.costs', []))
            ->map(function (array $item) {
                $monthly = $item['monthly'] ?? (isset($item['yearly']) ? $item['yearly'] / 12 : null);

                if ($monthly === null || (float) $monthly <= 0) {
                    return null;
                }

                return ['label' => $item['label'], 'monthly' => round((float) $monthly, 2)];
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function monthlyCost(): ?float
    {
        $items = static::costs();

        return $items ? round(array_sum(array_column($items, 'monthly')), 2) : null;
    }

    /** Quantos dias de operação um valor cobre; null se não há custo preenchido. */
    public static function daysCovered(float $amount): ?int
    {
        $monthly = static::monthlyCost();

        return $monthly ? max(1, (int) floor($amount / ($monthly / 30))) : null;
    }

    /**
     * Progresso do mês em direção à meta (= custo mensal), ou null sem custo
     * preenchido. 'raised' é manual: fora do mês registrado ele conta como zero.
     */
    public static function progress(): ?array
    {
        $goal = static::monthlyCost();

        if ($goal === null) {
            return null;
        }

        $config  = config('donation.goal', []);
        $current = ($config['month'] ?? null) === now()->format('Y-m');
        $raised  = $current ? max(0.0, (float) ($config['raised'] ?? 0)) : 0.0;

        $updatedAt = null;
        if ($current && ! empty($config['updated_at'])) {
            try {
                $updatedAt = \Illuminate\Support\Carbon::parse($config['updated_at'])->format('d/m');
            } catch (\Throwable $e) {
                $updatedAt = null;
            }
        }

        return [
            'goal'       => $goal,
            'raised'     => $raised,
            'percent'    => (int) min(100, floor($raised / $goal * 100)),
            'reached'    => $raised >= $goal,
            'updated_at' => $updatedAt,
        ];
    }

    public static function money(float $value): string
    {
        $formatted = number_format($value, 2, ',', '.');

        return 'R$ ' . (str_ends_with($formatted, ',00') ? substr($formatted, 0, -3) : $formatted);
    }

    /** Sorteia uma frase com os placeholders já resolvidos: ['id' => ..., 'text' => ...]. */
    public static function pickVariant(): array
    {
        $hasCost = static::monthlyCost() !== null;
        $amount  = (float) config('donation.default_amount');
        $example = (float) config('donation.example_amount');

        $pool = collect(config('donation.variants', []))
            ->filter(fn (array $variant) => $hasCost || empty($variant['needs_cost']))
            ->values()
            ->all();

        $variant = $pool[array_rand($pool)];

        $variant['text'] = strtr($variant['text'], [
            '{monthly}' => $hasCost ? static::money(static::monthlyCost()) : '',
            '{summary}' => (string) config('donation.cost_summary'),
            '{amount}'  => static::money($amount),
            '{example}' => static::money($example),
            '{example_days}' => (string) (static::daysCovered($example) ?? ''),
            '{days}'    => (string) (static::daysCovered($amount) ?? ''),
        ]);

        return [
            'id'         => $variant['id'],
            'text'       => $variant['text'],
            // A frase já fala de custo: o popup não repete a linha de custo.
            'needs_cost' => ! empty($variant['needs_cost']),
        ];
    }
}
