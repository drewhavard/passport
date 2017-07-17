<?php

namespace DrewHavard\Passport;

use Webpatser\Uuid\Uuid;

trait UuidForKey {
	/**
	 * Boot function from laravel.
	 */

	protected static function boot() {
		parent::boot();

		static::creating(function ($model) {
			$model->{$model->getKeyName()} = Uuid::generate()->string;
		});
	}
}