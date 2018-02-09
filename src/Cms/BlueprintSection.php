<?php

namespace Kirby\Cms;

use Exception;

class BlueprintSection extends BlueprintObject
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * Creates a new BlueprintSection object
     *
     * @param array $props
     */
    public function __construct(array $props)
    {
        $props = $this->extend($props);
        $this->setProperties($props);
    }

    /**
     * General factory for any section type
     *
     * @param array $props
     * @return BlueprintSection
     */
    public static function factory(array $props)
    {
        if (isset($props['type']) === false) {
            throw new Exception('Missing section type');
        }

        $className = __NAMESPACE__ . '\\Blueprint' . ucfirst($props['type']) . 'Section';

        if (class_exists($className) === false) {
            throw new Exception(sprintf('Invalid section type: "%s"', $props['type']));
        }

        return new $className($props);
    }

    /**
     * Gets the value of id
     * Will fall back to the name
     *
     * @return string
     */
    public function id(): string
    {
        return $this->name();
    }

    /**
     * Returns the simple name of the model type
     *
     * @param object|null $model
     * @return string
     */
    public function modelType($model = null): string
    {
        $model = $model ?? $this->model();
        $types = [
            'page' => Page::class,
            'site' => Site::class,
            'file' => File::class,
            'user' => User::class
        ];

        foreach ($types as $type => $className) {
            if (is_a($model, $className) === true) {
                return $type;
            }
        }

        throw new Exception('Unsupported model type');
    }

    /**
     * Gets the value of name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function stringQuery(string $query, array $data = [])
    {
        return (new Query($query, $this->stringQueryData($data)))->result();
    }

    public function stringQueryData($data = []): array
    {
        $model = $this->model();

        if ($model === null) {
            throw new Exception('Missing model');
        }

        $defaults = [
            'site'  => $model->site(),
            'kirby' => $model->kirby(),
        ];

        // inject the model with the simple model name
        $defaults[$this->modelType()] = $model;

        return array_merge($defaults, $data);
    }

    public function stringTemplate(string $template = null, array $data = [])
    {
        return (new Tempura($template, $this->stringQueryData($data)))->render();
    }

    /**
     * Sets the value of name
     *
     * @param string $name
     * @return self
     */
    protected function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the value of type
     *
     * @param string $type
     * @return  self
     */
    protected function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Converts the section object to a handy
     * array i.e. for API results
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->propertiesToArray();
    }

    /**
     * Gets the value of type
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function toLayout()
    {
        return $this->id();
    }

}
