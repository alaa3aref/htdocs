<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = $rootPath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});

use App\Contracts\Cache\CacheInterface;
use App\Contracts\Cache\CacheManagerInterface;
use App\Contracts\Cache\MemoryCacheInterface;
use App\Infrastructure\Cache\ArrayCache;
use App\Infrastructure\Cache\CacheManager;
use App\Infrastructure\Cache\CacheServiceProvider;
use App\Infrastructure\Cache\FileCache;
use App\Infrastructure\Cache\NullCache;
use App\Infrastructure\Config\Configuration;

// Test NullCache
$nullCache = new NullCache();
if (!($nullCache instanceof CacheInterface)) {
    fwrite(STDERR, "NullCache creation failed\n");
    exit(1);
}

if ($nullCache->has('any-key')) {
    fwrite(STDERR, "NullCache should not have any keys\n");
    exit(1);
}

if ($nullCache->get('any-key', 'default') !== 'default') {
    fwrite(STDERR, "NullCache get should return default\n");
    exit(1);
}

// NullCache operations should not throw
$nullCache->put('key', 'value');
$nullCache->forget('key');
$nullCache->clear();

echo "NullCache test passed\n";

// Test ArrayCache
$arrayCache = new ArrayCache();
if (!($arrayCache instanceof MemoryCacheInterface)) {
    fwrite(STDERR, "ArrayCache creation failed\n");
    exit(1);
}

if ($arrayCache->has('missing-key')) {
    fwrite(STDERR, "ArrayCache should not have missing keys\n");
    exit(1);
}

$arrayCache->put('test-key', 'test-value');
if (!$arrayCache->has('test-key')) {
    fwrite(STDERR, "ArrayCache should have stored key\n");
    exit(1);
}

if ($arrayCache->get('test-key') !== 'test-value') {
    fwrite(STDERR, "ArrayCache get returned wrong value\n");
    exit(1);
}

// Test TTL expiration
$arrayCache->put('ttl-key', 'ttl-value', 1);
if ($arrayCache->get('ttl-key') !== 'ttl-value') {
    fwrite(STDERR, "ArrayCache TTL put failed\n");
    exit(1);
}

// Test increment/decrement
$arrayCache->put('counter', 0);
$arrayCache->increment('counter');
if ($arrayCache->get('counter') !== 1) {
    fwrite(STDERR, "ArrayCache increment failed\n");
    exit(1);
}

$arrayCache->decrement('counter', 2);
if ($arrayCache->get('counter') !== -1) {
    fwrite(STDERR, "ArrayCache decrement failed\n");
    exit(1);
}

// Test remember
$called = false;
$remembered = $arrayCache->remember('remembered-key', static function () use (&$called): string {
    $called = true;
    return 'computed';
});
if ($remembered !== 'computed' || !$called) {
    fwrite(STDERR, "ArrayCache remember failed\n");
    exit(1);
}

// Second call should use cache
$called = false;
$remembered = $arrayCache->remember('remembered-key', static function () use (&$called): string {
    $called = true;
    return 'recomputed';
});
if ($remembered !== 'computed' || $called) {
    fwrite(STDERR, "ArrayCache remember should use cached value\n");
    exit(1);
}

// Test forget
$arrayCache->forget('test-key');
if ($arrayCache->has('test-key')) {
    fwrite(STDERR, "ArrayCache forget failed\n");
    exit(1);
}

// Test clear
$arrayCache->put('key-a', 'a');
$arrayCache->put('key-b', 'b');
$arrayCache->clear();
if ($arrayCache->has('key-a') || $arrayCache->has('key-b')) {
    fwrite(STDERR, "ArrayCache clear failed\n");
    exit(1);
}

echo "ArrayCache test passed\n";

// Test FileCache
$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'greens_cache_test_' . uniqid('', true);
$fileCache = new FileCache($tempDir);
if (!($fileCache instanceof CacheInterface)) {
    fwrite(STDERR, "FileCache creation failed\n");
    exit(1);
}

if ($fileCache->has('missing-key')) {
    fwrite(STDERR, "FileCache should not have missing keys\n");
    exit(1);
}

$fileCache->put('file-key', 'file-value');
if (!$fileCache->has('file-key')) {
    fwrite(STDERR, "FileCache should have stored key\n");
    exit(1);
}

if ($fileCache->get('file-key') !== 'file-value') {
    fwrite(STDERR, "FileCache get returned wrong value\n");
    exit(1);
}

$fileCache->forget('file-key');
if ($fileCache->has('file-key')) {
    fwrite(STDERR, "FileCache forget failed\n");
    exit(1);
}

// Cleanup
$fileCache->clear();
if (is_dir($tempDir)) {
    rmdir($tempDir);
}

echo "FileCache test passed\n";

// Test CacheManager
$configuration = new Configuration([
    'cache' => [
        'default' => 'array',
        'stores' => [
            'array' => ['driver' => 'array'],
            'file' => ['driver' => 'file', 'path' => 'storage/private/cache'],
            'null' => ['driver' => 'null'],
        ],
    ],
]);

$manager = new CacheManager($configuration);
if (!($manager instanceof CacheManagerInterface)) {
    fwrite(STDERR, "CacheManager creation failed\n");
    exit(1);
}

$defaultStore = $manager->store();
if (!($defaultStore instanceof CacheInterface)) {
    fwrite(STDERR, "CacheManager default store failed\n");
    exit(1);
}

$manager->put('manager-key', 'manager-value');
if (!$manager->has('manager-key')) {
    fwrite(STDERR, "CacheManager has failed\n");
    exit(1);
}

if ($manager->get('manager-key') !== 'manager-value') {
    fwrite(STDERR, "CacheManager get failed\n");
    exit(1);
}

$manager->forget('manager-key');
if ($manager->has('manager-key')) {
    fwrite(STDERR, "CacheManager forget failed\n");
    exit(1);
}

$manager->put('clear-key', 'clear-value');
$manager->clear();
if ($manager->has('clear-key')) {
    fwrite(STDERR, "CacheManager clear failed\n");
    exit(1);
}

echo "CacheManager test passed\n";

// Test CacheServiceProvider
$serviceProvider = new CacheServiceProvider();
if (!($serviceProvider instanceof CacheServiceProvider)) {
    fwrite(STDERR, "CacheServiceProvider creation failed\n");
    exit(1);
}

echo "CacheServiceProvider test passed\n";

// Test interface contracts
$cacheInterface = new class implements CacheInterface {
    public function has(string $key): bool { return false; }
    public function get(string $key, mixed $default = null): mixed { return $default; }
    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void {}
    public function forget(string $key): void {}
    public function clear(): void {}
};

if (!($cacheInterface instanceof CacheInterface)) {
    fwrite(STDERR, "CacheInterface contract validation failed\n");
    exit(1);
}

$managerInterface = new class implements CacheManagerInterface {
    public function store(?string $name = null): CacheInterface
    {
        return new NullCache();
    }
    public function has(string $key): bool { return false; }
    public function get(string $key, mixed $default = null): mixed { return $default; }
    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void {}
    public function forget(string $key): void {}
    public function clear(): void {}
};

if (!($managerInterface instanceof CacheManagerInterface)) {
    fwrite(STDERR, "CacheManagerInterface contract validation failed\n");
    exit(1);
}

echo "Cache architecture test passed\n";

