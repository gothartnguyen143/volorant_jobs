<?php

declare(strict_types=1);

namespace Services;

use PDO;

class RotationService
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  /**
   * Đảm bảo tồn tại player với identifier (email hoặc sđt).
   * Nếu chưa có thì tạo mới với total_turns = 1.
   */
  private function ensurePlayer(string $identifier): array
  {
    $stmt = $this->db->prepare("SELECT * FROM lucky_spin_players WHERE identifier = :identifier LIMIT 1");
    $stmt->execute(['identifier' => $identifier]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($player) {
      return $player;
    }

    $defaultTurns = $this->getDefaultTurns();
    $insert = $this->db->prepare("INSERT INTO lucky_spin_players (identifier, total_turns, used_turns, created_at) VALUES (:identifier, :total_turns, 0, datetime('now'))");
    $insert->execute(['identifier' => $identifier, 'total_turns' => $defaultTurns]);

    $id = (int)$this->db->lastInsertId();
    $stmt->execute(['identifier' => $identifier]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Lấy danh sách giải đang active và có quantity != 0 (0 nghĩa đã hết).
   */
  public function getAvailablePrizes(): array
  {
    $stmt = $this->db->prepare("SELECT * FROM lucky_spin_prizes WHERE is_active = 1 AND (quantity IS NULL OR quantity <> 0) ORDER BY id ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Chọn giải theo trọng số 'probability'.
   * - Nếu tổng trọng số = 0 thì trả về null (không có giải)
   * - Xử lý quantity: nếu quantity > 0 sẽ giảm 1 khi trúng
   */
  private function pickPrizeByWeight(array $prizes): ?array
  {
    $total = 0;
    foreach ($prizes as $p) {
      $total += (float)$p['probability'];
    }

    if ($total <= 0) {
      return null;
    }

    // Sử dụng số ngẫu nhiên từ 0..1 * total để hỗ trợ probability là số thực
    $r = mt_rand() / mt_getrandmax() * $total;
    $acc = 0.0;
    foreach ($prizes as $p) {
      $acc += (float)$p['probability'];
      if ($r <= $acc) {
        return $p;
      }
    }

    // Fallback: trả về giải cuối cùng
    return end($prizes) ?: null;
  }

  /**
   * Thực hiện một lượt quay cho `identifier`.
   * Trả về mảng kết quả chi tiết (prize info, index) hoặc throw Exception nếu không thể quay.
   * index: vị trí của prize trong danh sách active prizes (0-based)
   */
  public function performSpin(string $identifier): array
  {
    try {
      $this->db->beginTransaction();

      $player = $this->ensurePlayer($identifier);

      // Kiểm tra lượt còn hay hết

      // $used = (int)($player['used_turns'] ?? 0);
      // $total = (int)($player['total_turns'] ?? 0);
      // if ($used >= $total) {
      //   throw new \RuntimeException('No turns left', 400);
      // }

      $prizes = $this->getAvailablePrizes();
      $prize = $this->pickPrizeByWeight($prizes);

      // Tìm index của prize trong danh sách
      $prizeIndex = null;
      if ($prize) {
        foreach ($prizes as $i => $p) {
          if ($p['id'] == $prize['id']) {
            $prizeIndex = $i;
            break;
          }
        }
      }

      // Nếu không có prize (tổng trọng số = 0), vẫn tính là đã dùng 1 lượt nhưng prize null
      $prizeSnapshot = null;
      $prizeId = null;
      if ($prize) {
        $prizeSnapshot = $prize['name'];
        $prizeId = isset($prize['id']) ? (int)$prize['id'] : null;

        // Nếu quantity > 0 thì giảm 1. Nếu quantity = -1 hoặc NULL -> vô hạn, không giảm.
        if (isset($prize['quantity']) && $prize['quantity'] !== '' && $prize['quantity'] !== null) {
          $qty = (int)$prize['quantity'];
          if ($qty > 0) {
            $upd = $this->db->prepare('UPDATE lucky_spin_prizes SET quantity = quantity - 1 WHERE id = :id');
            $upd->execute(['id' => $prizeId]);
          }
        }
      }

      // Ghi lịch sử
      $insertHist = $this->db->prepare('INSERT INTO lucky_spin_history (player_id, prize_id, created_at) VALUES (:player_id, :prize_id, datetime("now"))');
      $insertHist->execute([
        'player_id' => $player['id'],
        'prize_id' => $prizeId
      ]);

      // Cập nhật used_turns và last_spin_time
      $updatePlayer = $this->db->prepare('UPDATE lucky_spin_players SET used_turns = used_turns + 1, last_spin_time = datetime("now") WHERE id = :id');
      $updatePlayer->execute(['id' => $player['id']]);

      $this->db->commit();

      return [
        'success' => true,
        'prize' => $prize ? [
          'id' => $prizeId,
          'name' => $prize['name'],
        ] : null,
        'index' => $prizeIndex
      ];
    } catch (\Throwable $e) {
      if ($this->db->inTransaction()) {
        $this->db->rollBack();
      }
      throw $e;
    }
  }

  /**
   * List prizes. If $onlyActive is true, return only active prizes.
   */
  public function listPrizes(bool $onlyActive = false): array
  {
    if ($onlyActive) {
      $stmt = $this->db->prepare('SELECT * FROM lucky_spin_prizes WHERE is_active = 1 ORDER BY id ASC');
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $stmt = $this->db->prepare('SELECT * FROM lucky_spin_prizes ORDER BY id ASC');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * List spin history joined with player identifier and prize name.
   * Returns array of records with fields: id, player_id, prize_id, created_at, player_identifier, prize_name
   */
  public function listHistory(): array
  {
    $sql = 'SELECT h.id, h.player_id, h.prize_id, h.created_at, '
         . 'p.identifier AS player_identifier, pr.name AS prize_name '
         . 'FROM lucky_spin_history h '
         . 'LEFT JOIN lucky_spin_players p ON h.player_id = p.id '
         . 'LEFT JOIN lucky_spin_prizes pr ON h.prize_id = pr.id '
         . 'ORDER BY h.created_at DESC';
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get single prize by id
   */
  public function getPrize(int $id): ?array
  {
    $stmt = $this->db->prepare('SELECT * FROM lucky_spin_prizes WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res ?: null;
  }

  /**
   * Create a prize. $data may contain keys: name,probability,quantity,is_active
   * Returns inserted id.
   */
  public function createPrize(array $data): int
  {
    $stmt = $this->db->prepare('INSERT INTO lucky_spin_prizes (name, probability, quantity, is_active) VALUES (:name, :probability, :quantity, :is_active)');
    $stmt->execute([
      'name' => $data['name'] ?? null,
      'probability' => isset($data['probability']) ? (float)$data['probability'] : 0,
      'quantity' => isset($data['quantity']) ? (int)$data['quantity'] : -1,
      'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
    ]);

    return (int)$this->db->lastInsertId();
  }

  /**
   * Update prize by id. $data same as createPrize.
   */
  public function updatePrize(int $id, array $data): void
  {
    $fields = [];
    $params = [];

    $allowed = ['name','probability','quantity','is_active'];
    foreach ($allowed as $k) {
      if (array_key_exists($k, $data)) {
        $fields[] = "$k = :$k";
        if ($k === 'probability') $params[$k] = (float)$data[$k];
        elseif ($k === 'quantity' || $k === 'is_active') $params[$k] = (int)$data[$k];
        else $params[$k] = $data[$k];
      }
    }

    if (empty($fields)) return;

    $params['id'] = $id;
    $sql = 'UPDATE lucky_spin_prizes SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
  }

  /**
   * Giảm quantity của một prize cụ thể (dùng khi cần giảm thủ công hoặc sau khi quay).
   * Trả về true nếu giảm thành công, false nếu quantity <= 0 hoặc không tồn tại.
   */
  public function decreasePrizeQuantity(int $prizeId): bool
  {
    $stmt = $this->db->prepare('SELECT quantity FROM lucky_spin_prizes WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $prizeId]);
    $prize = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prize || $prize['quantity'] <= 0) {
      return false; // Không thể giảm nếu quantity <= 0 hoặc không tồn tại
    }

    $upd = $this->db->prepare('UPDATE lucky_spin_prizes SET quantity = quantity - 1 WHERE id = :id');
    $upd->execute(['id' => $prizeId]);
    return true;
  }

  /**
   * Lấy thông tin lượt quay của player.
   * Trả về ['total_turns', 'used_turns', 'remaining'] hoặc null nếu player không tồn tại.
   */
  public function getPlayerTurns(string $identifier): ?array
  {
    $stmt = $this->db->prepare("SELECT total_turns, used_turns FROM lucky_spin_players WHERE identifier = :identifier LIMIT 1");
    $stmt->execute(['identifier' => $identifier]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$player) {
      return null;
    }

    $total = (int)($player['total_turns'] ?? 0);
    $used = (int)($player['used_turns'] ?? 0);
    $remaining = max(0, $total - $used);

    return [
      'total_turns' => $total,
      'used_turns' => $used,
      'remaining' => $remaining
    ];
  }

  /**
   * Cập nhật lượt quay cho tất cả players
   */
  public function updateAllPlayersTurns(int $totalTurns): void
  {
    $stmt = $this->db->prepare("UPDATE lucky_spin_players SET total_turns = :total_turns");
    $stmt->execute(['total_turns' => $totalTurns]);
    
    // Cập nhật default cho players mới
    $this->setDefaultTurns($totalTurns);
  }

  /**
   * Lấy giá trị mặc định lượt quay cho player mới từ file config
   */
  public function getDefaultTurns(): int
  {
    $configFile = __DIR__ . '/../../config/default_turns.php';
    if (file_exists($configFile)) {
      return include $configFile;
    }
    return 1; // fallback
  }

  /**
   * Cập nhật giá trị mặc định lượt quay cho player mới vào file config
   */
  public function setDefaultTurns(int $turns): void
  {
    $configFile = __DIR__ . '/../../config/default_turns.php';
    $content = "<?php\n// Default turns for new players\nreturn {$turns};";
    file_put_contents($configFile, $content);
  }
}
