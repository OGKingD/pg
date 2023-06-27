<?php

namespace App\Http\Controllers;

use ArrayIterator;
use FilesystemIterator;
use Illuminate\Http\Request;
use LimitIterator;

class DirectoryListingController extends Controller
{
    public function index(Request $request, $folder=null)
    {
        $page = $request->page ?? 1;
        $perpage = 10;
        $directory = 'logs/';
        $dir = storage_path('logs/');

        if (isset($folder)){
            //check if dir exists
            if (is_dir($dir)){
                $dir = storage_path('logs/'.$folder);
                $directory = 'logs/'.$folder;
            }
        }
        //load all files in directory;
        $dir_iterator = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
        foreach ($dir_iterator as $item) {
            $file2Sort[$item->getCTime().$item->getFilename()] = $item;
        }
        //Sort the Files In Descending Order;
        krsort($file2Sort,SORT_NUMERIC);
        //Paginate the Sorted Array of Files;
        $dir_iterator = new ArrayIterator($file2Sort);
        $paginationCount = ceil(iterator_count($dir_iterator)  /$perpage);
        $paginated = new LimitIterator($dir_iterator, ($page * $perpage) - $perpage, $perpage);
        $title = "Log Files";


        return view('admin.logs', compact("paginated", 'directory', 'paginationCount','title'));

    }
    public function download($filename, $path)
    {
        if (is_null($path)) {
            $path = "logs";
        }

        $file = storage_path("/$path/$filename");
        if (file_exists($file)) {
            if (\request()->deleteFile === "true"){
                return response()->download($file)->deleteFileAfterSend(true);
            }
            return response()->download($file);
        }
        return redirect()->back()->with("status", "Oops! Could not Download!");

    }

    public function deleteFile($filename, $path)
    {
        if (is_null($path)) {
            $path = "logs";
        }

        $file = storage_path("/$path/$filename");
        if (file_exists($file)) {
            unlink($file);
            return redirect()->back()->with("success", "$file Deleted!");
        }
        return redirect()->back()->with("status", "Oops! Could not Download!");

    }



}
