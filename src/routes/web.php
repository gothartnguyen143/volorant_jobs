<?php

declare(strict_types=1);

use Controllers\HomeController;
use Controllers\AdminController;
use Controllers\AuthController;
use Controllers\SaleController;
use Controllers\RotationController;

use Core\AuthMiddleware;

use Services\JwtService;
use Services\AuthService;
use Services\RulesService;
use Services\UserService;
use Services\SaleAccountService;
use Services\GameAccountService;
use Services\RotationService;

// Initialize services
$jwtService = new JwtService();
$authService = new AuthService($db, $jwtService);
$userService = new UserService($db);
$rulesService = new RulesService($db);
$saleAccountService = new SaleAccountService($db);
$gameAccountService = new GameAccountService($db);
$rotationService = new RotationService($db);

// Initialize controllers
$homeController = new HomeController($userService, $rulesService);
$adminController = new AdminController($userService, $authService, $rulesService, $gameAccountService);
$authController = new AuthController($authService);
$saleController = new SaleController($saleAccountService, $rulesService, $userService);
$rotationController = new RotationController($rotationService);

// Initialize middleware
$authMiddleware = new AuthMiddleware($authService);

// Define routes
$router->get('/', function () use ($homeController) {
	$homeController->showIntroPage();
});

$router->get('/thueacc', function () use ($homeController) {
	$homeController->showHomePage();
});

$router->get('/sale', function () use ($saleController) {
	$saleController->showSalePage();
});

// Public rotation page (accessible from hero section)
$router->get('/rotation', function () use ($rotationController) {
	$rotationController->showPage();
});

// Auth routes
$router->get('/admin/login', function () use ($authController) {
	$authController->showLoginPage();
});

// Protected admin routes
$router->get('/admin/manage-game-accounts', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showManageGameAccountsPage();
});

$router->get('/admin/rotation', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showRotationPage();
});

$router->get('/admin/rotation/history', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showRotationHistoryPage();
});

$router->get('/admin/profile', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showProfilePage();
});

$router->get('/admin/sale-accounts', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showSaleAccountsPage();
});
