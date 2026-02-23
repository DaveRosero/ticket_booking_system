<?php
require_once '../model/user.php';

class UserController {
    private $user;
    public function __construct() {
        $this->user = new User();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'PATCH':
                if (!isset($data['event_id']) || empty($data['event_id'])) {
                    $this->jsonResponse(false, "This event does not exist.");
                    return;
                }
                try {
                    if (isset($data['seats']) || !empty($data['seats'])) {
                        $event = $this->user->reserveSeat($data['event_id'], $data['seats']);
                        $event_date = new DateTime($event['date']);
                        $formatted_event_date = $event_date->format('F j, Y g:i A');
                        if ($event !== false) {
                            $this->jsonResponse(true, "You have reserved ". $event['seats'] . " seats for the event '". ucwords($event['event']) . "' scheduled on " . $formatted_event_date . ".");
                            return;
                        }
                        $this->jsonResponse(false, "There are not enough seats availble.");
                        return;
                    }
                } catch (PDOException $e) {
                    if (APP_DEBUG) {
                        $this->jsonResponse(false, $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
                        return;
                    } else { 
                        error_log($e->getMessage());
                        $this->jsonResponse(false, 'Internal Server Error');
                    }
                }
                break;
            default:
                $this->methodNotAllowed();
                break;
        }
    }

    public function jsonResponse($bool, $msg = null) {
        echo json_encode([
            'success' => $bool,
            'msg' => $msg
        ]);
        return;
    }

    public function methodNotAllowed() {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Method Not Allowed'
        ]);
        exit();
    }
}

$controller = new UserController();
$controller->handleRequest();
?>