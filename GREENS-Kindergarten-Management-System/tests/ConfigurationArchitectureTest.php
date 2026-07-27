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

use App\Contracts\Config\ConfigInterface;
use App\Contracts\Config\ConfigurationCacheInterface;
use App\Http\Routing\RouteCollection;
use App\Infrastructure\Config\BootstrapConfigLoader;
use App\Infrastructure\Config\ConfigCollection;
use App\Infrastructure\Config\ConfigLoader;
use App\Infrastructure\Config\ConfigManager;
use App\Infrastructure\Config\ConfigRepository;
use App\Infrastructure\Config\ConfigurationServiceProvider;
use App\Infrastructure\Config\DotEnvParser;
use App\Infrastructure\Config\EnvironmentLoader;
use App\Infrastructure\Config\EnvironmentValidator;
use App\Infrastructure\Config\ConfigurationValidator;
use App\Infrastructure\Config\NullConfigurationCache;
use App\Infrastructure\Config\ConfigurationException;

$rootPath = dirname(__DIR__);
$configDirectory = $rootPath . '/config';

$loader = new ConfigLoader($configDirectory);
$config = $loader->load();

if (!($config instanceof ConfigCollection)) {
    fwrite(STDERR, "Configuration loading failed\n");
    exit(1);
}

$manager = new ConfigManager(new ConfigRepository($config));
if (!($manager instanceof ConfigManager)) {
    fwrite(STDERR, "Configuration manager creation failed\n");
    exit(1);
}

if ($manager->get('app.name') !== 'GREENS Kindergarten Management System') {
    fwrite(STDERR, "Configuration value retrieval failed\n");
    exit(1);
}

if ($manager->get('database.default') !== 'mysql') {
    fwrite(STDERR, "Nested configuration retrieval failed\n");
    exit(1);
}

if ($manager->get('database.connections.mysql.host') !== '127.0.0.1') {
    fwrite(STDERR, "Dot notation configuration retrieval failed\n");
    exit(1);
}

$envLoader = new EnvironmentLoader($rootPath . '/.env.example');
$env = $envLoader->load();
if (!($env instanceof ConfigCollection)) {
    fwrite(STDERR, "Environment loading failed\n");
    exit(1);
}

$validator = new ConfigurationValidator();
if (!($validator instanceof ConfigurationValidator)) {
    fwrite(STDERR, "Configuration validator creation failed\n");
    exit(1);
}

$environmentValidator = new EnvironmentValidator();
if (!($environmentValidator instanceof EnvironmentValidator)) {
    fwrite(STDERR, "Environment validator creation failed\n");
    exit(1);
}

$cache = new NullConfigurationCache();
if (!($cache instanceof ConfigurationCacheInterface)) {
    fwrite(STDERR, "Configuration cache creation failed\n");
    exit(1);
}

$bootstrapLoader = new BootstrapConfigLoader($rootPath, $loader);
if (!($bootstrapLoader instanceof BootstrapConfigLoader)) {
    fwrite(STDERR, "Bootstrap config loader creation failed\n");
    exit(1);
}

$serviceProvider = new ConfigurationServiceProvider();
if (!($serviceProvider instanceof ConfigurationServiceProvider)) {
    fwrite(STDERR, "Configuration service provider creation failed\n");
    exit(1);
}

$compiled = $serviceProvider->createManager($rootPath);
if (!($compiled instanceof ConfigManager)) {
    fwrite(STDERR, "Configuration service provider manager creation failed\n");
    exit(1);
}

$interfaceInstance = new class implements ConfigInterface {
    public function get(string $key, mixed $default = null): mixed { return $default; }
    public function has(string $key): bool { return false; }
    public function all(): array { return []; }
};
if (!($interfaceInstance instanceof ConfigInterface)) {
    fwrite(STDERR, "Config interface validation failed\n");
    exit(1);
}

$exception = new ConfigurationException('configuration test');
if (!($exception instanceof ConfigurationException)) {
    fwrite(STDERR, "Configuration exception creation failed\n");
    exit(1);
}

echo "Configuration architecture test passed\n";
