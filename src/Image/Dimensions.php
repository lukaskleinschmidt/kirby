<?php

namespace Kirby\Image;

/**
 * The Dimension class is used to provide additional
 * methods for images and possibly other objects with
 * width and height to recalculate the size,
 * get the ratio or just the width and height.
 *
 * @package   Kirby Image
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Dimensions
{

    /**
     * the width of the parent object
     *
     * @var int
     */
    public $width = 0;

    /**
     * the height of the parent object
     *
     * @var int
     */
    public $height = 0;

    /**
     * Constructor
     *
     * @param int $width
     * @param int $height
     */
    public function __construct(int $width, int $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    /**
     * Returns the width
     *
     * @return int
     */
    public function width(): int
    {
        return $this->width;
    }

    /**
     * Returns the height
     *
     * @return int
     */
    public function height(): int
    {
        return $this->height;
    }

    /**
     * Calculates and returns the ratio
     *
     * <code>
     *
     * $dimensions = new Dimensions(1200, 768);
     * echo $dimensions->ratio();
     * // output: 1.5625
     *
     * </code>
     *
     * @return float
     */
    public function ratio(): float
    {
        if ($this->width !== 0 && $this->height !== 0) {
            return ($this->width / $this->height);
        }

        return 0;
    }

    /**
     * Recalculates the width and height to fit into the given box.
     *
     * <code>
     *
     * $dimensions = new Dimensions(1200, 768);
     * $dimensions->fit(500);
     *
     * echo $dimensions->width();
     * // output: 500
     *
     * echo $dimensions->height();
     * // output: 320
     *
     * </code>
     *
     * @param int         $box    the max width and/or height
     * @param bool        $force  If true, the dimensions will be
     *                            upscaled to fit the box if smaller
     * @return Dimensions         object with recalculated dimensions
     */
    public function fit(int $box, bool $force = false): self
    {
        if ($this->width == 0 || $this->height == 0) {
            $this->width  = $box;
            $this->height = $box;
            return $this;
        }

        $ratio = $this->ratio();

        if ($this->width > $this->height) {
            // wider than tall
            if ($this->width > $box || $force === true) {
                $this->width = $box;
            }
            $this->height = (int)round($this->width / $ratio);
        } elseif ($this->height > $this->width) {
            // taller than wide
            if ($this->height > $box || $force === true) {
                $this->height = $box;
            }
            $this->width = (int)round($this->height * $ratio);
        } elseif ($this->width > $box) {
            // width = height but bigger than box
            $this->width  = $box;
            $this->height = $box;
        }

        return $this;
    }

    /**
     * Helper for fitWidth and fitHeight methods
     *
     * @param   string      $ref    reference (width or height)
     * @param   int         $fit    the max width
     * @param   bool        $force  If true, the dimensions will be
     *                              upscaled to fit the box if smaller
     * @return  Dimensions          object with recalculated dimensions
    */
    protected function fitSize(string $ref, int $fit, bool $force = false): self
    {
        if ($fit === 0) {
            return $this;
        }

        if ($this->$ref <= $fit && !$force) {
            return $this;
        }

        $ratio        = $this->ratio();
        $mode         = $ref === 'width';
        $this->width  =  $mode ? $fit : (int)round($fit * $ratio);
        $this->height = !$mode ? $fit : (int)round($fit / $ratio);

        return $this;
    }

    /**
     * Recalculates the width and height to fit the given width
     *
     * <code>
     *
     * $dimensions = new Dimensions(1200, 768);
     * $dimensions->fitWidth(500);
     *
     * echo $dimensions->width();
     * // output: 500
     *
     * echo $dimensions->height();
     * // output: 320
     *
     * </code>
     *
     * @param   int         $fit    the max width
     * @param   bool        $force  If true, the dimensions will be
     *                              upscaled to fit the box if smaller
     * @return  Dimensions          object with recalculated dimensions
    */
    public function fitWidth(int $fit, bool $force = false): self
    {
        return $this->fitSize('width', $fit, $force);
    }

    /**
     * Recalculates the width and height to fit the given height
     *
     * <code>
     *
     * $dimensions = new Dimensions(1200, 768);
     * $dimensions->fitHeight(500);
     *
     * echo $dimensions->width();
     * // output: 781
     *
     * echo $dimensions->height();
     * // output: 500
     *
     * </code>
     *
     * @param   int         $fit    the max height
     * @param   bool        $force  If true, the dimensions will be
     *                              upscaled to fit the box if smaller
     * @return  Dimensions          object with recalculated dimensions
     */
    public function fitHeight(int $fit, bool $force = false): self
    {
        return $this->fitSize('height', $fit, $force);
    }

    /**
     * Recalculates the dimensions by the width and height
     *
     * @param   int         $width      the max height
     * @param   int         $height     the max width
     * @param   bool        $force
     * @return  Dimensions
     */
    public function fitWidthAndHeight(int $width, int $height, bool $force = false): self
    {
        if ($this->width > $this->height) {
            $this->fitWidth($width, $force);

            // do another check for the max height
            if ($this->height > $height) {
                $this->fitHeight($height);
            }
        } else {
            $this->fitHeight($height, $force);

            // do another check for the max width
            if ($this->width > $width) {
                $this->fitWidth($width);
            }
        }

        return $this;
    }

    /**
     * @param   int         $width
     * @param   int         $height
     * @param   bool        $force
     * @return  Dimensions
     */
    public function resize(int $width, int $height, bool $force = false): self
    {
        return $this->fitWidthAndHeight($width, $height, $force);
    }

    /**
     * Crops the dimensions by width and height
     *
     * @param    int         $width
     * @param    int         $height
     * @return   Dimensions
     */
    public function crop(int $width, int $height = 0): self
    {
        $this->width  = $width;
        $this->height = $width;

        if ($height !== 0) {
            $this->height = $height;
        }

        return $this;
    }

    /**
     * Returns a string representation of the orientation
     *
     * @return string|false
     */
    public function orientation()
    {
        if (!$this->ratio()) {
            return false;
        } elseif ($this->portrait()) {
            return 'portrait';
        } elseif ($this->landscape()) {
            return 'landscape';
        }

        return 'square';
    }

    /**
     * Checks if the dimensions are portrait
     *
     * @return bool
     */
    public function portrait(): bool
    {
        return $this->height > $this->width;
    }

    /**
     * Checks if the dimensions are landscape
     *
     * @return bool
     */
    public function landscape(): bool
    {
        return $this->width > $this->height;
    }

    /**
     * Checks if the dimensions are square
     *
     * @return bool
     */
    public function square(): bool
    {
        return $this->width == $this->height;
    }

    /**
     * Converts the dimensions object
     * to a plain PHP array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'width'       => $this->width(),
            'height'      => $this->height(),
            'ratio'       => $this->ratio(),
            'orientation' => $this->orientation(),
        ];
    }

    /**
     * Echos the dimensions as width × height
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->width . ' × ' . $this->height;
    }

    /**
     * Improved var_dump() output
     *
     * @return array
     */
    public function __debuginfo(): array
    {
        return $this->toArray();
    }
}
