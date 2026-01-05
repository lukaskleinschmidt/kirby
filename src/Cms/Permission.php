<?php

namespace Kirby\Cms;

use JsonSerializable;

class Permission implements JsonSerializable
{
	public function __construct(
		public readonly bool $value,
		public readonly PermissionSource $source,
	) {}

	public function undefined(): bool
	{
		return $this->source === PermissionSource::Defaults
			|| $this->source === PermissionSource::Wildcard;
	}

	public static function default(bool $value): self
	{
		return new self($value, PermissionSource::Defaults);
	}

	public static function defined(array $values, bool $default = true): self
	{
		foreach ($values as $value) {
			$value = self::for($value);

			if ($value->undefined()) {
				continue;
			}

			return $value;
		}

		return self::default($default);
	}

	public static function for(Permission|bool $value): self
	{
		if ($value instanceof self) {
			return $value;
		}

		return self::default($value);
	}

	public function jsonSerialize(): bool
	{
		return $this->value;
	}

	public static function setting(bool $value): self
	{
		return new self($value, PermissionSource::Settings);
	}

	public static function wildcard(bool $value): self
	{
		return new self($value, PermissionSource::Wildcard);
	}
}
