<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// ✅ รองรับ CORS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once "../config/database.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ======================
    // GET (อ่านข้อมูลทั้งหมด)
    // ======================
    case 'GET':

        $sql = "SELECT * FROM products ORDER BY id DESC";
        $result = $conn->query($sql);

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        echo json_encode($products);
        break;

    // ======================
    // POST (เพิ่มข้อมูล)
    // ======================
    case 'POST':

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->product_name) || !isset($data->price)) {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Missing product_name or price"
            ]);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO products (product_name, price) VALUES (?, ?)");
        $stmt->bind_param("sd", $data->product_name, $data->price);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                "status" => 201,
                "message" => "Product created successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => 500,
                "message" => "Insert failed"
            ]);
        }

        $stmt->close();
        break;

    // ======================
    // PUT (แก้ไขข้อมูล)
    // ======================
    case 'PUT':

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id) || !isset($data->product_name) || !isset($data->price)) {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Missing id, product_name or price"
            ]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE products SET product_name=?, price=? WHERE id=?");
        $stmt->bind_param("sdi", $data->product_name, $data->price, $data->id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => 200,
                "message" => "Product updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => 500,
                "message" => "Update failed"
            ]);
        }

        $stmt->close();
        break;

    // ======================
    // DELETE (ลบข้อมูล)
    // ======================
    case 'DELETE':

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id)) {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Missing id"
            ]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $data->id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => 200,
                "message" => "Product deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => 500,
                "message" => "Delete failed"
            ]);
        }

        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "status" => 405,
            "message" => "Method Not Allowed"
        ]);
        break;
}

$conn->close();
?>
