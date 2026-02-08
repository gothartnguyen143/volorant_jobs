<?php

declare(strict_types=1);

use Controllers\Apis\GameAccountApiController;
use Services\GameAccountService;
use Services\FileService;
use Services\UserService;
use Controllers\Apis\AdminApiController;
use Controllers\Apis\AuthApiController;
use Services\AuthService;
use Services\JwtService;
use Core\AuthMiddleware;
use Services\RulesService;
use Controllers\Apis\SaleAccountApiController;
use Services\SaleAccountService;

// Initialize API controller
$gameAccountApiController = new GameAccountApiController(new GameAccountService($db), new FileService());
$adminApiController = new AdminApiController(new UserService($db), new RulesService($db));
$authApiController = new AuthApiController(new AuthService($db, new JwtService()));
$authService = new AuthService($db, new JwtService());
$saleAccountApiController = new SaleAccountApiController(new SaleAccountService($db), new FileService());

// Initialize middleware
$authMiddleware = new AuthMiddleware($authService);

// API Routes
$apiRouter->get('/api/v1/game-accounts/load-more', function () use ($gameAccountApiController) {
  return $gameAccountApiController->loadMoreAccounts();
});

$apiRouter->get('/api/v1/admin/game-accounts/load-more', function () use ($gameAccountApiController) {
  return $gameAccountApiController->loadMoreAccountsForAdmin();
});

$apiRouter->get('/api/v1/game-accounts/rank-types', function () use ($gameAccountApiController) {
  return $gameAccountApiController->getAccountRankTypes();
});

$apiRouter->get('/api/v1/game-accounts/statuses', function () use ($gameAccountApiController) {
  return $gameAccountApiController->getAccountStatuses();
});

$apiRouter->get('/api/v1/game-accounts/device-types', function () use ($gameAccountApiController) {
  return $gameAccountApiController->getDeviceTypes();
});

$apiRouter->post('/api/v1/game-accounts/add-new', function () use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->addNewAccounts();
});

$apiRouter->post('/api/v1/game-accounts/update/{accountId}', function ($accountId) use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->updateAccount($accountId);
});

$apiRouter->delete('/api/v1/game-accounts/delete/{accountId}', function ($accountId) use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->deleteAccount($accountId);
});

$apiRouter->post('/api/v1/game-accounts/switch-status/{accountId}', function ($accountId) use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->switchAccountStatus($accountId);
});

$apiRouter->post('/api/v1/admin/update-profile', function () use ($adminApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $adminApiController->updateAdminProfile();
});

$apiRouter->post('/api/v1/admin/update-web-ui', function () use ($adminApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $adminApiController->updateWebUI();
});

$apiRouter->post('/api/v1/game-accounts/update-rent-time', function () use ($gameAccountApiController, $authMiddleware) {
  return $gameAccountApiController->updateAccountRentTime();
});

$apiRouter->get('/api/v1/game-accounts/fetch-single-account/{accountId}', function ($accountId) use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->fetchSingleAccount($accountId);
});

$apiRouter->put('/api/v1/game-accounts/switch-device-type/{accountId}', function ($accountId) use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->switchDeviceType($accountId);
});

$apiRouter->put('/api/v1/game-accounts/cancel-rent/{accountId}', function ($accountId) use ($gameAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $gameAccountApiController->cancelRent($accountId);
});

$apiRouter->post('/api/v1/auth/login', function () use ($authApiController) {
  return $authApiController->login();
});

$apiRouter->get('/api/v1/auth/logout', function () use ($authApiController) {
  return $authApiController->logout();
});

// Sale Account API Routes
$apiRouter->get('/api/v1/sale-accounts/load-more', function () use ($saleAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $saleAccountApiController->loadMoreAccounts();
});

$apiRouter->post('/api/v1/sale-accounts/add-new', function () use ($saleAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $saleAccountApiController->addNewAccounts();
});

$apiRouter->post('/api/v1/sale-accounts/update/{accountId}', function ($accountId) use ($saleAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $saleAccountApiController->updateAccount($accountId);
});

$apiRouter->delete('/api/v1/sale-accounts/delete/{accountId}', function ($accountId) use ($saleAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $saleAccountApiController->deleteAccount($accountId);
});

$apiRouter->get('/api/v1/sale-accounts/fetch-single-account/{accountId}', function ($accountId) use ($saleAccountApiController, $authMiddleware) {
  if (!$authMiddleware->handleApi()) {
    http_response_code(401);
    return [
      'success' => false,
      'message' => 'Unauthorized'
    ];
  }
  return $saleAccountApiController->fetchSingleAccount($accountId);
});
