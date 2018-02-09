<?php

namespace Kirby\Cms;

use Exception;
use Kirby\Image\Image;

class AvatarStoreDefault extends Store
{

    public function asset()
    {
        return new Image('profile.jpg', $this->url());
    }

    public function avatar()
    {
        return $this->model;
    }

    public function create(string $source)
    {
        throw new Exception('The avatar cannot be created');
    }

    public function delete(): bool
    {
        throw new Exception('The avatar cannot be deleted');
    }

    public function exists(): bool
    {
        return false;
    }

    public function id()
    {
        return $this->user()->id();
    }

    public function replace(string $source)
    {
        throw new Exception('The avatar cannot be replaced');
    }

    public function thumb(array $options = [])
    {
        throw new Exception('The avatar cannot be resized');
    }

    public function url(): string
    {
        return $this->user()->mediaUrl() . '/profile.jpg';
    }

    public function user()
    {
        return $this->model->user();
    }

}
