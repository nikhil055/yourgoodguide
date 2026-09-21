-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: finchskills_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mobile` varchar(25) DEFAULT NULL,
  `aadhaar` varchar(30) DEFAULT NULL,
  `dob` varchar(30) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `course` varchar(150) DEFAULT NULL,
  `education` varchar(150) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `post_office` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `marksheet10` varchar(255) DEFAULT NULL,
  `marksheet12` varchar(255) DEFAULT NULL,
  `aadhaar_card` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admissions`
--

LOCK TABLES `admissions` WRITE;
/*!40000 ALTER TABLE `admissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_inquiries`
--

DROP TABLE IF EXISTS `contact_inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('New','In Progress','Resolved') DEFAULT 'New',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `icon` varchar(60) DEFAULT 'fa-graduation-cap',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `duration` varchar(50) DEFAULT '6 Months',
  `study_mode` varchar(60) DEFAULT 'Online / Offline',
  `image` varchar(255) DEFAULT NULL,
  `short_desc` text DEFAULT NULL,
  `full_desc` longtext DEFAULT NULL,
  `what_you_learn` longtext DEFAULT NULL,
  `how_we_teach` longtext DEFAULT NULL,
  `career_roles` text DEFAULT NULL,
  `eligibility` varchar(255) DEFAULT '10+2 / Any Graduate',
  `certification` varchar(255) DEFAULT 'Industry Recognized Certification',
  `is_featured` tinyint(1) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
INSERT INTO `courses` VALUES (1,1,'Professional Course in Airport Management','airport-management-course','12 Months','Online / Hybrid Campus','img/airport-management-course.png','Equip yourself with essential skills in airport terminal operations, passenger services, security compliance, and airline coordination for successful careers in global aviation.','The Professional Course in Airport Management at Finchskills Institute is designed to give you a complete, practical understanding of modern airline and airport terminal operations. From check-in counters and baggage systems to airside safety and crisis handling, this course ensures you are completely job-ready for leading domestic and international airlines.','Airport Terminal Architecture & Passenger Flow\nPassenger Check-in Procedures & Boarding Management\nBaggage Handling, Screening & Reconciliation\nAviation Security (AVSEC), DGR & Safety Protocols\nAirline Customer Service Excellence & Conflict Handling\nFlight Dispatch Basics & Airport Emergency Procedures','Live Airport Mock Terminal Roleplays\nAirline Recruiter Interview Preparation\nInternational Grooming, Etiquette & Soft Skills\nDirect Industrial Visits & Expert Airline Masterclasses','Airport Ground Staff, Terminal Executive, Passenger Service Agent, Guest Relations Officer, Flight Dispatch Assistant','10+2 / Any Graduate (Age 18-27)','Professional Airport Operations Diploma',1,'active','2026-09-20 18:00:28'),(2,3,'Certificate Course in Travel & Air Ticketing','certificate-course-in-travel-air-ticketing','6 Months','Classroom with Live GDS Software','img/Certificate-Course.webp','Master Global Distribution Systems (GDS like Amadeus/Galileo), airfare calculations, PNR creation, and flight ticket issuance for instant airline hiring.','Become an expert airline ticketing professional by mastering industry-standard Global Distribution Systems (GDS) including Amadeus and Galileo. Learn flight searches, PNR creation, fare construction, mileage rules, airfare calculation, reissuance, cancellation, and refund management demanded by commercial airlines, online travel portals (OTAs), and corporate travel desks.\n\nThis practical hands-on training opens high-paying careers in airline reservation centers and multinational travel corporations.','GDS Architecture, Terminal Commands & Navigation (Amadeus / Galileo)\nFlight Availability Searches, Class of Service & Passenger PNR Generation\nAirfare Construction, Currency Conversion, Taxes & Baggage Allowances\nE-Ticket Issuance, Revalidation, Date Changes & Automated Reissuance\nTicket Cancellation, Refund Processing, Void Rules & MPD / EMD Issuance\nSpecial Service Requests (SSR), Frequent Flyer Accruals & Ancillary Bookings','Daily Hands-on Live Terminal Practice on Certified GDS Software\nReal-Time Airfare Calculation & Multi-City Itinerary Drills\nLive Ticket Booking, Exchange & Cancellation Case Studies\nDirect Industry Placement Drives with Commercial Airlines & Top OTAs','Air Ticketing Executive, GDS Reservation Specialist, Airline Customer Service Officer, Corporate Travel Consultant, Fare & Tariff Analyst','12th Pass or Graduate with basic computer typing knowledge.','Professional Certificate in GDS Air Ticketing & Global Reservations',1,'active','2026-09-20 18:00:28'),(3,2,'Diploma in Hospitality & Guest Relations','hospitality-guest-relations-diploma','12 Months','Classroom & Practical Labs','img/hospitality-management-course.png','Develop 5-star hospitality standards with comprehensive training in front office operations, guest service excellence, luxury dining etiquette, and accommodation management.','Step into the glamorous world of luxury hotels, 5-star resorts, and corporate concierge services. This diploma covers front desk systems, guest conflict resolution, VIP protocol management, and banquet operations.','Front Office Hierarchy & Property Management Systems (PMS)\nLuxury Guest Relations & VIP Protocol Management\nFood & Beverage Service Principles & Banqueting\nHousekeeping Operations & Room Inventory Control\nHigh-Impact Communication & Multilingual Basics\nComplaint Handling & Service Recovery Strategies','5-Star Hotel Mock Reception & Room Setup\nFine Dining Table Etiquette & Professional Grooming\nMock Interviews by Hotel HR Managers\nIndustry Internship & Placement Guarantee Support','Front Desk Executive, Guest Relations Associate, Concierge Specialist, F&B Supervisor, VIP Lounge Host','10+2 / Higher Secondary / Graduate','Professional Diploma in Hospitality Management',1,'active','2026-09-20 18:00:28'),(4,9,'Professional Course in Cruise Management','cruise-ship-operations-course','6 Months','Classroom & Maritime Simulation','img/Cruise-Management-course.jpg','Prepare for rewarding careers on international cruise lines with specialized training in shipboard hospitality, guest relations, safety protocols, and onboard crew operations.','Prepare for high-earning, tax-free international careers aboard luxury cruise liners, mega-ships, and international yachts. This comprehensive program covers shipboard guest relations, international maritime safety conventions (STCW guidelines), luxury dining operations, housekeeping management, and crew life protocols.\n\nOur instructors guide you through international cruise recruitment drives, visa medical standards, and maritime interview clearance.','Cruise Liner Operations, Deck Hierarchy & Maritime Terminology\nShipboard Guest Services, Concierge & Front Desk Operations\nInternational Maritime Safety Awareness & Emergency Procedures\nFine Dining & Food & Beverage Service Aboard Luxury Cruise Lines\nCross-Cultural Passenger Hospitality & Conflict Resolution\nSTCW Basic Safety Guidelines, US Visa (C1/D) Preparation & Crew Etiquette','Cruise Ship Mock Dining & Cabin Service Practical Drills\nMaritime Safety & Emergency Protocol Roleplay Simulations\nCross-Cultural Communication & International Etiquette Workshops\nDirect Interview Grooming for Global Cruise Line Recruitment Drives','Cruise Hospitality Associate, Shipboard Guest Relations Officer, F&B Service Attendant, Stateroom Host, Cruise Activity Coordinator','10+2 (12th Pass) or Hotel/Aviation Graduate. Age 18-30. Valid passport preferred.','Professional Diploma in International Cruise Ship Operations',1,'active','2026-09-20 18:00:28'),(5,5,'Professional Course in Air Hostess','professional-course-in-air-hostess','12 Months','Classroom & Cabin Labs','img/airhostess-course.png','Prepare for a successful career in aviation. Develop professional grooming, in-flight safety protocols, hospitality etiquette, and communication skills required to excel as cabin crew.','Prepare for a successful career in domestic and international commercial aviation with our premier Air Hostess & Cabin Crew training program. Develop professional grooming standards, in-flight passenger safety protocols, international hospitality etiquette, emergency situation management, and multilingual customer communication skills required to excel as elite cabin crew.\n\nOur curriculum is taught by former flight purser instructors and senior airline trainers, giving you hands-on exposure to simulated aircraft cabin environments.','Aviation Industry Overview & Commercial Aircraft Familiarization\nIn-Flight Safety Standards & Emergency Evacuation Protocols\nPassenger Service Excellence & First Aid Medical Training\nAviation English, Announcement Voice & International Communication\nIn-Flight Food, Wine & Luxury Beverage Service Standards\nExecutive Personality Grooming, Skin Care, Makeup & Hair Masterclass','Hands-on Cabin Mock-Up & Real-Life Emergency Drill Simulations\nProfessional Uniform, Hair, Makeup & High-Standard Grooming Workshops\nCustomer Conflict Resolution & De-escalation Roleplay Scenarios\nOne-on-One Airline Mock HR & GD Technical Rounds with Ex-Cabin Crew','Air Hostess, Cabin Crew, Flight Attendant, VIP Corporate Flight Purser, In-Flight Services Associate','10+2 (12th Pass) in any stream. Age 17-27 years. Pleasing personality & clear speech.','Certified Professional in Air Hostess & Cabin Crew Operations',1,'active','2026-09-20 18:21:58'),(6,6,'Professional Course in Ground Staff & Hospitality','ground-staff-hospitality-course','12 Months','Classroom & Airport Labs','img/Hospitality-course.png','Gain comprehensive training in airport terminal operations, passenger check-in handling, ramp coordination, security protocols, and luxury hospitality management.','Gain comprehensive practical training in airport terminal operations, departure control systems, passenger check-in handling, ramp coordination, baggage management, aviation security regulations, and high-touch hospitality. This course is specially tailored for aspirants aiming for prestigious airport roles across domestic and international airports.\n\nStudents receive practical instruction on real check-in terminals, customer service psychology, and ground handling coordination.','Airport Terminal Architecture & Passenger Flow Management\nDeparture Control Systems (DCS) & Boarding Gate Coordination\nBaggage Handling, Tagging & Lost and Found Systems (WorldTracer)\nAviation Security Protocols, DGR (Dangerous Goods Regulations) & Safety\nRamp Safety, Turnaround Coordination & Marshalling Basics\nVIP Lounge Hospitality & Special Assistance (PRM) Passenger Care','Simulated Airport Terminal & Boarding Gate Practical Drills\nLive Passenger Check-in Software & Baggage Tagging Training\nEmergency De-escalation & Passenger Assistance Roleplays\nMock Airport Interviews with Leading Domestic & Global Ground Handlers','Airport Ground Staff, Passenger Service Agent, Boarding Gate Officer, Customer Service Associate, Ramp Coordinator, VIP Airport Lounge Host','12th Pass or Any Graduate from a recognized board/university.','Professional Diploma in Airport Ground Operations & Hospitality',1,'active','2026-09-20 18:21:58'),(7,3,'Professional Course in Travel & Tourism','professional-course-in-travel-tourism','12 Months','Classroom & Online Hybrid','img/Diploma-travell.webp','Build a strong foundation in international tourism trends, tour itinerary planning, visa assistance, and customer relationship expertise for global travel agencies.','Build a strong foundation in international tourism trends, outbound & inbound tour itinerary planning, visa documentation, global geography, customer relationship management, and digital travel marketing. Learn how world-class travel agencies, Destination Management Companies (DMCs), and online travel portals (OTAs) operate.\n\nGain practical proficiency in designing customized international tour packages, luxury cruise bookings, and corporate business travel arrangements.','World Geography, Time Zones & Major Global Tourism Hotspots\nInbound & Outbound Tour Package Design & Costing\nInternational Visa Application Documentation & Consular Formalities\nHotel Contracting, Airport Transfers & Sightseeing Logistics\nCustomer Relationship Management for Travel Consultants\nDigital Travel Portals (OTAs), Online Booking Tools & Marketing','Live Itinerary Creation, Package Budgeting & Costing Projects\nCase Studies on Popular International Tourism Circuits\nRoleplay on Client Travel Consultations, Negotiation & Objection Handling\nIndustry Visits to Leading Tour Operators & Travel Management Companies','Tour Operations Executive, Travel Consultant, Holiday Package Planner, Visa Documentation Specialist, Destination Expert, Corporate Travel Desk Officer','12th Pass or Graduate in any stream with basic computer knowledge.','Certified Professional in Global Travel & Tourism Operations',1,'active','2026-09-20 18:21:58'),(8,7,'Foundation Course in Hotel Management','foundation-course-in-hotel-management','6 Months','Classroom & Hotel Labs','img/Hotel-Management-course.webp','Learn the fundamentals of hotel operations, front-office systems, housekeeping standards, food & beverage service, and guest relations for 5-star hotels and resorts.','Master the fundamentals of world-class hotel operations across four core hospitality pillars: Front Office Management, Food & Beverage Service, Housekeeping Operations, and Guest Relations. This intensive 6-month foundation program prepares students for immediate hiring across leading 5-star hotel chains, luxury resorts, and high-end banquet establishments.\n\nYou will master international etiquette, table service standards, front desk reservation systems, and professional hospitality communication.','Front Office Operations, Guest Check-in & Hotel PMS Software\nFood & Beverage Service Etiquette, Banquet Operations & Table Layouts\nHousekeeping Standards, Room Preparation, Linen & Quality Audits\nGuest Relations, Concierge Assistance & VIP Protocol Handling\nBeverage Fundamentals, Service Hygiene & Sanitation Practices\nHospitality Spoken English, Grooming & Customer Care Excellence','Hands-on Fine Dining Table Setting & F&B Service Lab Sessions\nLive Hotel Front Desk Simulation & Reservation Software Roleplay\nHousekeeping Room Inspection Drills & Hospitality Cleanliness Audits\n5-Star Hotel Exposure Visits & Interactive Masterclasses with General Managers','Front Office Executive, Guest Relations Associate, F&B Service Steward, Banquet Host, Housekeeping Supervisor, Resort Concierge','10th Pass or 12th Pass in any discipline.','Foundation Certificate in Hotel Operations & Hospitality Management',1,'active','2026-09-20 18:21:58'),(9,8,'Professional Course in Personality Development','professional-course-in-personality-development','3 Months','Classroom & Interactive Masterclasses','img/personality-development-course.webp','Elevate your career presence with corporate etiquette, spoken English fluency, body language refinement, and rigorous mock interview simulations.','Transform your professional presence, boost self-confidence, and master high-impact executive communication. Specially designed for job aspirants preparing for competitive airline, hospitality, and corporate interviews, this course refines spoken English fluency, body language, corporate dining etiquette, active listening, and interview clearing strategies.\n\nStudents undergo intensive video-recorded mock interview panels and personalized voice-and-accent refinement sessions.','Spoken English Fluency, Pronunciation & Accent Neutralization\nProfessional Body Language, Posture, Eye Contact & Hand Gestures\nCorporate Dressing Standards, Wardrobe Planning, Makeup & Personal Grooming\nPublic Speaking Confidence, Overcoming Hesitation & Stage Fear\nGroup Discussion (GD) Strategies, Moderation & Leadership Presence\nHR Interview Clearing Techniques, Behavioral Answers & Resume Optimization','Daily Video-Recorded Speaking & Body Language Review Sessions\nExtensive Group Discussions, Debates & Impromptu Speech Drills\nProfessional Personal Grooming & Executive Styling Workshops\nSimulated HR & Technical Panel Mock Interviews with Constructive Feedback','Customer Service Executive, Corporate PR Associate, Front Desk Coordinator, Airline Customer Care Officer, Corporate Host','Open to all students (10th, 12th, or Graduates) seeking career transformation.','Executive Certificate in Corporate Personality & Communication',1,'active','2026-09-20 18:21:58');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_submissions`
--

DROP TABLE IF EXISTS `fee_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `contact` varchar(25) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `fee_type` varchar(100) DEFAULT NULL,
  `purpose` varchar(200) DEFAULT NULL,
  `course` varchar(150) DEFAULT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES ('address','Orbit Plaza, Crossing Republik, NH-24, Ghaziabad, Uttar Pradesh, 201016','2026-09-09 05:54:35'),('email_1','hello@finchskills.com','2026-09-09 05:54:35'),('email_2','admission@finchskills.com','2026-09-09 05:54:35'),('facebook_url','https://facebook.com/','2026-09-09 05:54:35'),('footer_logo','img/footer-logo.png','2026-09-09 05:54:35'),('instagram_url','https://instagram.com/','2026-09-09 05:54:35'),('linkedin_url','https://linkedin.com/','2026-09-09 05:54:35'),('map_iframe','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.9501409758395!2d77.43262847457278!3d28.63125638414035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cee300c363997%3A0xe53a3aaacf648c83!2sOrbit%20plaza%2C%20Crossings%20Republik%2C%20Ghaziabad%2C%20Uttar%20Pradesh%20201016!5e0!3m2!1sen!2sin!4v1784362846035!5m2!1sen!2sin','2026-09-09 05:54:35'),('phone_1','+919650386711','2026-09-09 05:54:35'),('phone_2','+919876543210','2026-09-09 05:54:35'),('razorpay_enabled','1','2026-09-21 07:39:02'),('razorpay_live_key_id','','2026-09-21 07:39:02'),('razorpay_live_key_secret','','2026-09-21 07:39:02'),('razorpay_mode','test','2026-09-21 07:39:02'),('razorpay_test_key_id','rzp_test_1DP5mmOlF5G5ag','2026-09-21 07:39:02'),('razorpay_test_key_secret','s98s8F7d6g5H4j3K2l1P0o','2026-09-21 07:39:02'),('seat_booking_fee','999','2026-09-21 07:39:02'),('site_favicon','img/favicon.png','2026-09-09 05:54:35'),('site_logo','img/logo.png','2026-09-09 05:54:35'),('smtp_enabled','1','2026-09-21 07:58:42'),('smtp_from_email','hello@finchskills.com','2026-09-21 08:07:35'),('smtp_from_name','Finchskills Institute','2026-09-21 07:58:42'),('smtp_host','mail.finchskills.com','2026-09-21 08:07:35'),('smtp_password','Finc@1234$','2026-09-21 08:07:35'),('smtp_port','465','2026-09-21 08:07:35'),('smtp_reply_to','hello@finchskills.com','2026-09-21 07:58:42'),('smtp_secure','ssl','2026-09-21 08:07:35'),('smtp_username','support@finchskills.com','2026-09-21 08:07:35'),('timing_mon_fri','Monday - Friday: 10:00 AM - 05:00 PM','2026-09-09 05:54:35'),('timing_sat','Saturday: 10:00 AM - 02:00 PM','2026-09-09 05:54:35'),('twitter_url','https://twitter.com/','2026-09-09 05:54:35'),('whatsapp_number','919650386711','2026-09-09 05:54:35'),('youtube_url','https://youtube.com/','2026-09-09 05:54:35');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `aadhaar` varchar(30) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `seat_status` enum('Pending','Reserved','Confirmed') DEFAULT 'Pending',
  `payment_status` enum('Unpaid','Paid','Skipped') DEFAULT 'Unpaid',
  `payment_id` varchar(100) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT 0.00,
  `payment_date` datetime DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'FS-2026-0001','Nikhil Sharma','test.nikhil@example.com','+919876543210','4567 8901 2345',NULL,'$2y$10$tQpEUe0AtNfcosN8VKyHneDBQnd1H2KyVQuxCG12nWoHu/4Lkc8hq','776126','2026-09-21 08:29:50',1,'Confirmed','Paid','pay_test_6ab0e0fa3971a',999.00,'2026-09-21 13:17:06',NULL,NULL,'2026-09-21 07:47:06'),(2,'FS-2026-5097','Nikhil Singh Bhadauriya','nikhilsinghbhadauria93@gmail.com','7317788940','559687401324','uploads/students/std_1789976925_124.jpg','$2y$10$DvnjXS7OdygtbfOWdLWFj..8/XpDaM/3EtF59fbNCbXhjm4ZDHGbC','610870','2026-09-21 08:30:52',1,'Reserved','Skipped',NULL,0.00,NULL,NULL,NULL,'2026-09-21 07:48:46');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-21 13:48:23
