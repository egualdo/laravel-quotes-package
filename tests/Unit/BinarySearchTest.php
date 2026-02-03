<?php

use Vendor\Quotes\Utilities\BinarySearch;

test('binary search finds quote by id in sorted array', function () {
    $quotes = [
        ['id' => 1, 'quote' => 'Quote 1', 'author' => 'Author 1'],
        ['id' => 3, 'quote' => 'Quote 3', 'author' => 'Author 3'],
        ['id' => 5, 'quote' => 'Quote 5', 'author' => 'Author 5'],
        ['id' => 7, 'quote' => 'Quote 7', 'author' => 'Author 7'],
        ['id' => 9, 'quote' => 'Quote 9', 'author' => 'Author 9'],
    ];

    $result = BinarySearch::findQuoteById($quotes, 5);

    expect($result)->toBe(['id' => 5, 'quote' => 'Quote 5', 'author' => 'Author 5']);
});

test('binary search returns null when quote not found', function () {
    $quotes = [
        ['id' => 2, 'quote' => 'Quote 2'],
        ['id' => 4, 'quote' => 'Quote 4'],
        ['id' => 6, 'quote' => 'Quote 6'],
    ];

    $result = BinarySearch::findQuoteById($quotes, 5);

    expect($result)->toBeNull();
});

test('binary search works with empty array', function () {
    $result = BinarySearch::findQuoteById([], 1);

    expect($result)->toBeNull();
});

test('binary search finds first element', function () {
    $quotes = [
        ['id' => 1, 'quote' => 'First'],
        ['id' => 2, 'quote' => 'Second'],
        ['id' => 3, 'quote' => 'Third'],
    ];

    $result = BinarySearch::findQuoteById($quotes, 1);

    expect($result)->toBe(['id' => 1, 'quote' => 'First']);
});

test('binary search finds last element', function () {
    $quotes = [
        ['id' => 10, 'quote' => 'Quote 10'],
        ['id' => 20, 'quote' => 'Quote 20'],
        ['id' => 30, 'quote' => 'Quote 30'],
    ];

    $result = BinarySearch::findQuoteById($quotes, 30);

    expect($result)->toBe(['id' => 30, 'quote' => 'Quote 30']);
});

test('insert and sort maintains ascending order by id', function () {
    $quotes = [
        ['id' => 1, 'quote' => 'Quote 1'],
        ['id' => 4, 'quote' => 'Quote 4'],
    ];

    $newQuote = ['id' => 2, 'quote' => 'Quote 2'];

    $result = BinarySearch::insertAndSort($quotes, $newQuote);

    expect($result)->toBe([
        ['id' => 1, 'quote' => 'Quote 1'],
        ['id' => 2, 'quote' => 'Quote 2'],
        ['id' => 4, 'quote' => 'Quote 4'],
    ]);
});

test('insert and sort works with empty array', function () {
    $newQuote = ['id' => 1, 'quote' => 'First quote'];

    $result = BinarySearch::insertAndSort([], $newQuote);

    expect($result)->toBe([$newQuote]);
});
