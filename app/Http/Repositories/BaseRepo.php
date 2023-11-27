<?php

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepo
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->latest()->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $model = $this->find($id);

        if ($model) {
            $model->update($attributes);

            return $model;
        }

        return null;
    }

    public function delete($id)
    {
        $model = $this->find($id);

        if ($model) {
            $model->delete();

            return true;
        }

        return false;
    }

    public function where(array $conditions)
    {
        $query = $this->model;

        foreach ($conditions as $column => $value) {
            $query = $query->where($column, $value);
        }

        return $query->latest()->get();
    }

    public function whereFirst(array $conditions)
    {
        $query = $this->model;

        foreach ($conditions as $column => $value) {
            $query = $query->where($column, $value);
        }

        return $query->first();
    }

    public function exists(array $conditions)
    {
        $query = $this->model;

        foreach ($conditions as $column => $value) {
            $query = $query->where($column, $value);
        }

        return $query->exists();
    }

    public function fill($id, $data)
    {
        $model = $this->find($id);
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }

        if($model->save()) return $model;
    }

    public function getDataWithPigination($perPage = 10, $page = 1, $orderBy = 'created_at', $searches = null, $conditions = [], $orConditions = [], $with = [])
    {
        $query = $this->model->query();

        if (count($with) > 0) {
            $query->with($with);
        }
        $offset = $perPage * ($page - 1);

        // Apply offset and limit
        if ($offset > 0) {
            $query->offset($offset);
        }

        if ($orderBy) {
            $query->orderBy($orderBy, 'desc');
        }

        $query->limit($perPage);

        if ($searches) {
            $query->where(function ($q) use ($searches) {
                foreach ($searches as $key => $value) {
                    $q->orWhere($key, 'LIKE', "%$value%");
                }
            });
        }

        foreach ($conditions as $key => $condition) {
            $query->where($key, $condition);
        }

        foreach ($orConditions as $key => $condition) {
            $query->orWhere($key, $condition);
        }
        // Get total count
        $totalCount = $query->count();

        // Calculate total pages
        $totalPages = ceil($totalCount / $perPage);

        $results = $query->latest()->get();

        return [
            'data' => $results,
            'meta' => [
                'total' => $totalCount,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => $totalPages,
            ],
        ];
    }
}
