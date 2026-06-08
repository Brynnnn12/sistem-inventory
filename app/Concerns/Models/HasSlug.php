<?php

namespace App\Concerns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait for a model.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->slug) && ! empty($model->name)) {
                $model->slug = static::generateUniqueSlug($model, Str::slug($model->name));
            }
        });

        static::updating(function (Model $model) {
            if ($model->isDirty('name')) {
                $model->slug = static::generateUniqueSlug($model, Str::slug($model->name));
            }
        });
    }

    protected static function generateUniqueSlug(Model $model, string $slug): string
    {
        $original = $slug;
        $counter = 1;

        while (static::slugExists($model, $slug)) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }

    protected static function slugExists(Model $model, string $slug): bool
    {
        $query = $model->newQuery()->where('slug', $slug);

        if ($model->exists) {
            $query->where($model->getKeyName(), '!=', $model->getKey());
        }

        return $query->withTrashed()->exists();
    }
}
