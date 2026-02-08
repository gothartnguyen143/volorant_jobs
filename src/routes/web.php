<?php

declare(strict_types=1);

use Controllers\HomeController;
use Controllers\AdminController;
use Controllers\AuthController;
use Controllers\SaleController;

use Core\AuthMiddleware;

use Services\JwtService;
use Services\AuthService;
use Services\RulesService;
use Services\UserService;
use Services\SaleAccountService;
use Services\GameAccountService;

// Initialize services
$jwtService = new JwtService();
$authService = new AuthService($db, $jwtService);
$userService = new UserService($db);
$rulesService = new RulesService($db);
$saleAccountService = new SaleAccountService($db);
$gameAccountService = new GameAccountService($db);

// Initialize controllers
$homeController = new HomeController($userService, $rulesService);
$adminController = new AdminController($userService, $authService, $rulesService, $gameAccountService);
$authController = new AuthController($authService);
$saleController = new SaleController($saleAccountService, $rulesService, $userService);

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

// Auth routes
$router->get('/admin/login', function () use ($authController) {
	$authController->showLoginPage();
});

// Protected admin routes
$router->get('/admin/manage-game-accounts', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showManageGameAccountsPage();
});

$router->get('/admin/profile', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showProfilePage();
});

$router->get('/admin/sale-accounts', function () use ($adminController, $authMiddleware) {
	$authMiddleware->handle();
	$adminController->showSaleAccountsPage();
});
