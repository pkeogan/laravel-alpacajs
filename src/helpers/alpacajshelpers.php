<?php

    if (! function_exists('abortJson')) {
        /**
         * Helper to grab the application name.
         *
         * @return mixed
         */
        function abortJson($code, $message = 'There was an error that occured.')
        {
           return response()->json(['code' => $code, 'message' => $message], $code);
        }
		
    }

  if (! function_exists('array_mirror_values')) {

    /**
     * @return string
     */
    function array_mirror_values($array)
    {
		$newArray = array();
		foreach($array as $key=>$value){
			$newArray[$value] = $value;
		}
		return $newArray;
    }
}