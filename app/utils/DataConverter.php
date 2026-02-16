<?php

namespace app\utils;

/**
 * Utility class to convert Flight Collections to clean arrays
 */
class DataConverter
{
    /**
     * Convert a Flight Collection to a clean PHP array
     * Removes duplicate indexed keys that json_encode creates
     */
    public static function toArray($data): mixed
    {
        if ($data === null) {
            return null;
        }

        // If it's already an array, clean it
        if (is_array($data)) {
            return self::removeNumericDuplicates($data);
        }

        // Convert Collection to array using JSON
        $json = json_encode($data);
        $array = json_decode($json, true);

        // Remove duplicate numeric keys
        if (is_array($array)) {
            return self::removeNumericDuplicates($array);
        }

        return $array;
    }

    /**
     * Remove numeric key duplicates from Flight Collection arrays
     * Flight Collections create both numeric and string keys from indexed rows
     */
    private static function removeNumericDuplicates($data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        // Check if this is an array with mixed numeric and string keys (Collection row)
        $keys = array_keys($data);
        $hasNumeric = false;
        $hasString = false;
        
        foreach ($keys as $key) {
            if (is_int($key)) {
                $hasNumeric = true;
            } elseif (is_string($key)) {
                $hasString = true;
            }
        }

        // If we have both numeric and string keys, remove the numeric ones (Collection artifact)
        if ($hasNumeric && $hasString) {
            $cleaned = [];
            foreach ($keys as $key) {
                if (is_string($key)) {
                    $value = $data[$key];
                    // Recursively clean nested structures
                    if (is_array($value)) {
                        $cleaned[$key] = self::removeNumericDuplicates($value);
                    } else {
                        $cleaned[$key] = $value;
                    }
                }
            }
            return $cleaned;
        }

        // If it's a pure numeric array (list), recursively clean each item
        if ($hasNumeric && !$hasString) {
            $cleaned = [];
            foreach ($data as $item) {
                if (is_array($item)) {
                    // Recursively clean, which will remove numeric keys from items
                    $cleaned[] = self::removeNumericDuplicates($item);
                } else {
                    $cleaned[] = $item;
                }
            }
            return $cleaned;
        }

        // If it's a pure string key array, recursively clean values
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::removeNumericDuplicates($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
