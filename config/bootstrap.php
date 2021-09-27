<?php

use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Create App instance
$app = $container->get(App::class);

$files = scandir($container->get('settings')['root']."/src/Routes");
$routes_namespace = "App\\Routes\\";

foreach ($files as $key => $value) {
	$file_name = explode(".", $value);
	if (count($file_name) == 2) {
		$class_name = array_shift($file_name);

		if ($class_name == '' || $class_name == 'BaseRoute') {
			continue;
		}

		$new_class = $routes_namespace.$class_name;
		$new_class = new $new_class($app);
		$new_class->register_route();
	}
}


// (require __DIR__ . '/routes/root.php')($app);
// (require __DIR__ . '/routes/api.php')($app);

// (require __DIR__ . '/routes/pmb.php')($app);
// (require __DIR__ . '/routes/mahasiswa.php')($app);
// (require __DIR__ . '/routes/dosen.php')($app);
// (require __DIR__ . '/routes/tahun_ajaran.php')($app);
// (require __DIR__ . '/routes/paket.php')($app);

// (require __DIR__ . '/routes/test.php')($app);

// Register middleware
(require __DIR__ . '/middleware.php')($app);

return $app;
