<?php
require_once 'conn.php';

class Event {
    private $conn;
    public function __construct() {
        $this->conn = database();
    }

    public function createEvent($event, $date, $venue, $seats) {
        $stmt = $this->conn->prepare("INSERT INTO events (event, date, venue, seats)  VALUES (:event, :date, :venue, :seats)");
        $stmt->execute([
            'event' => $event,
            'date' => $date,
            'venue' => $venue,
            'seats' => $seats
        ]);
        return true;
    }

    public function getEventNameById($id) {
        $stmt = $this->conn->prepare("SELECT event FROM events WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
    }

    public function getEventDateById($id) {
        $stmt = $this->conn->prepare("SELECT date FROM events WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
    }

    public function getEventSeatsById($id) {
        $stmt = $this->conn->prepare("SELECT seats FROM events WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
    }

    public function updateEventName($event_id, $new_event) {
        $old_event = $this->getEventNameById($event_id);
        $stmt = $this->conn->prepare("UPDATE events SET event = :event WHERE id = :id");
        $stmt->execute([
            'event' => $new_event,
            'id' => $event_id
        ]);
        return [
            'old_event' => $old_event,
            'new_event' => $new_event
        ];
    }

    public function updateEventDate($event_id, $new_date) {
        $old_date = $this->getEventDateById($event_id);
        $stmt = $this->conn->prepare("UPDATE events SET date = :date WHERE id = :id");
        $stmt->execute(['date' => $new_date, 'id' => $event_id]);
        return [
            'old_date' => $old_date,
            'new_date' => $new_date
        ];
    }
}
?>