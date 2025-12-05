SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

CREATE TABLE
    `users` (
        `id` varchar(36) NOT NULL COMMENT 'Record ID',
        `name` varchar(255) NOT NULL COMMENT 'User Name',
        `email` varchar(255) NOT NULL COMMENT 'User Email Address'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `users` ADD PRIMARY KEY (`id`);

CREATE TABLE
    `events_store` (
        `id` bigint NOT NULL,
        `aggregate_id` varchar(64) NOT NULL,
        `aggregate_type` varchar(255) NOT NULL,
        `version` int NOT NULL,
        `event_type` varchar(255) NOT NULL,
        `payload` json NOT NULL,
        `occurred_at` datetime NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `events_store` ADD PRIMARY KEY (`id`),
ADD KEY `idx_event_store_aggregate` (`aggregate_type`, `aggregate_id`, `version`);

ALTER TABLE `events_store` MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

COMMIT;