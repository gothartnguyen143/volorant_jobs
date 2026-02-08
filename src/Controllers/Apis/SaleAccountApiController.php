<?php

declare(strict_types=1);

namespace Controllers\Apis;

use Services\SaleAccountService;
use Services\FileService;
use Utils\DevLogger;

class SaleAccountApiController
{
  private $saleAccountService;
  private $fileService;

  public function __construct(SaleAccountService $saleAccountService, FileService $fileService)
  {
    $this->saleAccountService = $saleAccountService;
    $this->fileService = $fileService;
  }

  public function loadMoreAccounts(): array
  {
    $last_id = isset($_GET['last_id']) ? (int) $_GET['last_id'] : null;
    $status = isset($_GET['status']) ? trim($_GET['status']) : null;
    $search_term = isset($_GET['search_term']) ? trim($_GET['search_term']) : null;
    $letter = isset($_GET['letter']) ? trim($_GET['letter']) : null;

    $accounts = $this->saleAccountService->fetchAccountsWithLoadMore(
      $last_id,
      $status,
      $search_term,
      $letter
    );

    return [
      'accounts' => $accounts,
    ];
  }

  public function addNewAccounts(): array
  {
    if (!isset($_POST['accounts'])) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Thiếu dữ liệu avatar hoặc accounts'
      ];
    }

    // Lấy dữ liệu tài khoản (giả sử client stringify JSON và append vào formData)
    $accountsJson = $_POST['accounts'];
    $accounts = json_decode($accountsJson, true);

    if (!is_array($accounts)) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Dữ liệu accounts không hợp lệ'
      ];
    }

    try {
      $this->saleAccountService->addNewAccounts($accounts);
    } catch (\Throwable $th) {
      // Bắt lỗi vi phạm UNIQUE
      if ($th->getCode() === '23000' && str_contains($th->getMessage(), 'game_code')) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => "Mã game đã tồn tại."
        ];
      }

      // Bắt lỗi vi phạm CHECK constraint trong SQLite
      if ($th instanceof \PDOException && str_contains($th->getMessage(), 'CHECK constraint failed')) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => 'Giá trị đầu vào không hợp lệ.'
        ];
      }

      if ($th instanceof \InvalidArgumentException) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => $th->getMessage()
        ];
      }

      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi hệ thống'
      ];
    }

    // Xử lý file avatar
    $avatarFile = $_FILES['avatar'] ?? null;
    if (count($accounts) == 1 && $avatarFile) {
      // Lấy account record vừa insert
      $latestAccount = $this->saleAccountService->getLatestAccount();

      if (!$latestAccount) {
        http_response_code(500);
        return [
          'success' => false,
          'message' => 'Không thể lấy thông tin tài khoản vừa tạo'
        ];
      }

      $latestAccountId = $latestAccount['id'];
      $imgName = null;
      try {
        $avatarInfo = $this->fileService->saveAvatarImage($avatarFile, $latestAccountId);
        $imgName = $avatarInfo['fileName'];
        $this->saleAccountService->updateAccount($latestAccountId, [
          'avatar' => $imgName,
        ]);
      } catch (\Throwable $th) {
        $this->fileService->deleteAvatarImage($imgName);
        http_response_code(400);
        return [
          'success' => false,
          'message' => 'Tạo ảnh đại diện thất bại'
        ];
      }
    }

    return [
      'success' => true,
    ];
  }

  public function updateAccount(string $accountId): array
  {
    $accountIdInt = (int) $accountId;
    $oldAccount = $this->saleAccountService->findAccountById($accountIdInt);
    if (!$oldAccount) {
      http_response_code(404);
      return [
        'success' => false,
        'message' => 'Tài khoản không tồn tại'
      ];
    }

    if (!isset($_POST['account'])) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Thiếu dữ liệu account'
      ];
    }

    // Lấy dữ liệu tài khoản (giả sử client stringify JSON và append vào formData)
    $accountJson = $_POST['account'];
    $account = json_decode($accountJson, true);

    // Xử lý file avatar (nếu có)
    $avatarFile = $_FILES['avatar'] ?? null;
    $imgName = null;
    if ($avatarFile) {
      $oldAvatar = $oldAccount['avatar'];
      if ($oldAvatar) {
        $this->fileService->deleteAvatarImage($oldAvatar);
      }
      try {
        $avatarInfo = $this->fileService->saveAvatarImage($avatarFile, $accountIdInt);
        $imgName = $avatarInfo['fileName'];
      } catch (\Throwable $th) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => 'Tạo ảnh đại diện thất bại: ' . $th->getMessage()
        ];
      }

      $account['avatar'] = $imgName;
    }

    try {
      $this->saleAccountService->updateAccount($accountIdInt, $account);
    } catch (\Throwable $th) {
      if ($imgName) {
        $this->fileService->deleteAvatarImage($imgName);
      }

      // Bắt lỗi vi phạm CHECK constraint trong SQLite
      if ($th instanceof \PDOException && str_contains($th->getMessage(), 'CHECK constraint failed')) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => 'Giá trị đầu vào không hợp lệ.'
        ];
      }

      if ($th instanceof \InvalidArgumentException) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => $th->getMessage()
        ];
      }

      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi hệ thống'
      ];
    }

    return [
      'success' => true,
    ];
  }

  public function deleteAccount(string $accountId): array
  {
    try {
      $this->saleAccountService->deleteAccount((int) $accountId);
    } catch (\Throwable $th) {
      if ($th instanceof \InvalidArgumentException) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => $th->getMessage()
        ];
      }

      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi hệ thống'
      ];
    }

    return [
      'success' => true,
    ];
  }

  public function fetchSingleAccount(string $accountId): array
  {
    try {
      $refreshedAccount = $this->saleAccountService->refreshAccount((int) $accountId);
    } catch (\Throwable $th) {
      if ($th instanceof \InvalidArgumentException) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => $th->getMessage()
        ];
      }
      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi hệ thống'
      ];
    }

    return [
      'success' => true,
      'account' => $refreshedAccount,
    ];
  }

  public function switchLetterQuickly(string $accountId): array
  {
    try {
      $this->saleAccountService->switchLetterQuickly((int) $accountId);
    } catch (\Throwable $th) {
      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi hệ thống'
      ];
    }

    return [
      'success' => true,
    ];
  }
}
