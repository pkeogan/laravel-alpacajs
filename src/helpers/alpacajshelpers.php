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
  