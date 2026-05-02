-- Table: transport_details (TransportOffer)
CREATE TABLE IF NOT EXISTS `transport_details` (
  `transport_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `transport_type` varchar(50) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `departure_city` varchar(100) DEFAULT NULL,
  `arrival_city` varchar(100) DEFAULT NULL,
  `departure_datetime` datetime NOT NULL,
  `arrival_datetime` datetime NOT NULL,
  `available_seats` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `departure_station` varchar(100) DEFAULT NULL,
  `arrival_station` varchar(100) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`transport_id`),
  KEY `idx_transport_type` (`transport_type`),
  KEY `idx_departure_city` (`departure_city`),
  KEY `idx_arrival_city` (`arrival_city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: booking_transport (TransportBooking)
CREATE TABLE IF NOT EXISTS `booking_transport` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_date` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'PENDING',
  `total_price` decimal(10,2) NOT NULL DEFAULT 0,
  `passengers` int(11) DEFAULT 1,
  `pickup_datetime` datetime DEFAULT NULL,
  `dropoff_datetime` datetime DEFAULT NULL,
  `travel_class` varchar(50) DEFAULT NULL,
  `cabin_bags` int(11) DEFAULT 0,
  `checked_bags` int(11) DEFAULT 0,
  `pickup_location` varchar(150) DEFAULT NULL,
  `dropoff_location` varchar(150) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `idx_offer_id` (`offer_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: avis_transport (TransportAvis)
CREATE TABLE IF NOT EXISTS `avis_transport` (
  `id_avis_transport` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id_avis_transport`),
  KEY `idx_offer_id` (`offer_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: circuit_avis (CircuitAvis)
CREATE TABLE IF NOT EXISTS `circuit_avis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_circuit` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_circuit` (`id_circuit`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
