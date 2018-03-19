<?php

namespace Kirby\Cms;

use Exception;

class Permissions
{

    protected $actions = [
        'file' => [
            'changeName' => true,
            'create' => true,
            'delete' => true,
            'replace' => true,
            'update' => true
        ],
        'page' => [
            'changeTemplate' => true,
            'changeTitle' => true,
            'changeSlug' => true,
            'changeStatus' => true,
            'create' => true,
            'delete' => true,
            'update' => true
        ],
        'site' => [
            'update' => true
        ],
        'user' => [
            'changeEmail' => true,
            'changeLanguage' => true,
            'changeName' => true,
            'changePassword' => true,
            'changeRole' => true,
            'create' => true,
            'delete' => true,
            'update' => true
        ],
    ];

    public function __construct($settings)
    {
        if (is_bool($settings) === true) {
            return $this->setAll($settings);
        }

        if (is_array($settings) === true) {
            return $this->setCategories($settings);
        }
    }

    protected function hasAction(string $category, string $action)
    {
        return $this->hasCategory($category) === true && array_key_exists($action, $this->actions[$category]) === true;
    }

    protected function hasCategory(string $category)
    {
        return array_key_exists($category, $this->actions) === true;
    }

    protected function setAction(string $category, string $action, $setting)
    {
        // wildcard to overwrite the entire category
        if ($action === '*') {
            return $this->setCategory($category, $setting);
        }

        if ($this->hasAction($category, $action) === false) {
            throw new Exception('Invalid permissions action');
        }

        $this->actions[$category][$action] = $setting;

        return $this;
    }

    protected function setAll(bool $setting)
    {
        foreach ($this->actions as $categoryName => $actions) {
            $this->setCategory($categoryName, $setting);
        }

        return $this;
    }

    protected function setCategories(array $settings)
    {
        foreach ($settings as $categoryName => $categoryActions) {

            if (is_bool($categoryActions) === true) {
                $this->setCategory($categoryName, $categoryActions);
            }

            if (is_array($categoryActions) === true) {
                foreach ($categoryActions as $actionName => $actionSetting) {
                    $this->setAction($categoryName, $actionName, $actionSetting);
                }
            }

        }

        return $this;
    }

    protected function setCategory(string $category, bool $setting)
    {
        if ($this->hasCategory($category) === false) {
            throw new Exception('Invalid permissions category');
        }

        foreach ($this->actions[$category] as $actionName => $actionSetting) {
            $this->actions[$category][$actionName] = $setting;
        }

        return $this;
    }

    public function toArray(): array
    {
        return $this->actions;
    }

}
