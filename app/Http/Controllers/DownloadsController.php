<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadsController extends Controller
{
    //
    public function download($filename, $path)
    {
        if (is_null($path)) {
            $path = "logs";
        }

        $file = storage_path("/$path/$filename");
        if (file_exists($file)) {
            return response()->download($file)->deleteFileAfterSend(true);
        }
        return redirect()->back()->with("status", "Oops! Could not Download!");

    }
}
