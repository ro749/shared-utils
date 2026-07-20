<?php

namespace Ro749\SharedUtils\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
class LangController{
    public function index() {
        $lang_info = [];
        foreach(File::directories(storage_path('lang')) as $lang) {
            foreach(File::files($lang) as $lang_section) {
                $lang_info[basename($lang)][basename($lang_section,'.json')] = json_decode(file_get_contents($lang_section), true);
            }
        }
        return response()->json($lang_info, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function changeLang() {
        $lang = Session::get('lang', 'es');
        Session::put('lang', $lang=='es'?'en':'es');
    }
}