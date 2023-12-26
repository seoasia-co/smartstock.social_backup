<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function index(Request $request)
    {

        \Session::put('lang', $request->input('locale'));
        return redirect()->back();

    }

    public function change($lang)
    {
        \Session::put('lang', $lang);
        try {
            $prev_url = url()->previous();
            $Languages = Language::all();
            foreach ($Languages as $Language) {
                if ($lang == env("DEFAULT_LANGUAGE") && env("DEFAULT_LANGUAGE") != "") {
                    $prev_url = str_replace("/" . $Language->code . "/", "/", $prev_url);
                } else {
                    $prev_url = str_replace("/" . $Language->code . "/", "/" . $lang . "/", $prev_url);
                }
            }
            return redirect()->to($prev_url);
        } catch (\Exception $e) {
            return redirect()->route("Home");
        }
    }

    public function locale($lang)
    {
        \Session::put('lang', $lang);
        return redirect()->back();

    }
}
