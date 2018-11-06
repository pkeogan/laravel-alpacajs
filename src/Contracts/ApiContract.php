<?php

namespace Pkeogan\LaravelAlpacaJS\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;


interface ApiContract
{
    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public static function findByUuid(String $uuid);
            
	public function checkValidateAndCreate(Request $request);
	public function checkValidateAndUpdate(Request $request);
	public function checkAndRead(Request $request, $query);
	public function checkAndDelete(Request $request);
}
