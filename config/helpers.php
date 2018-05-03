<?php

use Kirby\Cms\App;
use Kirby\Cms\Url;
use Kirby\Html\Attributes;
use Kirby\Http\Response\Redirect;
use Kirby\Toolkit\View;
use Kirby\Util\F;
use Kirby\Util\I18n;

function attr(array $attr = null, $before = null, $after = null)
{
    if ($attrs = (new Attributes($attr))->toString()) {
        return $before . $attrs. $after;
    }

    return null;
}

function css($url, $media = null)
{
    if (is_array($url) === true) {
        $links = array_map(function ($url) use ($media) {
            return css($url, $media);
        }, $url);

        return implode(PHP_EOL, $links);
    }

    $tag = '<link rel="stylesheet" href="%s"' . attr(['media' => $media], ' ') . '>';

    if ($url === '@auto' && $assetUrl = Url::toTemplateAsset('css/templates', 'css')) {
        return sprintf($tag, $assetUrl);
    } else {
        return sprintf($tag, Url::to($url));
    }
}

function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

function get($key, $default = null)
{
    return App::instance()->request()->get($key, $default);
}

function go($url, int $code = 301)
{
    die(new Redirect(url($url), $code));
}

function js($src, $async = null)
{
    if (is_array($src) === true) {
        $scripts = array_map(function ($src) use ($async) {
            return js($src, $async);
        }, $src);

        return implode(PHP_EOL, $scripts);
    }

    $tag = '<script src="%s"' . attr(['async' => $async], ' ') . '></script>';

    if ($src === '@auto' && $assetUrl = Url::toTemplateAsset('js/templates', 'js')) {
        return sprintf($tag, $assetUrl);
    } else {
        return sprintf($tag, Url::to($src));
    }
}

function kirby()
{
    return App::instance();
}

function kirbytag($input)
{
    return App::instance()->component('kirbytext')->tag($input);
}

function kirbytext($text, $markdown = true)
{
    $text = App::instance()->component('kirbytext')->parse($text);

    if ($markdown === true) {
        $text = markdown($text);
    }

    return $text;
}

function markdown($text)
{
    return App::instance()->component('markdown')->parse($text);
}

function option(string $key, $default = null)
{
    return App::instance()->option($key, $default);
}

function page(...$id)
{
    return App::instance()->site()->find(...$id);
}

function pages(...$id)
{
    return App::instance()->site()->find(...$id);
}

/**
 * Smart version of return with an if condition as first argument
 *
 * @param mixed $condition
 * @param mixed $value The string to be returned if the condition is true
 * @param mixed $alternative An alternative string which should be returned when the condition is false
 * @return null
 */
function r($condition, $value, $alternative = null)
{
    return $condition ? $value : $alternative;
}

function site()
{
    return App::instance()->site();
}

function smartypants($text)
{
    return App::instance()->component('smartypants')->parse($text);
}

function snippet($name, $data = [], $return = false)
{
    if (is_object($data) === true) {
        $data = ['item' => $data];
    }

    $snippet = App::instance()->component('snippet', $name, $data);

    if ($return === true) {
        return $snippet->render();
    }

    echo $snippet->render();
}

function svg(string $file)
{
    $root = App::instance()->root();
    $file = $root . '/' . $file;

    if (file_exists($file) === false) {
        return false;
    }

    ob_start();
    include F::realpath($file, $root);
    $svg = ob_get_contents();
    ob_end_clean();

    return $svg;
}

/**
 * Returns translate string for key from translation file
 *
 * @param   string|array $key
 * @param   string|null  $fallback
 * @return  mixed
 */
function t($key, string $fallback = null)
{
    return I18n::translate($key, $fallback);
}

/**
 * Translates a count
 *
 * @param   string|array $key
 * @param   int  $count
 * @return  mixed
 */
function tc($key, int $count)
{
    return I18n::translateCount($key, $count);
}

function u(string $path = null): string
{
    return Url::to();
}

function url(string $path = null): string
{
    return Url::to($path);
}

