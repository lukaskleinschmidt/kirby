<?php

namespace Kirby\Form\Mixins;

trait Text
{

    protected $text;

    protected function defaultText(): string
    {
        return '';
    }

    protected function setText(string $text = null)
    {
        $this->text = $this->translate($text);
        return $this;
    }

    public function text(): string
    {
        return markdown(kirbytext($this->text));
    }

}
