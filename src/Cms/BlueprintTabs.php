<?php

namespace Kirby\Cms;

use Exception;
use Kirby\Util\Translate;
use Kirby\Util\Str;

class BlueprintTabs
{

    use Translate;

    protected $blueprint;
    protected $tabs = [];

    public function __construct(Blueprint $blueprint, array $tabs)
    {
        $this->blueprint = $blueprint;
        $this->tabs      = $tabs;
    }

    public function columns(array $columns): array
    {
        $result = [];

        foreach ($columns as $column) {
            if (is_array($column) === false) {
                continue;
            }

            $sections = current($column);
            $width    = key($column);

            // available column widths
            $widths = [
                '1/1',
                '2/2',
                '3/3',
                '4/4',
                '6/6',
                '1/2',
                '2/4',
                '3/6',
                '1/3',
                '2/6',
                '2/3',
                '4/6',
                '1/4',
                '1/6',
                '5/6',
                '3/4',
            ];

            if (in_array($width, $widths) === false) {
                throw new Exception('Invalid column width: ' . $width);
            }

            if (empty($sections) === true) {
                throw new Exception('The sections are missing');
            }

            $result[] = [
                'sections' => $this->sections(Str::split($sections)),
                'width'    => $width,
            ];
        }

        return $result;
    }

    public function sections(array $sectionNames): array
    {
        $sections = [];

        foreach ($sectionNames as $sectionName) {
            $sectionObject = $this->blueprint->section($sectionName);

            $sections[$sectionName] = [
                'name' => $sectionObject->name(),
                'type' => $sectionObject->type()
            ];
        }

        return $sections;
    }

    public function tabs(array $tabs): array
    {
        $result = [];

        foreach ($tabs as $name => $tab) {

            if (is_string($name) === false) {
                throw new Exception('Missing tab name');
            }

            $tab = Blueprint::extend($tab);

            if (empty($tab['label']) === true) {
                throw new Exception('The tab label is missing');
            }

            if (empty($tab['columns']) === true) {
                throw new Exception('The columns are undefined or empty');
            }

            // use the key as name if the name is not already set
            $tab['name'] = $tab['name'] ?? $name;

            // convert all columns
            $tab['columns'] = $this->columns($tab['columns']);

            // translate the label if necessary
            $tab['label'] = $this->translate($tab['label']);

            ksort($tab);

            $result[$tab['name']] = $tab;

        }

        return $result;
    }

    public function toArray(): array
    {
        return array_values($this->tabs($this->tabs));
    }

}
