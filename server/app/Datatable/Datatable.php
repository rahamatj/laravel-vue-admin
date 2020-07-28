<?php

namespace App\Datatable;

class Datatable
{
    protected $query;
    protected $filterableColumns = [];
    protected $orderByColumn = '';
    protected $order = 'asc';
    protected $getLatest = false;
    protected $perPage = 25;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function filterBy(array $filterableColumns)
    {
        $this->filterableColumns = $filterableColumns;
    }

    public function orderBy($column, $order = 'asc')
    {
        $this->orderByColumn = $column;
        $this->order = $order;
    }

    public function latest()
    {
        $this->getLatest = true;
    }

    public function query()
    {
        $this->sort();
        $this->filter();

        return $this->query;
    }

    public function sort()
    {
        if (request()->has('sort_by') && request()->sort_by != null)
            if (request()->has('sort_desc') && request()->sort_desc == 1)
                $this->query->orderByDesc(request()->sort_by);
            else
                $this->query->orderBy(request()->sort_by);
        else if($this->orderByColumn)
            $this->query->orderBy($this->orderByColumn, $this->order);
        else if ($this->getLatest)
            $this->query->latest();
    }

    public function filter()
    {
        if (request()->has('filter') && request()->filter != null && ! empty($this->filterableColumns))
            $this->query->where(function ($query) {
                foreach ($this->filterableColumns as $filterableColumn)
                    $query->orWhere($filterableColumn, 'like', '%' . request()->filter . '%');
            });
    }


    public function get(?callable $callback = null)
    {
        if (request()->has('per_page') && request()->per_page != null)
            $this->perPage = request()->per_page;

        if (is_callable($callback))
            return $callback($this->query())->paginate($this->perPage);

        return $this->query()->paginate($this->perPage);
    }
}