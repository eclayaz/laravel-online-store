<?php


namespace App\Http\Helpers;


use Exception;

class CvsHandler
{
    /**
     * @param string $file
     * @param int $limit
     * @param int $offset
     * @param string[] $keys
     * @return array | string[]
     *
     * @throws Exception
     */
    public static function read($file, $keys, $limit = 100, $offset = 0)
    {
        $items = [];
        $fileHandle = fopen($file, "r");
        if ($fileHandle === FALSE) {
            throw new Exception('Error opening ' . $file, 500);
        }

        $i = 0;
        while (!feof($fileHandle)) {
            fseek($fileHandle, $offset);
            $items[] = self::fillDataDataWithKeys(fgetcsv($fileHandle), $keys);;

            if (++$i >= $limit) {
                break;
            }
        }
        fclose($fileHandle);
        return $items;
    }

    /**
     * @param string[] $data
     * @param string[] $keys
     * @return array | string[]
     */
    private static function fillDataDataWithKeys($data, $keys)
    {
        $withKeys = [];
        foreach ($data as $key => $item) {
            $withKeys[$keys[$key]] = $item;
        }
        return $withKeys;
    }
}