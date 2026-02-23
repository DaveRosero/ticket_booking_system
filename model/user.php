<?php
require_once 'conn.php';
require_once 'event.php';

class User {
    private $conn;
    protected $event;
    public function __construct() {
        $this->conn = database();
        $this->event = new Event();
    }

    public function reserveSeat($event_id, $seats_requested) {
        $seats_available = $this->event->getEventSeatsById($event_id);
        if ($seats_available == 0 || $seats_available < $seats_requested) {
            return false;
        }
        $seats_reserved = $seats_available - $seats_requested;
        $stmt = $this->conn->prepare("UPDATE events SET seats = :seats WHERE id = :id LIMIT 1");
        $stmt->execute(['seats' => $seats_reserved, 'id' => $event_id]);
        $event_name = $this->event->getEventNameById($event_id);
        $event_date = $this->event->getEventDateById($event_id);
        return [
            "event" => $event_name,
            "date" => $event_date,
            "seats" => $seats_requested
        ];
    }
}
?>