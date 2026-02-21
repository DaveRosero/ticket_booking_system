<?php
require_once '../model/event.php';

class EventController {
    private $event;
    public function __construct() {
        $this->event = new Event();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $event = $data['event'] ?? '';
                $date = $data['date'] ?? '';
                $venue = $data['venue'] ?? '';
                $seats = $data['seats'] ?? 0;

                if (empty($event) || empty($date) || empty($venue) || empty($seats)) {
                    $this->jsonResponse(false, "All fields are required.");
                    return;
                }

                try {
                    if ($this->event->createEvent($event, $date, $venue, $seats)) {
                        $this->jsonResponse(true, "A new event has been created.");
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
            case 'PATCH':
                $event_id = $data['event_id'] ?? '';
                if (empty($event_id)) {
                    $this->jsonResponse(false, "The event does not exist.");
                    return;
                }
                try {
                    $changes = [];
                    $updates = "Updates: ";
                    if (isset($data['event']) && !empty($data['event'])) {
                        $changes['event'] = $this->event->updateEventName($event_id, $data['event']);
                        $updates .= "Event: " . ucwords($changes['event']['old_event'] . " -> " . ucwords($changes['event']['new_event'] . "; "));                
                    }
                    if (isset($data['date']) && !empty($data['date'])) {
                        $changes['date'] = $this->event->updateEventDate($event_id, $data['date']);
                        $updates .= "Date: " . strtotime($changes['date']['old_date']) . " -> " . strtotime($changes['date']['new_date']) . "; "; 
                    }
                    $this->jsonResponse(true, $updates);
                    return;
                } catch (PDOException $e) {
                    if (APP_DEBUG) {
                        $this->jsonResponse(false, $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
                        return;
                    } else { 
                        error_log($e->getMessage());
                        $this->jsonResponse(false, 'Internal Server Error');
                    }
                }
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

$controller = new EventController();
$controller->handleRequest();
?>