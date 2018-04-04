<?php

namespace Kirby\Cms;

use Kirby\Exception\InvalidArgumentException;
use Exception;

trait HasContent
{

    /**
     * The content
     *
     * @var Content
     */
    protected $content;

    /**
     * Modified getter to also return fields
     * from the object's content
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments = [])
    {
        return $this->content()->get($method, $arguments);
    }

    /**
     * Prepares the content object for the
     * toArray method
     *
     * @return array
     */
    protected function convertContentToArray(): array
    {
        return $this->content()->toArray();
    }

    /**
     * Returns the content
     *
     * @return Content
     */
    public function content(): Content
    {
        if (is_a($this->content, Content::class) === true) {
            return $this->content;
        }

        return $this->setContent($this->store()->content())->content();
    }

    /**
     * Returns a formatted date field from the content
     *
     * @param string $format
     * @param string $field
     * @return Field
     */
    public function date(string $format = null, $field = 'date')
    {
        return $this->content()->get($field)->toDate($format);
    }

    /**
     * Checks if the model data has any errors
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return Form::for($this)->hasErrors() === false;
    }

    /**
     * Sets the Content object
     *
     * @param Content|null $content
     * @return self
     */
    protected function setContent(array $content = null): self
    {
        if ($content !== null) {
            $content = new Content($content, $this);
        }

        $this->content = $content;
        return $this;
    }

    /**
     * Updates the model data
     *
     * @param array $input
     * @param boolean $validate
     * @return self
     */
    public function update(array $input = null, bool $validate = true): self
    {
        $form = Form::for($this, [
            'values' => $input
        ]);

        // validate the input
        if ($validate === true) {
            try {
                $form->isValid();
            } catch (Exception $e) {
                throw new InvalidArgumentException([
                    'details' => $form->errors()
                ]);
            }
        }

        // get the data values array
        $values  = $form->values();
        $strings = $form->stringValues();

        return $this->commit('update', $values, $strings);
    }

}
