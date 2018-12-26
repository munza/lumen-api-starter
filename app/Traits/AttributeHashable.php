<?php

namespace App\Traits;

trait AttributeHashable
{
    /**
     * Boot method for the trait.
     *
     * @return void
     */
    public static function bootAttributeHashable(): void
    {
        static::saving(function ($model) {
            foreach ($model->hashable as $attribute) {
                if (!$model->isDirty($attribute)) {
                    continue;
                }

                $model->attributes[$attribute] = app('hash')->make($model->attributes[$attribute]);
            }
        });
    }
}
