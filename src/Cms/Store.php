<?php

namespace Kirby\Cms;

use Exception;
use Kirby\Util\Properties;

abstract class Store
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    abstract public function exists(): bool;
    abstract public function id();

    public function kirby()
    {
        return $this->model()->kirby();
    }

    public function media()
    {
        return $this->model()->kirby()->media();
    }

    public function model()
    {
        return $this->model;
    }

}
