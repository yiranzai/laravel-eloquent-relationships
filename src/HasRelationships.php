<?php

namespace Ankurk91\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Ankurk91\Eloquent\Relations\MorphManyTo;
use Ankurk91\Eloquent\Relations\MorphOneTo;

trait HasRelationships
{
    /**
     * Define a polymorphic, direct one-to-many relationship.
     *
     * @param  string|null  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $foreignKey
     * @return \Ankurk91\Eloquent\Relations\MorphManyTo
     */
    public function morphManyTo($name = null, $type = null, $id = null, $foreignKey = null)
    {
        // If no name is provided, we will use the backtrace to get the function name
        // since that is most likely the name of the polymorphic interface. We can
        // use that to get both the class and foreign key that will be utilized.
        $name = $name ?: $this->guessBelongsToRelation();

        [$type, $id] = $this->getMorphs(
            Str::snake($name), $type, $id
        );

        // If the type value is null it is probably safe to assume we're eager loading
        // the relationship. In this case we'll just pass in a dummy query where we
        // need to remove any eager loads that may already be defined on a model.
        return empty($class = $this->{$type})
            ? $this->morphManyEagerTo($name, $type, $id, $foreignKey)
            : $this->morphManyInstanceTo($class, $name, $type, $id, $foreignKey);
    }

    /**
     * Define a polymorphic, direct one-to-many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $foreignKey
     * @return \Ankurk91\Eloquent\Relations\MorphManyTo
     */
    protected function morphManyEagerTo($name, $type, $id, $foreignKey)
    {
        return $this->newMorphManyTo(
            $this->newQuery()->setEagerLoads([]), $this, $id, $foreignKey, $type, $name
        );
    }

    /**
     * Define a polymorphic, direct one-to-many relationship.
     *
     * @param  string  $target
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $foreignKey
     * @return \Ankurk91\Eloquent\Relations\MorphManyTo
     */
    protected function morphManyInstanceTo($target, $name, $type, $id, $foreignKey)
    {
        $instance = $this->newRelatedInstance(
            static::getActualClassNameForMorph($target)
        );

        return $this->newMorphManyTo(
            $instance->newQuery(), $this, $id, $foreignKey ?? $instance->getKeyName(), $type, $name
        );
    }

    /**
     * Instantiate a new MorphManyTo relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $localKey
     * @param  string  $foreignKey
     * @param  string  $type
     * @param  string  $relation
     * @return \Ankurk91\Eloquent\Relations\MorphManyTo
     */
    protected function newMorphManyTo(Builder $query, Model $parent, $localKey, $foreignKey, $type, $relation)
    {
        return new MorphManyTo($query, $parent, $localKey, $foreignKey, $type, $relation);
    }

    /**
     * Define a polymorphic, direct one-to-one relationship.
     *
     * @param  string|null  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $foreignKey
     * @return \Ankurk91\Eloquent\Relations\MorphOneTo
     */
    public function morphOneTo($name = null, $type = null, $id = null, $foreignKey = null)
    {
        // If no name is provided, we will use the backtrace to get the function name
        // since that is most likely the name of the polymorphic interface. We can
        // use that to get both the class and foreign key that will be utilized.
        $name = $name ?: $this->guessBelongsToRelation();

        [$type, $id] = $this->getMorphs(
            Str::snake($name), $type, $id
        );

        // If the type value is null it is probably safe to assume we're eager loading
        // the relationship. In this case we'll just pass in a dummy query where we
        // need to remove any eager loads that may already be defined on a model.
        return empty($class = $this->{$type})
            ? $this->morphOneEagerTo($name, $type, $id, $foreignKey)
            : $this->morphOneInstanceTo($class, $name, $type, $id, $foreignKey);
    }

    /**
     * Define a polymorphic, direct one-to-one relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $foreignKey
     * @return \Ankurk91\Eloquent\Relations\MorphOneTo
     */
    protected function morphOneEagerTo($name, $type, $id, $foreignKey)
    {
        return $this->newMorphOneTo(
            $this->newQuery()->setEagerLoads([]), $this, $id, $foreignKey, $type, $name
        );
    }

    /**
     * Define a polymorphic, direct one-to-one relationship.
     *
     * @param  string  $target
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $foreignKey
     * @return \Ankurk91\Eloquent\Relations\MorphOneTo
     */
    protected function morphOneInstanceTo($target, $name, $type, $id, $foreignKey)
    {
        $instance = $this->newRelatedInstance(
            static::getActualClassNameForMorph($target)
        );

        return $this->newMorphOneTo(
            $instance->newQuery(), $this, $id, $foreignKey ?? $instance->getKeyName(), $type, $name
        );
    }

    /**
     * Instantiate a new MorphOneTo relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $localKey
     * @param  string  $foreignKey
     * @param  string  $type
     * @param  string  $relation
     * @return \Ankurk91\Eloquent\Relations\MorphOneTo
     */
    protected function newMorphOneTo(Builder $query, Model $parent, $localKey, $foreignKey, $type, $relation)
    {
        return new MorphOneTo($query, $parent, $localKey, $foreignKey, $type, $relation);
    }

}
