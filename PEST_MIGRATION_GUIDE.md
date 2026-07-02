# PHPUnit to Pest v4 Migration Guide

## Overview

This guide helps you migrate your existing PHPUnit tests to Pest v4.

## Installation Complete

✅ Pest v4 has been installed with all dependencies
✅ Configuration files have been created
✅ .gitignore has been updated

## Manual Migration Steps

### 1. Convert Test Classes to Functions

**Before (PHPUnit):**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_example(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
```

**After (Pest v4):**
```php
<?php

test('example', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
```

### 2. Convert Traits Usage

**Before:**
```php
class ExampleTest extends TestCase
{
    use RefreshDatabase;
}
```

**After:**
```php
uses(RefreshDatabase::class);
```

### 3. Convert Setup Methods

**Before:**
```php
protected function setUp(): void
{
    parent::setUp();
    // setup code
}
```

**After:**
```php
beforeEach(function () {
    // setup code
});
```

### 4. Update Assertions (Optional)

You can optionally use Pest's expectation API:

**Before:**
```php
$this->assertTrue($value);
$this->assertEquals('expected', $actual);
```

**After (Optional):**
```php
expect($value)->toBeTrue();
expect($actual)->toBe('expected');
```

## Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run specific test suite
./vendor/bin/pest --testsuite=Feature

# Run with coverage
./vendor/bin/pest --coverage
```

## Resources

- [Pest Documentation](https://pestphp.com/docs)
- [Migration Guide](https://pestphp.com/docs/migrating-from-phpunit)
- [Expectations API](https://pestphp.com/docs/expectations)

## Need Help?

Run `./vendor/bin/pest --help` for available options and commands.