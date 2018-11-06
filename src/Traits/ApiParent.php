<?php

namespace Pkeogan\LaravelAlpacaJS\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use App\Models\Auth\Permission;
/**
 * Trait build
 */
trait ApiParent
{
	use ApiTrait;
	protected $is_child = false;
	protected $is_parent = true;

	
}