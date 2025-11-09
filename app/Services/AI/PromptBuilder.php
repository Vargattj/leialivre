<?php

namespace App\Services\AI;

class PromptBuilder
{
    /**
     * Constrói prompt para enriquecimento de sinopse de livro
     */
    public function buildSynopsisPrompt(array $bookData): string
    {
        $title = $bookData['title'] ?? 'Livro';
        $authors = $this->formatAuthors($bookData['authors'] ?? []);
        $year = $bookData['first_publish_year'] ?? null;
        $yearText = $year ? " ({$year})" : '';

        $sources = $this->collectDescriptionSources($bookData);

        $prompt = "Você é um especialista em literatura. Crie uma sinopse curta, atrativa e envolvente em português brasileiro para o seguinte livro:\n\n";
        $prompt .= "Título: {$title}\n";
        $prompt .= "Autor(es): {$authors}{$yearText}\n\n";

        if (!empty($sources)) {
            $prompt .= "Informações coletadas de diferentes fontes:\n\n";
            foreach ($sources as $source => $description) {
                $prompt .= "Fonte: {$source}\n";
                $prompt .= "{$description}\n\n";
            }
        }

        $prompt .= "Instruções:\n";
        $prompt .= "- Crie uma sinopse curta e cativante (máximo 500 caracteres)\n";
        $prompt .= "- Use linguagem clara e envolvente\n";
        $prompt .= "- Destaque os aspectos mais interessantes da obra\n";
        $prompt .= "- Mantenha tom profissional mas acessível\n";
        $prompt .= "- Responda APENAS com a sinopse, sem explicações adicionais\n";

        return $prompt;
    }

    /**
     * Constrói prompt para enriquecimento de descrição completa de livro
     */
    public function buildDescriptionPrompt(array $bookData): string
    {
        $title = $bookData['title'] ?? 'Livro';
        $authors = $this->formatAuthors($bookData['authors'] ?? []);
        $year = $bookData['first_publish_year'] ?? null;
        $yearText = $year ? " ({$year})" : '';

        $sources = $this->collectDescriptionSources($bookData);
        $categories = $bookData['final_categories'] ?? [];
        $tags = $bookData['final_tags'] ?? [];

        $prompt = "Você é um especialista em literatura. Crie uma descrição completa e detalhada em português brasileiro para o seguinte livro:\n\n";
        $prompt .= "Título: {$title}\n";
        $prompt .= "Autor(es): {$authors}{$yearText}\n";

        if (!empty($categories)) {
            $prompt .= "Categorias: " . implode(', ', $categories) . "\n";
        }

        if (!empty($tags)) {
            $prompt .= "Tags: " . implode(', ', $tags) . "\n";
        }

        $prompt .= "\n";

        if (!empty($sources)) {
            $prompt .= "Informações coletadas de diferentes fontes:\n\n";
            foreach ($sources as $source => $description) {
                $prompt .= "Fonte: {$source}\n";
                $prompt .= "{$description}\n\n";
            }
        }

        $prompt .= "Instruções:\n";
        $prompt .= "- Consolide todas as informações disponíveis em uma descrição completa e coerente\n";
        $prompt .= "- Use linguagem clara e informativa\n";
        $prompt .= "- Destaque aspectos importantes da obra, como temas, estilo, contexto histórico\n";
        $prompt .= "- Mantenha tom profissional e acadêmico\n";
        $prompt .= "- Responda APENAS com a descrição, sem explicações adicionais\n";

        return $prompt;
    }

    /**
     * Constrói prompt para enriquecimento de biografia de autor
     */
    public function buildBiographyPrompt(array $authorData): string
    {
        $name = $authorData['name'] ?? 'Autor';
        $fullName = $authorData['full_name'] ?? $name;
        $birthDate = $authorData['birth_date'] ?? null;
        $deathDate = $authorData['death_date'] ?? null;
        $nationality = $authorData['nationality'] ?? null;

        $sources = $this->collectBiographySources($authorData);

        $prompt = "Você é um especialista em biografias literárias. Crie uma biografia completa e detalhada em português brasileiro para o seguinte autor:\n\n";
        $prompt .= "Nome: {$fullName}\n";

        if ($birthDate) {
            $prompt .= "Data de nascimento: {$birthDate}\n";
        }

        if ($deathDate) {
            $prompt .= "Data de falecimento: {$deathDate}\n";
        }

        if ($nationality) {
            $prompt .= "Nacionalidade: {$nationality}\n";
        }

        $prompt .= "\n";

        if (!empty($sources)) {
            $prompt .= "Informações coletadas de diferentes fontes:\n\n";
            foreach ($sources as $source => $biography) {
                $prompt .= "Fonte: {$source}\n";
                $prompt .= "{$biography}\n\n";
            }
        }

        $prompt .= "Instruções:\n";
        $prompt .= "- Consolide todas as informações disponíveis em uma biografia completa e coerente\n";
        $prompt .= "- Use linguagem clara e biográfica\n";
        $prompt .= "- Destaque aspectos importantes da vida e obra do autor\n";
        $prompt .= "- Mantenha tom profissional e informativo\n";
        $prompt .= "- Responda APENAS com a biografia, sem explicações adicionais\n";

        return $prompt;
    }

    /**
     * Coleta descrições de diferentes fontes para livros
     */
    private function collectDescriptionSources(array $bookData): array
    {
        $sources = [];

        if (!empty($bookData['google_books_description'])) {
            $sources['Google Books'] = $bookData['google_books_description'];
        }

        if (!empty($bookData['openlibrary_description'])) {
            $sources['OpenLibrary'] = $bookData['openlibrary_description'];
        }

        if (!empty($bookData['gutendex_description'])) {
            $sources['Gutendex/Gutenberg'] = $bookData['gutendex_description'];
        }

        if (!empty($bookData['wikipedia_description'])) {
            $sources['Wikipedia'] = $bookData['wikipedia_description'];
        }

        return $sources;
    }

    /**
     * Coleta biografias de diferentes fontes para autores
     */
    private function collectBiographySources(array $authorData): array
    {
        $sources = [];

        if (!empty($authorData['openlibrary_description'])) {
            $sources['OpenLibrary'] = $authorData['openlibrary_description'];
        }

        if (!empty($authorData['wikipedia_biography'])) {
            $sources['Wikipedia'] = $authorData['wikipedia_biography'];
        }

        return $sources;
    }

    /**
     * Formata lista de autores
     */
    private function formatAuthors(array $authors): string
    {
        if (empty($authors)) {
            return 'Autor desconhecido';
        }

        $names = [];
        foreach ($authors as $author) {
            if (is_array($author)) {
                $names[] = $author['name'] ?? 'Desconhecido';
            } else {
                $names[] = $author;
            }
        }

        return implode(', ', $names);
    }
}

