<?php

namespace Pkeogan\LaravelAlpacaJS\Models\Fields;

use Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use App\Exceptions\GeneralException;

/**
 * Trait build
 */
interface  RenderFieldInterface
{
	public function render();
}
