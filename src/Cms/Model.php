<?php

namespace Kirby\Cms;

use Exception;
use stdClass;
use ReflectionMethod;
use Kirby\Util\Str;

abstract class Model extends Object
{

    /**
     * The parent collection
     *
     * @var Collection
     */
    protected $collection;

    /**
     * The parent Kirby instance
     *
     * @var App
     */
    protected $kirby;

    /**
     * The parent Site instance
     *
     * @var Site
     */
    protected $site;

    /**
     * The Store instance
     *
     * @var Store
     */
    protected $store;

    /**
     * Returns the default parent collection
     *
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * Returns the parent Kirby instance
     *
     * @return App|null
     */
    public function kirby(): App
    {
        if (is_a($this->kirby, App::class)) {
            return $this->kirby;
        }

        return $this->kirby = App::instance();
    }

    /**
     * Returns the parent Site instance
     *
     * @return Site|null
     */
    public function site()
    {
        if (is_a($this->site, Site::class)) {
            return $this->site;
        }

        return $this->site = $this->kirby()->site();
    }

    /**
     * Sets the parent Collection object
     * This is used to handle traversal methods
     * like next, prev, etc.
     *
     * @param Collection|null $collection
     * @return self
     */
    public function setCollection(Collection $collection = null)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Setter for the parent Kirby object
     *
     * @param Kirby|null $kirby
     * @return self
     */
    protected function setKirby(App $kirby = null)
    {
        $this->kirby = $kirby;
        return $this;
    }

    /**
     * Setter for the parent Site object
     *
     * @param Site|null $site
     * @return self
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * Setter for the Store instance
     *
     * @param Store $store
     * @return self
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return Store
     */
    protected function store()
    {
        if (is_a($this->store, Store::class) === true) {
            return $this->store;
        }

        $className = $this->store;
        return new $className($this);
    }

}
