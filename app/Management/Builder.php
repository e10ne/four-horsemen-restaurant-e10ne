<?php

namespace App\Management;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

final class Builder
{
    public function __construct()
    {
        $this->columns = new Collection();
    }

    /*
     * This function defines a column in the index page.
     *
     * @param $column The same column used in the database, used for sorting the results.
     * @param $header Displayed at the top of the index page, only descriptive.
     *
     * @param $map if set then this function will be used to determine the value that is displayed.
     * If not set the database column will be used.
     */
    public function defineColumn(
        string $column,
        string $header,
        ?callable $map = null
    ): self {
        $this->columns->push(
            new Column(
                $column,
                $header,
                $map !== null
                    ? $map
                    : function (model $model) use ($column) {
                        return $model->$column;
                    }
            )
        );

        return $this;
    }

    /*
     * This function defines a field on the left side of the.
     * This can be the show, create or edit page.
     * Uses components internally which transform the type in something useful.
     *
     * @param $column The same column used in the database, used as input for the field.
     * @param $type The type of field, which will be passed to the component.
     * @param $label Display text which labeles the current field.
     *
     * @param $map if set then this function will be used to determine the value that is displayed.
     * Only used in show page.
     * If not set the database column will be used.
     */
    public function defineFieldLeft(
        string $column,
        string $type,
        string $label,
        ?callable $map = null
    ): self {
        return $this;
    }

    /*
     * This function defines a field on the right side of the.
     * This can be the show, create or edit page.
     * Uses components internally which transform the type in something useful.
     *
     * @param $column The same column used in the database, used as input for the field.
     * @param $type The type of field, which will be passed to the component.
     * @param $label Display text which labeles the current field.
     *
     * @param $map if set then this function will be used to determine the value that is displayed.
     * Only used in show page.
     * If not set the database column will be used.
     */
    public function defineFieldRight(
        string $column,
        string $type,
        string $label,
        ?callable $map = null
    ): self {
        return $this;
    }

    /*
     * @param $routes These routes will be excluded, as a result these routes will result in a 404 if used anyways. Additionally avoids linking to invalid routes.
     */
    public function exclude(array $routes): self
    {
        return $this;
    }

    public Collection $columns;
}
