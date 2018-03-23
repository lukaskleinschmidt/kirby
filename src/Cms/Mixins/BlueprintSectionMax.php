<?php

namespace Kirby\Cms\Mixins;

use Exception;

trait BlueprintSectionMax
{

    protected $max;

    public function max()
    {
        return $this->max;
    }

    protected function setMax(int $max = null)
    {
        $this->max = $max;
        return $this;
    }

    protected function validateMax(): bool
    {
        if ($max = $this->max()) {
            if ($this->total() > $max) {
                throw new Exception('No more than ' . $max . ' entries allowed');
            }
        }

        return true;
    }

}
