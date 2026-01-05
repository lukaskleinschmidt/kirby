<?php

namespace Kirby\Cms;

enum PermissionSource: string
{
	case Defaults = 'defaults';
	case Settings = 'settings';
	case Wildcard = 'wildcard';
}
