<?php


namespace App\Http\Helpers;


use Exception;
use SplFileObject;

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
    public static function getAll(string $file, array $keys, int $limit = 100, int $offset = 0) : array
    {
        $items = [];
        $fileHandle = new SplFileObject($file, 'r');

        $i = 0;
        $fileHandle->seek($offset);
        while (!$fileHandle->eof()) {
            $items[] = self::fillDataDataWithKeys($fileHandle->fgetcsv(), $keys);

            if (++$i >= $limit) {
                break;
            }
        }
        $fileHandle = null;
        return $items;
    }

    /**
     * @param array|string[] $data
     * @param array|string[] $keys
     * @return array|string[]
     */
    private static function fillDataDataWithKeys(array $data, array $keys) : array
    {
        $withKeys = [];
        foreach ($data as $key => $item) {
            if(!array_key_exists($key, $keys)) {
                continue;
            }
            $withKeys[$keys[$key]] = trim($item);
        }
        return $withKeys;
    }

    /**
     * @param string $file
     * @param array|string[] $search
     * @param array|string[] $keys
     * @return array|string[]
     *
     * @throws Exception
     */
    public static function findRecord(string $file, array $search, array $keys): array
    {
        $fileHandle = fopen($file, "r");
        if ($fileHandle === FALSE) {
            throw new Exception('Error opening ' . $file, 500);
        }

        $searchKeyword = reset($search);
        $searchIndex = key($search);
        fseek($fileHandle, 0);
        while (!feof($fileHandle)) {
            $data = fgetcsv($fileHandle);
            if($searchKeyword === $data[$searchIndex]) {
                return self::fillDataDataWithKeys($data, $keys);
            }
        }
        fclose($fileHandle);
        throw new Exception('Additional data not found', 400);
    }
}