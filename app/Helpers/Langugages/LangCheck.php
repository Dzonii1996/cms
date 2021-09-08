<?php


namespace App\Helpers\Langugages;




use Illuminate\Support\Facades\Config;

class LangCheck
{
public  static  function  LangCheckIfExist ($lang) {
    $existing_lang=Config::get('languages');
    if (env('MULTILINGUAL') !==false){
        if (!array_key_exists($lang, $existing_lang)){
            return response()->json(['message' => 'The language you selected does not exist'], 404);
        }


    }
}


}
