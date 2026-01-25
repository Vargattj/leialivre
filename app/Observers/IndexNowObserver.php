<?php

namespace App\Observers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Services\IndexNowService;
use Illuminate\Support\Facades\Log;

class IndexNowObserver
{
    public function __construct(protected IndexNowService $indexNowService)
    {
    }

    /**
     * Handle the model "created" event.
     */
    public function created(mixed $model): void
    {
        $this->notifyIndexNow($model);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(mixed $model): void
    {
        $this->notifyIndexNow($model);
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(mixed $model): void
    {
        $this->notifyIndexNow($model);
    }

    /**
     * Notify IndexNow about the model's URL.
     */
    protected function notifyIndexNow(mixed $model): void
    {
        $url = $this->resolveUrl($model);

        if ($url) {
            $this->indexNowService->submit($url);
        }
    }

    /**
     * Resolve the URL for the given model.
     */
    protected function resolveUrl(mixed $model): ?string
    {
        try {
            if ($model instanceof Book) {
                return route('livros.show', $model->slug);
            }

            if ($model instanceof Author) {
                return route('autores.show', $model->slug);
            }

            if ($model instanceof Category) {
                return route('livros.categorias', $model->slug);
            }
        } catch (\Exception $e) {
            Log::warning('IndexNow: Could not resolve URL for model.', [
                'model' => get_class($model),
                'id' => $model->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
}
