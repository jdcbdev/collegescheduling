<?php

require_once __DIR__ . '/Database.php';

class Room extends Database {

    public $id;
    public $room_name;
    public $capacity;

    function addRoom() {
        $conn = $this->connect();

        $sql = "INSERT INTO rooms 
                (room_name, capacity)
                VALUES (:room_name, :capacity)";

        $query = $conn->prepare($sql);
        $query->bindParam(':room_name', $this->room_name);
        $query->bindParam(':capacity', $this->capacity);

        if($query->execute()){
            return $conn->lastInsertId();
        }
        return false;
    }

    public function getRoomById($id) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    room_name,
                    capacity,
                    created_at
                FROM rooms
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllRooms() {

        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    room_name,
                    capacity,
                    created_at
                FROM rooms
                ORDER BY room_name ASC";

        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchRoom($searchQuery) {
        $conn = $this->connect();

        $sql = "SELECT 
                    id,
                    room_name,
                    capacity,
                    created_at
                FROM rooms
                WHERE room_name LIKE :query
                ORDER BY room_name ASC LIMIT 20";

        $query = $conn->prepare($sql);
        $searchParam = '%' . $searchQuery . '%';
        $query->bindParam(':query', $searchParam, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function roomExists($room_name, $excludeId = null) {
        $conn = $this->connect();

        $sql = "SELECT COUNT(*) as count FROM rooms
                WHERE room_name = :room_name";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $query = $conn->prepare($sql);
        $query->bindParam(':room_name', $room_name, PDO::PARAM_STR);
        if ($excludeId !== null) {
            $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return isset($result['count']) && $result['count'] > 0;
    }

    public function updateRoom() {
        $conn = $this->connect();

        $sql = "UPDATE rooms SET 
                room_name = :room_name,
                capacity = :capacity
                WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->bindParam(':id', $this->id);
        $query->bindParam(':room_name', $this->room_name);
        $query->bindParam(':capacity', $this->capacity);

        return $query->execute();
    }

    public function deleteRoom($id) {
        $conn = $this->connect();

        // Check if room has scheduled classes
        $query = $conn->prepare("SELECT COUNT(*) as count FROM schedules WHERE room_id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $scheduleCount = $query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($scheduleCount > 0) {
            return false;
        }

        $sql = "DELETE FROM rooms WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }
}
