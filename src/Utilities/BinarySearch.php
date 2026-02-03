<?php

namespace Vendor\Quotes\Utilities;

class BinarySearch
{
    /**
     * Find a quote by ID using binary search algorithm.
     *
     * @param array<int, array{id: int, quote: string, author: string}> $quotes
     * @param int $id
     * @return array{id: int, quote: string, author: string}|null
     */
    public static function findQuoteById(array $quotes, int $id): ?array
    {
        $left = 0;
        $right = count($quotes) - 1;

        while ($left <= $right) {
            $mid = (int) floor(($left + $right) / 2);
            $currentId = $quotes[$mid]['id'] ?? null;

            if ($currentId === $id) {
                return $quotes[$mid];
            }

            if ($currentId < $id) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return null;
    }

    /**
     * Insert a new quote and maintain sorted order by ID.
     *
     * @param array<int, array{id: int, quote: string, author: string}> $quotes
     * @param array{id: int, quote: string, author: string} $newQuote
     * @return array<int, array{id: int, quote: string, author: string}>
     */
    public static function insertAndSort(array $quotes, array $newQuote): array
    {
        $quotes[] = $newQuote;

        usort($quotes, function (array $a, array $b): int {
            return $a['id'] <=> $b['id'];
        });

        return $quotes;
    }

    /**
     * Find if ID exists using binary search (optimized for ID arrays).
     *
     * @param array<int, int> $ids Sorted array of IDs
     * @param int $id ID to find
     * @return bool
     */
    public static function idExists(array $ids, int $id): bool
    {
        $left = 0;
        $right = count($ids) - 1;

        while ($left <= $right) {
            $mid = (int) floor(($left + $right) / 2);
            $currentId = $ids[$mid];

            if ($currentId === $id) {
                return true;
            }

            if ($currentId < $id) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return false;
    }
}
