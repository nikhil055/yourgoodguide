-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: finchskills_db
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','admin@finchskills.com','$2y$10$/2Ny.fGQmmM2cS0LYeVRHOD/aLTIQifLH1N3mOn0YdCaT/WtC.Et6','2026-09-09 05:54:35');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admissions`
--

DROP TABLE IF EXISTS `admissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `percentage` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passing_year` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `board_university` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_office` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `marksheet10` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marksheet12` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar_card` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `fee_total` decimal(10,2) DEFAULT '0.00',
  `fee_paid` decimal(10,2) DEFAULT '0.00',
  `fee_pending` decimal(10,2) DEFAULT '0.00',
  `payment_plan` enum('full','installment') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('Pending Payment','Partial Paid','Fully Paid') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending Payment',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admissions`
--

LOCK TABLES `admissions` WRITE;
/*!40000 ALTER TABLE `admissions` DISABLE KEYS */;
INSERT INTO `admissions` VALUES (1,'FS-2026-2546','Nikhil singh','fsdf','nikhilbhadauriya.gspt@gmail.com','06676767677','559687401326','2026-09-24','Male','Certificate Course in Travel & Air Ticketing','12th Pass (Pursuing)','fsdf','2001','dfsd','Uttar Pradesh','Noida','dfsdf','201307','Mamura, Noida, Dadri, Gautam Buddha Nagar, Uttar Pradesh, 201307, India','m10_1790116540_6ab302bc5e729.png','m12_1790116540_6ab302bc5ef7a.png','aadh_1790116540_6ab302bc5f271.png','uploads/students/std_1790113330_601.png','Pending',0.00,0.00,0.00,NULL,'Pending Payment',1,'2026-09-22 22:35:40');
/*!40000 ALTER TABLE `admissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_inquiries`
--

DROP TABLE IF EXISTS `contact_inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_inquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('New','In Progress','Resolved') COLLATE utf8mb4_unicode_ci DEFAULT 'New',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_inquiries`
--

LOCK TABLES `contact_inquiries` WRITE;
/*!40000 ALTER TABLE `contact_inquiries` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_inquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_categories`
--

DROP TABLE IF EXISTS `course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `icon` varchar(60) COLLATE utf8mb4_general_ci DEFAULT 'fa-graduation-cap',
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_categories`
--

LOCK TABLES `course_categories` WRITE;
/*!40000 ALTER TABLE `course_categories` DISABLE KEYS */;
INSERT INTO `course_categories` VALUES (1,'Aviation Management','aviation-management','fa-plane-departure','active','2026-09-20 18:00:28'),(2,'Hospitality & Hotel','hospitality-hotel','fa-hotel','active','2026-09-20 18:00:28'),(3,'Travel & Tourism','travel-tourism','fa-earth-americas','active','2026-09-20 18:00:28'),(4,'Cruise Operations','cruise-operations','fa-ship','active','2026-09-20 18:00:28'),(5,'Aviation & Cabin Crew','aviation-cabin-crew','fa-plane-departure','active','2026-09-20 18:21:58'),(6,'Airport Operations','airport-operations','fa-id-badge','active','2026-09-20 18:21:58'),(7,'Hotel Management','hotel-management','fa-hotel','active','2026-09-20 18:21:58'),(8,'Grooming & Personality','grooming-personality','fa-user-tie','active','2026-09-20 18:21:58'),(9,'Cruise & Maritime','cruise-maritime','fa-ship','active','2026-09-20 18:21:58');
/*!40000 ALTER TABLE `course_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `duration` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '6 Months',
  `study_mode` varchar(60) COLLATE utf8mb4_general_ci DEFAULT 'Online / Offline',
  `fee` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_desc` text COLLATE utf8mb4_general_ci,
  `full_desc` longtext COLLATE utf8mb4_general_ci,
  `what_you_learn` longtext COLLATE utf8mb4_general_ci,
  `how_we_teach` longtext COLLATE utf8mb4_general_ci,
  `career_roles` text COLLATE utf8mb4_general_ci,
  `eligibility` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '10+2 / Any Graduate',
  `certification` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'Industry Recognized Certification',
  `is_featured` tinyint(1) DEFAULT '1',
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,1,'Professional Course in Airport Management','airport-management-course','12 Months','Online / Hybrid Campus','₹45,000','img/airport-management-course.png','Equip yourself with essential skills in airport terminal operations, passenger services, security compliance, and airline coordination for successful careers in global aviation.','The Professional Course in Airport Management at Finchskills Institute is designed to give you a complete, practical understanding of modern airline and airport terminal operations. From check-in counters and baggage systems to airside safety and crisis handling, this course ensures you are completely job-ready for leading domestic and international airlines.','Airport Terminal Architecture & Passenger Flow\nPassenger Check-in Procedures & Boarding Management\nBaggage Handling, Screening & Reconciliation\nAviation Security (AVSEC), DGR & Safety Protocols\nAirline Customer Service Excellence & Conflict Handling\nFlight Dispatch Basics & Airport Emergency Procedures','Live Airport Mock Terminal Roleplays\nAirline Recruiter Interview Preparation\nInternational Grooming, Etiquette & Soft Skills\nDirect Industrial Visits & Expert Airline Masterclasses','Airport Ground Staff, Terminal Executive, Passenger Service Agent, Guest Relations Officer, Flight Dispatch Assistant','10+2 / Any Graduate (Age 18-27)','Professional Airport Operations Diploma',1,'active','2026-09-20 18:00:28'),(2,3,'Certificate Course in Travel & Air Ticketing','certificate-course-in-travel-air-ticketing','6 Months','Classroom with Live GDS Software','₹35,000','img/Certificate-Course.webp','Master Global Distribution Systems (GDS like Amadeus/Galileo), airfare calculations, PNR creation, and flight ticket issuance for instant airline hiring.','Become an expert airline ticketing professional by mastering industry-standard Global Distribution Systems (GDS) including Amadeus and Galileo. Learn flight searches, PNR creation, fare construction, mileage rules, airfare calculation, reissuance, cancellation, and refund management demanded by commercial airlines, online travel portals (OTAs), and corporate travel desks.\n\nThis practical hands-on training opens high-paying careers in airline reservation centers and multinational travel corporations.','GDS Architecture, Terminal Commands & Navigation (Amadeus / Galileo)\nFlight Availability Searches, Class of Service & Passenger PNR Generation\nAirfare Construction, Currency Conversion, Taxes & Baggage Allowances\nE-Ticket Issuance, Revalidation, Date Changes & Automated Reissuance\nTicket Cancellation, Refund Processing, Void Rules & MPD / EMD Issuance\nSpecial Service Requests (SSR), Frequent Flyer Accruals & Ancillary Bookings','Daily Hands-on Live Terminal Practice on Certified GDS Software\nReal-Time Airfare Calculation & Multi-City Itinerary Drills\nLive Ticket Booking, Exchange & Cancellation Case Studies\nDirect Industry Placement Drives with Commercial Airlines & Top OTAs','Air Ticketing Executive, GDS Reservation Specialist, Airline Customer Service Officer, Corporate Travel Consultant, Fare & Tariff Analyst','12th Pass or Graduate with basic computer typing knowledge.','Professional Certificate in GDS Air Ticketing & Global Reservations',1,'active','2026-09-20 18:00:28'),(3,2,'Diploma in Hospitality & Guest Relations','hospitality-guest-relations-diploma','12 Months','Classroom & Practical Labs','₹55,000','img/hospitality-management-course.png','Develop 5-star hospitality standards with comprehensive training in front office operations, guest service excellence, luxury dining etiquette, and accommodation management.','Step into the glamorous world of luxury hotels, 5-star resorts, and corporate concierge services. This diploma covers front desk systems, guest conflict resolution, VIP protocol management, and banquet operations.','Front Office Hierarchy & Property Management Systems (PMS)\nLuxury Guest Relations & VIP Protocol Management\nFood & Beverage Service Principles & Banqueting\nHousekeeping Operations & Room Inventory Control\nHigh-Impact Communication & Multilingual Basics\nComplaint Handling & Service Recovery Strategies','5-Star Hotel Mock Reception & Room Setup\nFine Dining Table Etiquette & Professional Grooming\nMock Interviews by Hotel HR Managers\nIndustry Internship & Placement Guarantee Support','Front Desk Executive, Guest Relations Associate, Concierge Specialist, F&B Supervisor, VIP Lounge Host','10+2 / Higher Secondary / Graduate','Professional Diploma in Hospitality Management',1,'active','2026-09-20 18:00:28'),(4,9,'Professional Course in Cruise Management','cruise-ship-operations-course','6 Months','Classroom & Maritime Simulation','₹65,000','img/Cruise-Management-course.jpg','Prepare for rewarding careers on international cruise lines with specialized training in shipboard hospitality, guest relations, safety protocols, and onboard crew operations.','Prepare for high-earning, tax-free international careers aboard luxury cruise liners, mega-ships, and international yachts. This comprehensive program covers shipboard guest relations, international maritime safety conventions (STCW guidelines), luxury dining operations, housekeeping management, and crew life protocols.\n\nOur instructors guide you through international cruise recruitment drives, visa medical standards, and maritime interview clearance.','Cruise Liner Operations, Deck Hierarchy & Maritime Terminology\nShipboard Guest Services, Concierge & Front Desk Operations\nInternational Maritime Safety Awareness & Emergency Procedures\nFine Dining & Food & Beverage Service Aboard Luxury Cruise Lines\nCross-Cultural Passenger Hospitality & Conflict Resolution\nSTCW Basic Safety Guidelines, US Visa (C1/D) Preparation & Crew Etiquette','Cruise Ship Mock Dining & Cabin Service Practical Drills\nMaritime Safety & Emergency Protocol Roleplay Simulations\nCross-Cultural Communication & International Etiquette Workshops\nDirect Interview Grooming for Global Cruise Line Recruitment Drives','Cruise Hospitality Associate, Shipboard Guest Relations Officer, F&B Service Attendant, Stateroom Host, Cruise Activity Coordinator','10+2 (12th Pass) or Hotel/Aviation Graduate. Age 18-30. Valid passport preferred.','Professional Diploma in International Cruise Ship Operations',1,'active','2026-09-20 18:00:28'),(5,5,'Professional Course in Air Hostess','professional-course-in-air-hostess','12 Months','Classroom & Cabin Labs','₹75,000','img/airhostess-course.png','Prepare for a successful career in aviation. Develop professional grooming, in-flight safety protocols, hospitality etiquette, and communication skills required to excel as cabin crew.','Prepare for a successful career in domestic and international commercial aviation with our premier Air Hostess & Cabin Crew training program. Develop professional grooming standards, in-flight passenger safety protocols, international hospitality etiquette, emergency situation management, and multilingual customer communication skills required to excel as elite cabin crew.\n\nOur curriculum is taught by former flight purser instructors and senior airline trainers, giving you hands-on exposure to simulated aircraft cabin environments.','Aviation Industry Overview & Commercial Aircraft Familiarization\nIn-Flight Safety Standards & Emergency Evacuation Protocols\nPassenger Service Excellence & First Aid Medical Training\nAviation English, Announcement Voice & International Communication\nIn-Flight Food, Wine & Luxury Beverage Service Standards\nExecutive Personality Grooming, Skin Care, Makeup & Hair Masterclass','Hands-on Cabin Mock-Up & Real-Life Emergency Drill Simulations\nProfessional Uniform, Hair, Makeup & High-Standard Grooming Workshops\nCustomer Conflict Resolution & De-escalation Roleplay Scenarios\nOne-on-One Airline Mock HR & GD Technical Rounds with Ex-Cabin Crew','Air Hostess, Cabin Crew, Flight Attendant, VIP Corporate Flight Purser, In-Flight Services Associate','10+2 (12th Pass) in any stream. Age 17-27 years. Pleasing personality & clear speech.','Certified Professional in Air Hostess & Cabin Crew Operations',1,'active','2026-09-20 18:21:58'),(6,6,'Professional Course in Ground Staff & Hospitality','ground-staff-hospitality-course','12 Months','Classroom & Airport Labs','₹48,000','img/Hospitality-course.png','Gain comprehensive training in airport terminal operations, passenger check-in handling, ramp coordination, security protocols, and luxury hospitality management.','Gain comprehensive practical training in airport terminal operations, departure control systems, passenger check-in handling, ramp coordination, baggage management, aviation security regulations, and high-touch hospitality. This course is specially tailored for aspirants aiming for prestigious airport roles across domestic and international airports.\n\nStudents receive practical instruction on real check-in terminals, customer service psychology, and ground handling coordination.','Airport Terminal Architecture & Passenger Flow Management\nDeparture Control Systems (DCS) & Boarding Gate Coordination\nBaggage Handling, Tagging & Lost and Found Systems (WorldTracer)\nAviation Security Protocols, DGR (Dangerous Goods Regulations) & Safety\nRamp Safety, Turnaround Coordination & Marshalling Basics\nVIP Lounge Hospitality & Special Assistance (PRM) Passenger Care','Simulated Airport Terminal & Boarding Gate Practical Drills\nLive Passenger Check-in Software & Baggage Tagging Training\nEmergency De-escalation & Passenger Assistance Roleplays\nMock Airport Interviews with Leading Domestic & Global Ground Handlers','Airport Ground Staff, Passenger Service Agent, Boarding Gate Officer, Customer Service Associate, Ramp Coordinator, VIP Airport Lounge Host','12th Pass or Any Graduate from a recognized board/university.','Professional Diploma in Airport Ground Operations & Hospitality',1,'active','2026-09-20 18:21:58'),(7,3,'Professional Course in Travel & Tourism','professional-course-in-travel-tourism','12 Months','Classroom & Online Hybrid','₹40,000','img/Diploma-travell.webp','Build a strong foundation in international tourism trends, tour itinerary planning, visa assistance, and customer relationship expertise for global travel agencies.','Build a strong foundation in international tourism trends, outbound & inbound tour itinerary planning, visa documentation, global geography, customer relationship management, and digital travel marketing. Learn how world-class travel agencies, Destination Management Companies (DMCs), and online travel portals (OTAs) operate.\n\nGain practical proficiency in designing customized international tour packages, luxury cruise bookings, and corporate business travel arrangements.','World Geography, Time Zones & Major Global Tourism Hotspots\nInbound & Outbound Tour Package Design & Costing\nInternational Visa Application Documentation & Consular Formalities\nHotel Contracting, Airport Transfers & Sightseeing Logistics\nCustomer Relationship Management for Travel Consultants\nDigital Travel Portals (OTAs), Online Booking Tools & Marketing','Live Itinerary Creation, Package Budgeting & Costing Projects\nCase Studies on Popular International Tourism Circuits\nRoleplay on Client Travel Consultations, Negotiation & Objection Handling\nIndustry Visits to Leading Tour Operators & Travel Management Companies','Tour Operations Executive, Travel Consultant, Holiday Package Planner, Visa Documentation Specialist, Destination Expert, Corporate Travel Desk Officer','12th Pass or Graduate in any stream with basic computer knowledge.','Certified Professional in Global Travel & Tourism Operations',1,'active','2026-09-20 18:21:58'),(8,7,'Foundation Course in Hotel Management','foundation-course-in-hotel-management','6 Months','Classroom & Hotel Labs','₹38,000','img/Hotel-Management-course.webp','Learn the fundamentals of hotel operations, front-office systems, housekeeping standards, food & beverage service, and guest relations for 5-star hotels and resorts.','Master the fundamentals of world-class hotel operations across four core hospitality pillars: Front Office Management, Food & Beverage Service, Housekeeping Operations, and Guest Relations. This intensive 6-month foundation program prepares students for immediate hiring across leading 5-star hotel chains, luxury resorts, and high-end banquet establishments.\n\nYou will master international etiquette, table service standards, front desk reservation systems, and professional hospitality communication.','Front Office Operations, Guest Check-in & Hotel PMS Software\nFood & Beverage Service Etiquette, Banquet Operations & Table Layouts\nHousekeeping Standards, Room Preparation, Linen & Quality Audits\nGuest Relations, Concierge Assistance & VIP Protocol Handling\nBeverage Fundamentals, Service Hygiene & Sanitation Practices\nHospitality Spoken English, Grooming & Customer Care Excellence','Hands-on Fine Dining Table Setting & F&B Service Lab Sessions\nLive Hotel Front Desk Simulation & Reservation Software Roleplay\nHousekeeping Room Inspection Drills & Hospitality Cleanliness Audits\n5-Star Hotel Exposure Visits & Interactive Masterclasses with General Managers','Front Office Executive, Guest Relations Associate, F&B Service Steward, Banquet Host, Housekeeping Supervisor, Resort Concierge','10th Pass or 12th Pass in any discipline.','Foundation Certificate in Hotel Operations & Hospitality Management',1,'active','2026-09-20 18:21:58'),(9,8,'Professional Course in Personality Development','professional-course-in-personality-development','3 Months','Classroom & Interactive Masterclasses','₹18,000','img/personality-development-course.webp','Elevate your career presence with corporate etiquette, spoken English fluency, body language refinement, and rigorous mock interview simulations.','Transform your professional presence, boost self-confidence, and master high-impact executive communication. Specially designed for job aspirants preparing for competitive airline, hospitality, and corporate interviews, this course refines spoken English fluency, body language, corporate dining etiquette, active listening, and interview clearing strategies.\n\nStudents undergo intensive video-recorded mock interview panels and personalized voice-and-accent refinement sessions.','Spoken English Fluency, Pronunciation & Accent Neutralization\nProfessional Body Language, Posture, Eye Contact & Hand Gestures\nCorporate Dressing Standards, Wardrobe Planning, Makeup & Personal Grooming\nPublic Speaking Confidence, Overcoming Hesitation & Stage Fear\nGroup Discussion (GD) Strategies, Moderation & Leadership Presence\nHR Interview Clearing Techniques, Behavioral Answers & Resume Optimization','Daily Video-Recorded Speaking & Body Language Review Sessions\nExtensive Group Discussions, Debates & Impromptu Speech Drills\nProfessional Personal Grooming & Executive Styling Workshops\nSimulated HR & Technical Panel Mock Interviews with Constructive Feedback','Customer Service Executive, Corporate PR Associate, Front Desk Coordinator, Airline Customer Care Officer, Corporate Host','Open to all students (10th, 12th, or Graduates) seeking career transformation.','Executive Certificate in Corporate Personality & Communication',1,'active','2026-09-20 18:21:58');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_submissions`
--

DROP TABLE IF EXISTS `fee_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fee_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','Verified','Rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_submissions`
--

LOCK TABLES `fee_submissions` WRITE;
/*!40000 ALTER TABLE `fee_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_pages`
--

DROP TABLE IF EXISTS `site_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `is_system` tinyint(1) DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_pages`
--

LOCK TABLES `site_pages` WRITE;
/*!40000 ALTER TABLE `site_pages` DISABLE KEYS */;
INSERT INTO `site_pages` VALUES (1,'terms-conditions','Terms and Conditions','Please read these terms carefully before enrolling or using our services','<h2>1. Acceptance of Terms</h2>\n<p>Welcome to <strong>Finchskills Institute</strong>. By registering on our website, applying for our certification or diploma programs, submitting admission forms, or accessing our candidate portal, you agree to comply with and be bound by the following terms and conditions.</p>\n\n<h2>2. Eligibility & Admission Policy</h2>\n<p>Admissions to training programs offered by Finchskills Institute are subject to candidate eligibility verification, seat availability, and timely clearance of prescribed registration/admission fees. The Institute reserves the right to accept or reject any application based on documentation validity.</p>\n\n<h2>3. Fee Payment & Installment Policy</h2>\n<p>Course fees may be paid in full (with applicable full-payment discount) or through authorized part-wise installment plans. Installment dues must be cleared on or before their respective scheduled due dates. Delays in installment payments may lead to temporary suspension of class access and certificate processing.</p>\n\n<h2>4. Attendance & Code of Conduct</h2>\n<p>Students must maintain a minimum of 75% attendance throughout the course duration to be eligible for practical training modules, placement assistance, and final certification assessments. Any form of academic dishonesty, misconduct, or disruptive behavior will result in disciplinary action.</p>\n\n<h2>5. Placement Assistance Disclaimer</h2>\n<p>Finchskills Institute offers dedicated placement assistance, interview preparation, and corporate networking. While we make every endeavor to connect eligible candidates with leading employers in aviation, travel, and hospitality, final selection depends on the candidate\'s performance in interviews.</p>\n\n<h2>6. Intellectual Property</h2>\n<p>All study materials, lecture recordings, curriculum notes, and portal assets provided by Finchskills Institute remain the exclusive intellectual property of the Institute and may not be redistributed without prior written consent.</p>\n\n<h2>7. Contact Information</h2>\n<p>For any questions regarding these terms, please contact us at <a href=\"mailto:info@finchskills.com\">info@finchskills.com</a> or call +91 96503 86711.</p>','Terms and Conditions - Finchskills Institute','Official terms and conditions governing admission, code of conduct, and academic training at Finchskills Institute.','active',1,'2026-09-22 22:48:03','2026-09-22 22:48:03'),(2,'privacy-policy','Privacy Policy','How we collect, use, protect, and handle your personal information','<h2>1. Information We Collect</h2>\n<p>When you register, apply for courses, or submit inquiries on our portal, we may collect personal identifiable information including your full name, email address, contact number, date of birth, educational qualifications, identification documents (such as Aadhaar), and payment transaction details.</p>\n\n<h2>2. How We Use Your Information</h2>\n<p>We use the collected information for the following legitimate purposes:</p>\n<ul>\n    <li>Processing course admissions, candidate registrations, and seat reservations.</li>\n    <li>Sending admission confirmation emails, fee receipts, and installment schedule reminders.</li>\n    <li>Managing candidate portal accounts and student progress tracking.</li>\n    <li>Communicating important academic updates, class schedules, and placement opportunities.</li>\n    <li>Complying with regulatory, auditing, and legal statutory obligations.</li>\n</ul>\n\n<h2>3. Data Protection & Security</h2>\n<p>We implement industry-standard security measures including SSL 256-bit encryption, restricted database access, and secure payment processing via PCI-DSS compliant payment gateways (such as Razorpay). We never store raw credit/debit card numbers or CVVs on our servers.</p>\n\n<h2>4. Third-Party Sharing</h2>\n<p>Finchskills Institute does not sell, trade, or rent candidate personal information to third parties. Information may only be shared with authorized academic accreditation bodies, background verification partners for placement, or payment gateway providers solely for service delivery.</p>\n\n<h2>5. Cookies & Analytics</h2>\n<p>Our website may use cookies and web analytics to improve browsing experience, analyze site traffic, and optimize our learning portal interfaces.</p>\n\n<h2>6. Your Rights & Inquiries</h2>\n<p>You may request correction of your profile data or ask questions regarding our privacy practices by writing to <a href=\"mailto:privacy@finchskills.com\">privacy@finchskills.com</a>.</p>','Privacy Policy - Finchskills Institute','Learn how Finchskills Institute collects, stores, and protects candidate personal and educational data.','active',1,'2026-09-22 22:48:03','2026-09-22 22:48:03'),(3,'refund-policy','Refund & Cancellation Policy','Guidelines regarding course enrollment cancellations and fee refunds','<h2>1. Registration & Seat Reservation Fee</h2>\n<p>Initial seat reservation and candidate registration fees are generally non-refundable as they cover provisional seat allocation, portal license setup, and administrative onboarding costs.</p>\n\n<h2>2. Course Admission Fee Refund Guidelines</h2>\n<p>Requests for course fee refunds must be submitted in writing to the Accounts Department within the following timeframes:</p>\n<ul>\n    <li><strong>Before Batch Commencement (7+ days prior):</strong> Eligible for an 80% refund of the total fee paid (excluding administrative and processing fees).</li>\n    <li><strong>Within 3 Days of Batch Start:</strong> Eligible for up to 50% refund subject to management review.</li>\n    <li><strong>After 7 Days of Batch Commencement:</strong> No refunds will be issued once training classes and learning resources have been accessed.</li>\n</ul>\n\n<h2>3. Mode of Refund Processing</h2>\n<p>Approved refunds are processed via the original payment method (bank account / payment gateway) within <strong>7 to 10 working days</strong> from the date of official written approval.</p>\n\n<h2>4. Batch Transfers & Rescheduling</h2>\n<p>If a candidate cannot continue in their allocated batch due to medical or unavoidable circumstances, they may request a batch transfer or freeze their enrollment for up to 6 months without paying the course fee again.</p>\n\n<h2>5. Contact for Refund Requests</h2>\n<p>Please send all refund-related formal requests with payment receipts and student ID to <a href=\"mailto:accounts@finchskills.com\">accounts@finchskills.com</a>.</p>','Refund & Cancellation Policy - Finchskills Institute','Official fee refund and course cancellation policy of Finchskills Institute.','active',1,'2026-09-22 22:48:03','2026-09-22 22:48:03'),(4,'shipping-policy','Shipping & Delivery Policy','Details regarding digital training access, study kit delivery, and certificate dispatch','<h2>1. Digital Access Delivery</h2>\n<p>Upon successful registration and fee verification, candidate portal credentials, course syllabus, and lecture timetables are delivered instantly via email and portal dashboard.</p>\n\n<h2>2. Physical Study Kits & Training Material</h2>\n<p>For classroom and hybrid batches where physical kits, books, or uniforms are included, items are handed over at the institute center during the orientation session or dispatched via speed courier within <strong>5 to 7 business days</strong>.</p>\n\n<h2>3. Certificate & Diploma Dispatch</h2>\n<p>Official course completion certificates and diplomas are awarded during convocation or dispatched via registered post / courier to the candidate\'s verified residential address within <strong>15 days</strong> of final assessment clearance.</p>\n\n<h2>4. Tracking & Support</h2>\n<p>For any queries related to material or certificate delivery, please contact our support desk at <a href=\"mailto:support@finchskills.com\">support@finchskills.com</a>.</p>','Shipping & Delivery Policy - Finchskills Institute','Delivery terms for study materials, portal access, and physical diploma certificates.','active',1,'2026-09-22 22:48:03','2026-09-22 22:48:03'),(5,'disclaimer','Disclaimer','General educational and informational disclaimer','<h2>1. General Information</h2>\n<p>The information provided on the Finchskills Institute website is for general educational, skill development, and career guidance purposes. While we endeavor to keep all information up to date and correct, course structures and curriculum may evolve to match industry standards.</p>\n\n<h2>2. Placement Assistance</h2>\n<p>Placement assistance provided by Finchskills Institute refers to career counseling, interview scheduling, resume building, and recruiter networking. We do not sell or guarantee government or private employment contracts.</p>\n\n<h2>3. External Links</h2>\n<p>Our website may contain links to external third-party websites for reference. Finchskills Institute has no control over the nature, content, and availability of those external sites.</p>','Disclaimer - Finchskills Institute','General educational and placement disclaimer of Finchskills Institute.','active',1,'2026-09-22 22:48:03','2026-09-22 22:48:03');
/*!40000 ALTER TABLE `site_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES ('address','Orbit Plaza, Crossing Republik, NH-24, Ghaziabad, Uttar Pradesh, 201016','2026-09-09 05:54:35'),('email_1','hello@finchskills.com','2026-09-09 05:54:35'),('email_2','admission@finchskills.com','2026-09-09 05:54:35'),('facebook_url','https://facebook.com/','2026-09-09 05:54:35'),('footer_logo','img/footer-logo.png','2026-09-09 05:54:35'),('full_payment_discount_percent','10','2026-09-22 22:28:12'),('instagram_url','https://instagram.com/','2026-09-09 05:54:35'),('linkedin_url','https://linkedin.com/','2026-09-09 05:54:35'),('map_iframe','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.9501409758395!2d77.43262847457278!3d28.63125638414035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cee300c363997%3A0xe53a3aaacf648c83!2sOrbit%20plaza%2C%20Crossings%20Republik%2C%20Ghaziabad%2C%20Uttar%20Pradesh%20201016!5e0!3m2!1sen!2sin!4v1784362846035!5m2!1sen!2sin','2026-09-09 05:54:35'),('phone_1','+919650386711','2026-09-09 05:54:35'),('phone_2','+919876543210','2026-09-09 05:54:35'),('razorpay_enabled','1','2026-09-21 07:39:02'),('razorpay_live_key_id','','2026-09-21 07:39:02'),('razorpay_live_key_secret','','2026-09-21 07:39:02'),('razorpay_mode','test','2026-09-21 07:39:02'),('razorpay_test_key_id','rzp_test_1DP5mmOlF5G5ag','2026-09-21 07:39:02'),('razorpay_test_key_secret','s98s8F7d6g5H4j3K2l1P0o','2026-09-21 07:39:02'),('seat_booking_fee','999','2026-09-21 07:39:02'),('site_favicon','img/favicon.png','2026-09-09 05:54:35'),('site_logo','img/logo.png','2026-09-09 05:54:35'),('smtp_enabled','1','2026-09-21 07:58:42'),('smtp_from_email','hello@finchskills.com','2026-09-21 08:07:35'),('smtp_from_name','Finchskills Institute','2026-09-21 07:58:42'),('smtp_host','mail.finchskills.com','2026-09-21 08:07:35'),('smtp_password','Finc@1234$','2026-09-21 08:07:35'),('smtp_port','465','2026-09-21 08:07:35'),('smtp_reply_to','hello@finchskills.com','2026-09-21 07:58:42'),('smtp_secure','ssl','2026-09-21 08:07:35'),('smtp_username','support@finchskills.com','2026-09-21 08:07:35'),('timing_mon_fri','Monday - Friday: 10:00 AM - 05:00 PM','2026-09-09 05:54:35'),('timing_sat','Saturday: 10:00 AM - 02:00 PM','2026-09-09 05:54:35'),('twitter_url','https://twitter.com/','2026-09-09 05:54:35'),('whatsapp_number','919650386711','2026-09-09 05:54:35'),('youtube_url','https://youtube.com/','2026-09-09 05:54:35');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_installments`
--

DROP TABLE IF EXISTS `student_installments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_installments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_db_id` int DEFAULT NULL,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` int NOT NULL,
  `course_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_course_fee` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `final_payable` decimal(10,2) NOT NULL,
  `payment_type` enum('full','installment') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full',
  `total_parts` int NOT NULL DEFAULT '1',
  `installment_no` int NOT NULL DEFAULT '1',
  `installment_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1st Installment (Admission Fee)',
  `installment_amount` decimal(10,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `status` enum('Paid','Pending','Overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `admission_id` (`admission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_installments`
--

LOCK TABLES `student_installments` WRITE;
/*!40000 ALTER TABLE `student_installments` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_installments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aadhaar` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `seat_status` enum('Pending','Reserved','Confirmed') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `payment_status` enum('Unpaid','Paid','Skipped') COLLATE utf8mb4_unicode_ci DEFAULT 'Unpaid',
  `payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT '0.00',
  `payment_date` datetime DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'FS-2026-0001','Nikhil Sharma','test.nikhil@example.com','+919876543210','4567 8901 2345',NULL,'$2y$10$tQpEUe0AtNfcosN8VKyHneDBQnd1H2KyVQuxCG12nWoHu/4Lkc8hq','776126','2026-09-21 08:29:50',1,'Confirmed','Paid','pay_test_6ab0e0fa3971a',999.00,'2026-09-21 13:17:06',NULL,NULL,'2026-09-21 07:47:06'),(2,'FS-2026-5097','Nikhil Singh Bhadauriya','nikhilsinghbhadauria93@gmail.com','7317788940','559687401324','uploads/students/std_1789976925_124.jpg','$2y$10$DvnjXS7OdygtbfOWdLWFj..8/XpDaM/3EtF59fbNCbXhjm4ZDHGbC','610870','2026-09-21 08:30:52',1,'Reserved','Skipped',NULL,0.00,NULL,NULL,NULL,'2026-09-21 07:48:46'),(3,'FS-2026-2546','Nikhil singh','nikhilbhadauriya.gspt@gmail.com','06676767677','559687401326','uploads/students/std_1790113330_601.png','$2y$10$XEDIn/Saujm.ijshlWayweUfjWXfxk7v/SMt8zaLKPNfZZL6x/fL.',NULL,'2026-09-22 21:57:10',1,'Reserved','Skipped',NULL,0.00,NULL,NULL,NULL,'2026-09-22 21:42:11');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'finchskills_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-23  4:30:39
