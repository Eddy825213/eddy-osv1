<?php
  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
  header('Access-Control-Allow-Headers: Content-Type');

  // Debug log
  file_put_contents('debug.log', "Request received at " . date('Y-m-d H:i:s') . " from " . $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);

  // Thông tin kết nối MySQL từ InfinityFree
  $host = 'sql203.infinityfree.com';
  $dbname = 'if0_38617759_eddy';
  $username = 'if0_38617759';
  $password = 'mIwIfL4cZSy';

  // Mật khẩu admin
  $adminPassword = 'eddy';

  // Kết nối MySQL
  try {
      $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->exec("SET CHARACTER SET utf8mb4");
      file_put_contents('debug.log', "Database connected successfully\n", FILE_APPEND);
  } catch (PDOException $e) {
      file_put_contents('error.log', "Database connection failed: " . $e->getMessage() . "\n", FILE_APPEND);
      http_response_code(500);
      echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
      exit;
  }

  // Hàm tạo UUID
  function generateUUID(): string {
      return sprintf(
          '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0xffff)
      );
  }

  // Hàm trả về lỗi
  function sendError($message, $code = 400): never {
      file_put_contents('error.log', "[$code] $message\n", FILE_APPEND);
      http_response_code($code);
      echo json_encode(['error' => $message]);
      exit;
  }

  // Khởi tạo session
  session_start();

  // Xử lý yêu cầu
  $method = $_SERVER['REQUEST_METHOD'];
  file_put_contents('debug.log', "Method: $method\n", FILE_APPEND);

  if ($method === 'POST') {
      file_put_contents('debug.log', "Handling POST request\n", FILE_APPEND);

      // Kiểm tra tần suất gửi
      if (isset($_SESSION['last_comment_time']) && (time() - $_SESSION['last_comment_time']) < 30) {
          sendError('Please wait 30 seconds before posting again!');
      }

      // Lấy dữ liệu
      $inputRaw = file_get_contents('php://input');
      file_put_contents('debug.log', "Raw Input: $inputRaw\n", FILE_APPEND);
      $input = json_decode($inputRaw, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
          sendError('Invalid JSON data: ' . json_last_error_msg());
      }

      $name = isset($input['name']) ? trim($input['name']) : '';
      $content = isset($input['content']) ? trim($input['content']) : '';
      file_put_contents('debug.log', "Name: $name, Content: $content\n", FILE_APPEND);

      // Xác thực dữ liệu
      if (empty($content)) {
          sendError('Comment content cannot be empty!');
      }
      if (strlen($content) > 500) {
          sendError('Comment is too long (max 500 characters)!');
      }
      if (strlen($name) > 50) {
          sendError('Name is too long (max 50 characters)!');
      }

      // Thoát ký tự
      $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
      $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

      // Thêm bình luận
      try {
          $uuid = generateUUID();
          file_put_contents('debug.log', "Generated UUID: $uuid\n", FILE_APPEND);
          $stmt = $pdo->prepare('INSERT INTO comments (id, name, content, timestamp, status) VALUES (?, ?, ?, ?, ?)');
          $stmt->execute([$uuid, $name ?: 'Anonymous', $content, time() * 1000, 'pending']);
          file_put_contents('debug.log', "Comment inserted successfully\n", FILE_APPEND);
      } catch (PDOException $e) {
          file_put_contents('error.log', "Insert failed: " . $e->getMessage() . "\n", FILE_APPEND);
          sendError('Failed to save comment: ' . $e->getMessage(), 500);
      }

      // Cập nhật thời gian
      $_SESSION['last_comment_time'] = time();
      file_put_contents('debug.log', "Session updated with last_comment_time: " . $_SESSION['last_comment_time'] . "\n", FILE_APPEND);

      echo json_encode(['success' => true, 'message' => 'Comment posted successfully!']);
  } elseif ($method === 'GET') {
      // Lấy danh sách bình luận
      $isAdmin = isset($_GET['admin']) && $_GET['admin'] === $adminPassword;
      $statusFilter = ''; // Bỏ điều kiện WHERE để hiển thị tất cả bình luận
      
      try {
          $stmt = $pdo->query("SELECT id, name, content, timestamp, status FROM comments $statusFilter ORDER BY timestamp DESC");
          $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
          file_put_contents('debug.log', "Fetched " . count($comments) . " comments\n", FILE_APPEND);
          echo json_encode(['comments' => $comments]);
      } catch (PDOException $e) {
          sendError('Failed to load comments: ' . $e->getMessage(), 500);
      }
  } elseif ($method === 'DELETE') {
      // Xóa bình luận
      $input = json_decode(file_get_contents('php://input'), true);
      if (!isset($input['id']) || !isset($input['password']) || $input['password'] !== $adminPassword) {
          sendError('Invalid ID or admin password!', 403);
      }

      try {
          $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
          $stmt->execute([$input['id']]);
          if ($stmt->rowCount() === 0) {
              sendError('Comment not found!');
          }
          echo json_encode(['success' => true, 'message' => 'Comment deleted successfully!']);
      } catch (PDOException $e) {
          sendError('Failed to delete comment: ' . $e->getMessage(), 500);
      }
  } elseif ($method === 'PUT') {
      // Kiểm duyệt bình luận
      $input = json_decode(file_get_contents('php://input'), true);
      if (!isset($input['id']) || !isset($input['status']) || !isset($input['password']) || $input['password'] !== $adminPassword) {
          sendError('Invalid data or admin password!', 403);
      }
      if (!in_array($input['status'], ['approved', 'rejected'])) {
          sendError('Invalid status!');
      }

      try {
          if ($input['status'] === 'rejected') {
              $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
              $stmt->execute([$input['id']]);
          } else {
              $stmt = $pdo->prepare('UPDATE comments SET status = ? WHERE id = ?');
              $stmt->execute(['approved', $input['id']]);
          }
          if ($stmt->rowCount() === 0) {
              sendError('Comment not found!');
          }
          echo json_encode(['success' => true, 'message' => 'Comment ' . $input['status'] . ' successfully!']);
      } catch (PDOException $e) {
          sendError('Failed to update comment: ' . $e->getMessage(), 500);
      }
  } else {
      sendError('Method not allowed!', 405);
  }
  ?>