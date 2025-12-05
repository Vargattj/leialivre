# SEO Helper - Guia Rápido

## 🚀 Como Usar em Qualquer Página

### 1. Uso Básico (Mínimo)

```blade
@section('seo')
    <x-seo-meta
        title="Título da Sua Página"
        description="Descrição clara e objetiva da página (máx 160 caracteres)"
    />
@endsection
```

### 2. Uso Completo (Recomendado)

```blade
@section('seo')
    <x-seo-meta
        title="Título Completo da Página - Nome do Site"
        description="Descrição otimizada com palavras-chave e call-to-action"
        keywords="palavra1, palavra2, palavra3"
        :image="asset('images/og-image.jpg')"
        type="website"
        :jsonLd="[
            [
                'type' => 'WebSite',
                'data' => [
                    'name' => 'Nome do Site',
                    'url' => url('/'),
                ]
            ]
        ]"
    />
@endsection
```

## 📚 Exemplos por Tipo de Página

### Página de Livro

```blade
@section('seo')
    <x-seo-meta
        :title="$book->title . ' - ' . $book->authors_names . ' | Leia Livre'"
        :description="'Baixe gratuitamente ' . $book->title . ' de ' . $book->authors_names . '. ' . Str::limit($book->synopsis, 100)"
        :image="$book->cover_url"
        type="book"
        :jsonLd="[
            [
                'type' => 'Book',
                'data' => [
                    'name' => $book->title,
                    'author' => ['@type' => 'Person', 'name' => $book->authors_names],
                    'datePublished' => $book->publication_year,
                    'description' => $book->synopsis,
                    'image' => $book->cover_url,
                ]
            ]
        ]"
    />
@endsection
```

### Página de Autor

```blade
@section('seo')
    <x-seo-meta
        :title="$author->name . ' - Biografia e Obras | Leia Livre'"
        :description="'Conheça ' . $author->name . '. ' . Str::limit($author->biography, 120)"
        :image="$author->photo_url"
        type="profile"
        :jsonLd="[
            [
                'type' => 'Person',
                'data' => [
                    'name' => $author->name,
                    'description' => $author->biography,
                    'birthDate' => $author->birth_date?->format('Y-m-d'),
                    'nationality' => $author->nationality,
                    'image' => $author->photo_url,
                ]
            ]
        ]"
    />
@endsection
```

## 🎯 Tipos de Schema Disponíveis

### WebSite

```php
[
    'type' => 'WebSite',
    'data' => [
        'name' => 'Nome do Site',
        'description' => 'Descrição',
        'url' => url('/'),
        'search_url' => route('search') . '?q={search_term_string}',
    ]
]
```

### Organization

```php
[
    'type' => 'Organization',
    'data' => [
        'name' => 'Nome da Organização',
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'sameAs' => ['https://facebook.com/...', 'https://twitter.com/...'],
    ]
]
```

### Book

```php
[
    'type' => 'Book',
    'data' => [
        'name' => 'Título do Livro',
        'author' => ['@type' => 'Person', 'name' => 'Nome do Autor'],
        'datePublished' => '2020',
        'description' => 'Sinopse',
        'image' => 'url-da-capa.jpg',
        'isbn' => '978-...',
        'numberOfPages' => 300,
    ]
]
```

### Person

```php
[
    'type' => 'Person',
    'data' => [
        'name' => 'Nome da Pessoa',
        'description' => 'Biografia',
        'birthDate' => '1900-01-01',
        'deathDate' => '1980-12-31',
        'nationality' => 'Brasileiro',
        'image' => 'url-da-foto.jpg',
    ]
]
```

### BreadcrumbList

```php
[
    'type' => 'BreadcrumbList',
    'data' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Livros', 'url' => route('livros.index')],
        ['name' => 'Título', 'url' => route('livros.show', $slug)],
    ]
]
```

## ⚙️ Props Disponíveis

| Prop           | Tipo   | Padrão                         | Descrição                                 |
| -------------- | ------ | ------------------------------ | ----------------------------------------- |
| `title`        | string | config('app.name')             | Título da página                          |
| `description`  | string | ''                             | Meta description                          |
| `keywords`     | string | ''                             | Palavras-chave                            |
| `image`        | string | asset('images/og-default.jpg') | Imagem OG                                 |
| `url`          | string | url()->current()               | URL canônica                              |
| `type`         | string | 'website'                      | Tipo OG (website, article, book, profile) |
| `locale`       | string | 'pt_BR'                        | Localização                               |
| `author`       | string | null                           | Autor do conteúdo                         |
| `jsonLd`       | array  | []                             | Array de schemas                          |
| `ogAdditional` | array  | []                             | Tags OG extras                            |
| `twitterCard`  | string | 'summary_large_image'          | Tipo de Twitter Card                      |
| `index`        | bool   | true                           | Indexar página                            |
| `follow`       | bool   | true                           | Seguir links                              |

## 🔍 Validação

Após implementar, valide com:

1. **Google Rich Results Test**: https://search.google.com/test/rich-results
2. **Schema Validator**: https://validator.schema.org/
3. **Facebook Debugger**: https://developers.facebook.com/tools/debug/
4. **Twitter Validator**: https://cards-dev.twitter.com/validator

## 💡 Dicas

1. **Title**: Máximo 60 caracteres, inclua palavras-chave principais
2. **Description**: Entre 120-160 caracteres, inclua call-to-action
3. **Keywords**: 5-10 palavras-chave relevantes
4. **Image**: Mínimo 1200x630px para Open Graph
5. **Alt Text**: Sempre descritivo e relevante

## 🐛 Troubleshooting

**Meta tags não aparecem?**

```bash
php artisan view:clear
php artisan cache:clear
```

**Componente não encontrado?**

-   Verificar: `resources/views/components/seo-meta.blade.php`
-   Executar: `php artisan view:clear`

**Helper não funciona?**

```bash
composer dump-autoload
```
