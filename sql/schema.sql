-- =====================================================================
--  Police Crime Report Portal — Vulnerable Lab Database (MariaDB/MySQL)
--  Run once:  sudo mariadb < sql/schema.sql
-- =====================================================================

DROP DATABASE IF EXISTS police_portal;
CREATE DATABASE police_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Dedicated app user so PHP (www-data) can connect without root/socket auth
CREATE USER IF NOT EXISTS 'portal'@'localhost' IDENTIFIED BY 'portal123';
GRANT ALL PRIVILEGES ON police_portal.* TO 'portal'@'localhost';
FLUSH PRIVILEGES;

USE police_portal;

CREATE TABLE users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,   -- PLAINTEXT ON PURPOSE for the lab
  role       ENUM('citizen','officer') NOT NULL DEFAULT 'citizen',
  full_name  VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reports (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  tracking_code VARCHAR(20)  NOT NULL UNIQUE,
  reporter_id   INT,
  title         VARCHAR(200),
  category      VARCHAR(50),
  location      VARCHAR(200),
  description   TEXT,
  status        ENUM('open','investigating','closed','dismissed') NOT NULL DEFAULT 'open',
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE notes (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  report_id  INT,
  officer_id INT,
  note       TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
);

-- ---- Seed accounts -------------------------------------------------
INSERT INTO users (username, password, role, full_name) VALUES
('officer', 'police123', 'officer', 'Officer R. Khan'),
('admin',   'admin@123', 'officer', 'Sgt. A. Rahman'),
('rahim',   'rahim123',  'citizen', 'Rahim Uddin'),
('fatema',  'fatema123', 'citizen', 'Fatema Akter'),
('karim',   'karim123',  'citizen', 'Karim Hossain'),
('shila',   'shila123',  'citizen', 'Shila Rani');

-- ---- Seed reports (realistic Dhaka context, varied statuses/dates) --
INSERT INTO reports (tracking_code, reporter_id, title, category, location, description, status, created_at) VALUES
('CR-1001', 3, 'Motorbike stolen from market',        'Theft',     'Mirpur 10, Dhaka',      'My Pulsar motorbike was stolen from outside Shah Ali market around 9:15pm. Black, plate DHAKA-METRO-LA-34-5521.', 'open',          '2026-06-14 21:40:00'),
('CR-1002', 3, 'Phone snatched near footbridge',      'Robbery',   'Farmgate, Dhaka',       'Two men on a red motorbike snatched my phone while I was crossing under the footbridge.',                        'investigating', '2026-06-18 19:05:00'),
('CR-1003', 5, 'Shop shutter broken overnight',       'Vandalism', 'Uttara Sector 7',       'Someone broke the shutter lock and cracked the front glass of my electronics shop overnight.',                   'open',          '2026-06-20 07:30:00'),
('CR-1004', 4, 'Online seller took advance and ran',  'Fraud',     'Dhanmondi 27',          'Paid 12,000 BDT advance to a Facebook marketplace seller for a phone. Seller blocked me and disappeared.',        'investigating', '2026-06-22 14:20:00'),
('CR-1005', 6, 'Harassment at bus stand',             'Assault',   'Gulistan',              'Was harassed and pushed by a group near the bus counter. One person tried to grab my bag.',                       'closed',        '2026-06-11 18:10:00'),
('CR-1006', 5, 'Laptop bag lifted from cafe',         'Theft',     'Banani 11',             'Left my seat for two minutes; laptop bag with a Dell laptop and documents was gone when I returned.',             'open',          '2026-06-25 16:45:00'),
('CR-1007', 4, 'House broken into during Eid trip',   'Burglary',  'Bashundhara R/A, Block C','Returned from a trip to find the back door forced open. Cash and jewellery missing.',                            'investigating', '2026-06-09 11:00:00'),
('CR-1008', 3, 'Repeated extortion calls',            'Other',     'Mohammadpur',           'Getting repeated calls from an unknown number demanding money and threatening my family.',                        'open',          '2026-06-28 22:30:00'),
('CR-1009', 6, 'Rickshaw held up at night',           'Robbery',   'Jatrabari',             'Two men stopped my rickshaw near the flyover and took my cash and wallet.',                                       'closed',        '2026-06-05 23:15:00'),
('CR-1010', 5, 'Fake job offer, paid a fee',          'Fraud',     'Motijheel',             'Paid a 3,000 BDT processing fee for a job that never existed. The office address was fake.',                       'dismissed',     '2026-05-30 10:25:00'),
('CR-1011', 4, 'Car keyed in parking lot',            'Vandalism', 'Baridhara DOHS',        'Someone scratched a long line across the driver-side doors of my parked car in the DOHS lot.',                    'open',          '2026-07-01 09:00:00'),
('CR-1012', 3, 'Gold chain snatched in traffic',      'Theft',     'Mouchak',               'A man on foot snatched my wife''s gold chain while our CNG was stuck in traffic, then ran into an alley.',        'investigating', '2026-07-03 17:50:00');

-- ---- Seed case notes -----------------------------------------------
INSERT INTO notes (report_id, officer_id, note, created_at) VALUES
(2, 1, 'Reviewed footbridge CCTV. Two suspects on a red bike, partial plate visible. Requesting clearer footage.', '2026-06-19 10:15:00'),
(4, 1, 'Contacted the marketplace platform for seller KYC and payment trail. Awaiting response.',                  '2026-06-23 12:40:00'),
(7, 2, 'Crime scene photographed. Neighbour reports an unfamiliar van parked nearby that week.',                    '2026-06-10 15:20:00');
