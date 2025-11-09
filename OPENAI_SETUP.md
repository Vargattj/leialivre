# Configuração OpenAI

Este documento descreve como configurar o enriquecimento de conteúdo com IA usando OpenAI.

## Variáveis de Ambiente

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# OpenAI API Configuration
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
OPENAI_ENABLED=true
OPENAI_TIMEOUT=30
OPENAI_MAX_RETRIES=3
OPENAI_RETRY_DELAY=1

# Token Limits
OPENAI_MAX_TOKENS_SYNOPSIS=200
OPENAI_MAX_TOKENS_DESCRIPTION=1000
OPENAI_MAX_TOKENS_BIOGRAPHY=1000

# Temperature (0.0 a 2.0, padrão: 0.7)
OPENAI_TEMPERATURE=0.7

# Cache Configuration
OPENAI_CACHE_ENABLED=true
OPENAI_CACHE_TTL=86400
```

## Descrição das Variáveis

- **OPENAI_API_KEY**: Sua chave de API da OpenAI (obrigatório se OPENAI_ENABLED=true)
- **OPENAI_MODEL**: Modelo a ser usado (padrão: `gpt-4o-mini`, alternativas: `gpt-3.5-turbo`, `gpt-4`, etc.)
- **OPENAI_ENABLED**: Habilita/desabilita o uso de IA (padrão: `false`)
- **OPENAI_TIMEOUT**: Timeout em segundos para requisições (padrão: 30)
- **OPENAI_MAX_RETRIES**: Número máximo de tentativas em caso de falha (padrão: 3)
- **OPENAI_RETRY_DELAY**: Delay em segundos entre tentativas (padrão: 1)
- **OPENAI_MAX_TOKENS_SYNOPSIS**: Máximo de tokens para sinopse (padrão: 200)
- **OPENAI_MAX_TOKENS_DESCRIPTION**: Máximo de tokens para descrição completa (padrão: 1000)
- **OPENAI_MAX_TOKENS_BIOGRAPHY**: Máximo de tokens para biografia (padrão: 1000)
- **OPENAI_TEMPERATURE**: Criatividade da resposta (0.0 = determinístico, 2.0 = muito criativo, padrão: 0.7)
- **OPENAI_CACHE_ENABLED**: Habilita cache de respostas (padrão: `true`)
- **OPENAI_CACHE_TTL**: Tempo de vida do cache em segundos (padrão: 86400 = 24 horas)

## Como Funciona

O sistema enriquece automaticamente os seguintes campos quando a IA está habilitada:

### Books
- **synopsis**: Sinopse curta e atrativa (máx 500 caracteres)
- **full_description**: Descrição completa consolidando informações de todas as fontes

### Authors
- **biography**: Biografia completa consolidando informações de OpenLibrary e Wikipedia

## Fallback

Se a IA não estiver habilitada ou falhar, o sistema usa automaticamente a consolidação normal baseada em regras de prioridade entre as fontes.

## Cache

O sistema usa cache para evitar chamadas repetidas à API OpenAI para o mesmo conteúdo. O cache é baseado em hash do conteúdo das fontes, garantindo que mudanças nas fontes resultem em novas chamadas à IA.

## Custos

Lembre-se de monitorar o uso da API OpenAI, pois cada chamada tem custo associado. O cache ajuda a reduzir chamadas desnecessárias.

