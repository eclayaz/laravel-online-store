<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
//        $file = public_path('files/test.csv');
//        var_dump($file);

        $csvFileName = "product.csv";
        $csvFile = storage_path('app/csv/' . $csvFileName);
        $dd = $this->readCSV($csvFile,array('delimiter' => ','));

        return $dd;
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }
}
