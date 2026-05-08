-- GoldenEye Academy Hostinger database import
-- Base export: goldeneye_hostinger_release_20260504-080254.sql
-- Patch: goldeneye_hostinger_patch_display_order_20260504.sql
-- Import this file once into Hostinger phpMyAdmin.


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
DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_status_index` (`status`),
  KEY `blog_posts_slug_index` (`slug`),
  KEY `blog_posts_published_at_index` (`published_at`),
  KEY `blog_posts_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'Which Course Should I Choose After SEE or Plus Two?','which-course-should-i-choose-after-see-or-plus-two','<p>Choosing a course after SEE or Plus Two should start with your goal, timeline, budget, and current skill level. Students should compare study abroad requirements, language needs, computer confidence, and job-readiness before enrolling.</p><p>At GoldenEye Academy, simple guidance comes before course selection. This helps students avoid random enrollment and choose a practical next step.</p>','site/img/carousel-1.png','GoldenEye Course Help Team','Student Guide','Which Course Should I Choose After SEE or Plus Two? | GoldenEye Academy','Choosing a course after SEE or Plus Two should start with your goal, timeline, budget, and current skill level. Students should compare study abroad requir','after SEE course, after plus two course, student guidance Nepal','Choosing a course after SEE or Plus Two should start with your goal, timeline, budget, and current skill level. Students should compare study abroad requirements, language needs, computer confidence, and job-readiness before enrolling.At Go','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Which Course Should I Choose After SEE or Plus Two?\",\"author\":\"GoldenEye Course Help Team\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-05-02 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(2,'IELTS or PTE: How to Choose the Right Test','ielts-or-pte-how-to-choose-the-right-test','<p>IELTS is widely accepted and includes a human speaking interview. PTE is computer-based, often faster for results, and preferred by students who are comfortable with typed and recorded responses.</p><p>The right choice depends on destination, university requirement, timeline, confidence level, and test style.</p>','site/img/ielts-preparation.jpg','GoldenEye Test Prep Team','Study Abroad','IELTS or PTE: How to Choose the Right Test | GoldenEye Academy','IELTS is widely accepted and includes a human speaking interview. PTE is computer-based, often faster for results, and preferred by students who are comfor','IELTS vs PTE, PTE Pokhara, IELTS Pokhara','IELTS is widely accepted and includes a human speaking interview. PTE is computer-based, often faster for results, and preferred by students who are comfortable with typed and recorded responses.The right choice depends on destination, univ','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"IELTS or PTE: How to Choose the Right Test\",\"author\":\"GoldenEye Test Prep Team\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-05-01 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(3,'Why Office Skills Still Matter for Job Seekers','why-office-skills-still-matter-for-job-seekers','<p>Office skills are still a practical entry point for many job seekers. Word, Excel, PowerPoint, email, internet research, and document handling are basic expectations in admin, front desk, accounting support, and office roles.</p><p>A focused office package can quickly improve confidence for interviews and workplace tasks.</p>','site/img/computer-office-package.jpg','GoldenEye Career Team','Career Skills','Why Office Skills Still Matter for Job Seekers | GoldenEye Academy','Office skills are still a practical entry point for many job seekers. Word, Excel, PowerPoint, email, internet research, and document handling are basic ex','office package Pokhara, computer course Nepal, Excel training','Office skills are still a practical entry point for many job seekers. Word, Excel, PowerPoint, email, internet research, and document handling are basic expectations in admin, front desk, accounting support, and office roles.A focused offic','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Why Office Skills Still Matter for Job Seekers\",\"author\":\"GoldenEye Career Team\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-04-30 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(4,'How Web Development Builds a Career Portfolio','how-web-development-builds-a-career-portfolio','<p>Web development is valuable because students can show what they can build. A portfolio with landing pages, dashboards, forms, database projects, and deployed work is stronger than a certificate alone.</p><p>GoldenEye focuses on practical projects so learners can explain their work with confidence.</p>','site/img/basic-web-development.jpg','GoldenEye IT Faculty','IT Career','How Web Development Builds a Career Portfolio | GoldenEye Academy','Web development is valuable because students can show what they can build. A portfolio with landing pages, dashboards, forms, database projects, and deploy','web development course Pokhara, Laravel course Nepal, coding portfolio','Web development is valuable because students can show what they can build. A portfolio with landing pages, dashboards, forms, database projects, and deployed work is stronger than a certificate alone.GoldenEye focuses on practical projects','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"How Web Development Builds a Career Portfolio\",\"author\":\"GoldenEye IT Faculty\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-04-29 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(5,'Korean EPS-TOPIK Preparation: What Beginners Should Know','korean-eps-topik-preparation-what-beginners-should-know','<p>EPS-TOPIK preparation requires consistent vocabulary, listening practice, reading speed, and exam-style discipline. Beginners should first master Hangul and then build daily study habits.</p><p>Structured classes help learners avoid random memorization and stay aligned with exam needs.</p>','site/img/eps-topik.jpg','GoldenEye Korean Faculty','Language','Korean EPS-TOPIK Preparation: What Beginners Should Know | GoldenEye Academy','EPS-TOPIK preparation requires consistent vocabulary, listening practice, reading speed, and exam-style discipline. Beginners should first master Hangul an','EPS TOPIK Nepal, Korean class Pokhara, Korean language','EPS-TOPIK preparation requires consistent vocabulary, listening practice, reading speed, and exam-style discipline. Beginners should first master Hangul and then build daily study habits.Structured classes help learners avoid random memoriz','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Korean EPS-TOPIK Preparation: What Beginners Should Know\",\"author\":\"GoldenEye Korean Faculty\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-04-28 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(6,'Japanese JLPT N5: A Practical Starting Plan','japanese-jlpt-n5-a-practical-starting-plan','<p>JLPT N5 starts with Hiragana, Katakana, basic Kanji, daily vocabulary, and simple grammar. Students should focus on small daily practice instead of waiting for long study sessions.</p><p>A good N5 plan balances reading, listening, writing, and conversation exposure.</p>','site/img/jlpt-n5.jpg','GoldenEye Japanese Faculty','Language','Japanese JLPT N5: A Practical Starting Plan | GoldenEye Academy','JLPT N5 starts with Hiragana, Katakana, basic Kanji, daily vocabulary, and simple grammar. Students should focus on small daily practice instead of waiting','JLPT N5 Pokhara, Japanese language Nepal, Japan study','JLPT N5 starts with Hiragana, Katakana, basic Kanji, daily vocabulary, and simple grammar. Students should focus on small daily practice instead of waiting for long study sessions.A good N5 plan balances reading, listening, writing, and con','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Japanese JLPT N5: A Practical Starting Plan\",\"author\":\"GoldenEye Japanese Faculty\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-04-27 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(7,'Parents Guide: How to Evaluate a Training Institute','parents-guide-how-to-evaluate-a-training-institute','<p>Parents should evaluate an institute by guidance quality, teacher experience, class structure, practical outcomes, communication, and follow-up support. The best decision is not always the cheapest or fastest option.</p><p>Ask what the student will be able to do after the course and how progress will be tracked.</p>','site/img/about.jpg','GoldenEye Academic Team','Parent Guide','Parents Guide: How to Evaluate a Training Institute | GoldenEye Academy','Parents should evaluate an institute by guidance quality, teacher experience, class structure, practical outcomes, communication, and follow-up support. Th','parent guide training institute, course guidance Nepal','Parents should evaluate an institute by guidance quality, teacher experience, class structure, practical outcomes, communication, and follow-up support. The best decision is not always the cheapest or fastest option.Ask what the student wil','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Parents Guide: How to Evaluate a Training Institute\",\"author\":\"GoldenEye Academic Team\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-04-26 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36'),(8,'Why You Should Ask Before Enrollment','why-you-should-ask-before-enrollment','<p>Good guidance reduces wrong enrollment. Students often know they want improvement but are unsure whether they need language, computer, test prep, or career support first.</p><p>A short roadmap conversation helps align the course with the student goal.</p>','site/img/cat-4.jpg','GoldenEye Admissions Team','Admissions','Why You Should Ask Before Enrollment | GoldenEye Academy','Good guidance reduces wrong enrollment. Students often know they want improvement but are unsure whether they need language, computer, test prep, or career','course guidance Pokhara, free course help Nepal, admissions guidance','Good guidance reduces wrong enrollment. Students often know they want improvement but are unsure whether they need language, computer, test prep, or career support first.A short roadmap conversation helps align the course with the student g','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"BlogPosting\",\"headline\":\"Why You Should Ask Before Enrollment\",\"author\":\"GoldenEye Admissions Team\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','published','2026-04-25 20:32:36','2026-05-03 20:32:36','2026-05-03 20:32:36');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `landing_page` varchar(255) DEFAULT NULL,
  `cta_id` varchar(255) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_lead_source_index` (`lead_source`),
  KEY `contacts_cta_id_index` (`cta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
DROP TABLE IF EXISTS `course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `order_priority` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_categories_slug_unique` (`slug`),
  KEY `course_categories_status_index` (`status`),
  KEY `course_categories_slug_index` (`slug`),
  KEY `course_categories_order_priority_index` (`order_priority`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `course_categories` DISABLE KEYS */;
INSERT INTO `course_categories` VALUES (1,'Study Abroad Test Prep','study-abroad-test-prep','active',10,'site/img/ielts-preparation.jpg','IELTS, PTE, SAT, language, and destination-readiness programs for students planning global education or migration.','Study Abroad Test Prep in Pokhara | GoldenEye Academy','Prepare for IELTS, PTE, SAT, Japanese, Korean, and global admission pathways with GoldenEye Academy.','IELTS Pokhara, PTE Pokhara, study abroad Nepal, Japanese language, Korean language','GoldenEye Academy helps students choose and prepare for the right language or test-prep pathway for study abroad.',NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(2,'Computer and Office Skills','computer-classes','active',20,'site/img/computer-office-package.jpg','Practical computer, office package, Excel, documentation, and digital productivity programs for students and job seekers.','Computer and Office Skills Course in Pokhara | GoldenEye Academy','Learn practical computer and office skills including Word, Excel, PowerPoint, email, internet, and workplace productivity.','computer course Pokhara, office package Nepal, Excel training Pokhara','The Computer and Office Skills category prepares learners for daily workplace tasks and stronger job readiness.',NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(3,'Web Development and IT Career','web-development-it-career','active',30,'site/img/basic-web-development.jpg','Project-based coding, Laravel, web development, and software-career programs for beginners and upskilling learners.','Web Development Course in Pokhara | GoldenEye Academy','Build job-ready web development skills with practical HTML, CSS, Laravel, database, API, and deployment training.','web development Pokhara, Laravel course Nepal, coding class Pokhara','GoldenEye Academy offers practical web development training for learners who want a software career path.',NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(4,'Global Language Academy','language-classes','active',40,'site/img/advanced-english.jpg','English communication, Japanese, Korean, Chinese, and professional language classes for academic and career mobility.','Language Classes in Pokhara | GoldenEye Academy','Learn English, Japanese, Korean, and Chinese with practical language classes for study, work, and migration goals.','language classes Pokhara, Korean class Nepal, Japanese class Pokhara, English speaking course','The Global Language Academy category supports students and professionals who need stronger communication skills.',NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(5,'Course Guidance and Events','other-classes','active',50,'site/img/cat-4.jpg','Course guidance, workshops, admissions help, career mapping, and short skill sessions for undecided learners.','Course Guidance and Events in Pokhara | GoldenEye Academy','Ask about course guidance, workshops, and events that help students, parents, and job seekers choose the next right step.','career guidance Pokhara, student guidance Nepal, career events Pokhara','GoldenEye Academy provides course guidance and events for learners who need clarity before choosing a program.',NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35');
/*!40000 ALTER TABLE `course_categories` ENABLE KEYS */;
DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `badge_text` varchar(255) DEFAULT 'Job Oriented',
  `slug` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `course_outline` longtext NOT NULL,
  `rating_star` varchar(255) NOT NULL,
  `rating_count` varchar(255) NOT NULL,
  `capacity` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `instructor` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `category_slug` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_slug_unique` (`slug`),
  KEY `courses_status_index` (`status`),
  KEY `courses_is_featured_index` (`is_featured`),
  KEY `courses_category_id_index` (`category_id`),
  CONSTRAINT `courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,'IELTS Masterclass for Band 7+','Top Rated','ielts-masterclass','Planning abroad but unsure how to reach your target band? This IELTS Masterclass turns confusion into a weekly score plan with mock tests, writing correction, speaking practice, and personal feedback. Best for students who want a clear route before paying for applications, documents, or visa processing.','IELTS format and scoring\nListening strategy and note-taking\nReading speed and accuracy\nWriting Task 1 and Task 2 structures\nSpeaking confidence labs\nWeekly mock tests and score review\nDestination and timeline planning','4.9','342','15 Seats','site/img/ielts-preparation.jpg','Rs. 7,000','6 Weeks','Aakash Subedi','Study Abroad Test Prep','study-abroad-test-prep',1,'active',1,'IELTS Masterclass for Band 7+ | GoldenEye Academy','Planning abroad but unsure how to reach your target band? This IELTS Masterclass turns confusion into a weekly score plan with mock tests, writing correction, speaking practice, and personal feedback. Best for students who want a clear route before paying for applications, documents, or visa processing.','IELTS Pokhara, IELTS preparation Nepal, Band 7 IELTS','IELTS Masterclass for Band 7+ helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"IELTS Masterclass for Band 7+\",\"description\":\"Planning abroad but unsure how to reach your target band? This IELTS Masterclass turns confusion into a weekly score plan with mock tests, writing correction, speaking practice, and personal feedback. Best for students who want a clear route before paying for applications, documents, or visa processing.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(2,'PTE Elite Academic Training','Fast Track','pte-elite-training','Need a faster computer-based English test route? PTE Elite trains you around the actual AI-scored exam experience: templates, timing, fluency, and mock analysis. Best for Australia-focused or deadline-driven students who want precision instead of random practice.','PTE test interface\nAI scoring essentials\nSpeaking fluency drills\nWriting templates\nReading and listening practice\nMock test analysis\nScore improvement roadmap','4.8','215','10 Seats','site/img/pte-preparation.jpg','Rs. 7,000','6 Weeks','Aakash Subedi','Study Abroad Test Prep','study-abroad-test-prep',1,'active',1,'PTE Elite Academic Training | GoldenEye Academy','Need a faster computer-based English test route? PTE Elite trains you around the actual AI-scored exam experience: templates, timing, fluency, and mock analysis. Best for Australia-focused or deadline-driven students who want precision instead of random practice.','PTE Pokhara, PTE class Nepal, PTE Academic training','PTE Elite Academic Training helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"PTE Elite Academic Training\",\"description\":\"Need a faster computer-based English test route? PTE Elite trains you around the actual AI-scored exam experience: templates, timing, fluency, and mock analysis. Best for Australia-focused or deadline-driven students who want precision instead of random practice.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(3,'Japanese Proficiency JLPT N5','Japan Pathway','jlpt-n5-elite','Starting your Japan journey from zero? JLPT N5 gives you the foundation: Hiragana, Katakana, basic Kanji, grammar, listening, and cultural confidence. Best for students who want structure before moving into higher JLPT levels, study pathways, or Japan work preparation.','Hiragana and Katakana\nN5 vocabulary and grammar\nBasic Kanji\nListening and conversation practice\nJapanese culture and etiquette\nJLPT mock preparation','4.7','189','15 Seats','site/img/jlpt-n5.jpg','Rs. 15,000','6 Months','Navaraj Thapa','Global Language Academy','language-classes',4,'active',1,'Japanese Proficiency JLPT N5 | GoldenEye Academy','Starting your Japan journey from zero? JLPT N5 gives you the foundation: Hiragana, Katakana, basic Kanji, grammar, listening, and cultural confidence. Best for students who want structure before moving into higher JLPT levels, study pathways, or Japan work preparation.','JLPT N5 Pokhara, Japanese class Nepal, Japan language course','Japanese Proficiency JLPT N5 helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Japanese Proficiency JLPT N5\",\"description\":\"Starting your Japan journey from zero? JLPT N5 gives you the foundation: Hiragana, Katakana, basic Kanji, grammar, listening, and cultural confidence. Best for students who want structure before moving into higher JLPT levels, study pathways, or Japan work preparation.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(4,'Japanese Proficiency JLPT N4','Language Plus','jlpt-n4','Already know the basics but not ready for real Japanese conversations yet? JLPT N4 builds vocabulary, Kanji, grammar, reading, and listening so learners can move from memorizing scripts to understanding everyday Japanese with more confidence.','N4 grammar patterns\nExpanded vocabulary\nKanji practice\nReading short passages\nListening comprehension\nConversation drills\nJLPT preparation strategy','4.6','88','15 Seats','site/img/jlpt-n4.jpg','Rs. 13,000','3 Months','Navaraj Thapa','Global Language Academy','language-classes',4,'active',0,'Japanese Proficiency JLPT N4 | GoldenEye Academy','Already know the basics but not ready for real Japanese conversations yet? JLPT N4 builds vocabulary, Kanji, grammar, reading, and listening so learners can move from memorizing scripts to understanding everyday Japanese with more confidence.','JLPT N4 Pokhara, Japanese language Nepal','Japanese Proficiency JLPT N4 helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Japanese Proficiency JLPT N4\",\"description\":\"Already know the basics but not ready for real Japanese conversations yet? JLPT N4 builds vocabulary, Kanji, grammar, reading, and listening so learners can move from memorizing scripts to understanding everyday Japanese with more confidence.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(5,'Professional Korean EPS-TOPIK','High Success','professional-korean-eps','Aiming for South Korea but overwhelmed by EPS-TOPIK preparation? This program gives learners a disciplined Korean study route with Hangul, high-frequency vocabulary, listening, reading, and exam-style practice for work-focused applicants.','Hangul foundation\nEPS vocabulary\nListening drills\nReading comprehension\nIndustry terms\nMock tests\nApplication timeline planning','4.8','276','40 Seats','site/img/eps-topik.jpg','Rs. 18,000','6 Months','Pradeep Paudel','Global Language Academy','language-classes',4,'active',1,'Professional Korean EPS-TOPIK | GoldenEye Academy','Aiming for South Korea but overwhelmed by EPS-TOPIK preparation? This program gives learners a disciplined Korean study route with Hangul, high-frequency vocabulary, listening, reading, and exam-style practice for work-focused applicants.','Korean class Pokhara, EPS TOPIK Nepal, Korean language','Professional Korean EPS-TOPIK helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Professional Korean EPS-TOPIK\",\"description\":\"Aiming for South Korea but overwhelmed by EPS-TOPIK preparation? This program gives learners a disciplined Korean study route with Hangul, high-frequency vocabulary, listening, reading, and exam-style practice for work-focused applicants.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(6,'Basic Korean Course','Beginner Friendly','basic-korean-course','Want to learn Korean without feeling lost in the alphabet? This beginner course helps learners read Hangul, speak simple sentences, understand daily vocabulary, and build the base needed for culture, study, or future EPS-TOPIK preparation.','Hangul reading and writing\nBasic vocabulary\nSimple sentence patterns\nDaily conversation\nListening practice\nKorean culture basics','4.5','96','40 Seats','site/img/basic-korean.jpg','Rs. 10,000','3 Months','Pradeep Paudel','Global Language Academy','language-classes',4,'active',0,'Basic Korean Course | GoldenEye Academy','Want to learn Korean without feeling lost in the alphabet? This beginner course helps learners read Hangul, speak simple sentences, understand daily vocabulary, and build the base needed for culture, study, or future EPS-TOPIK preparation.','basic Korean Pokhara, Korean language Nepal','Basic Korean Course helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Basic Korean Course\",\"description\":\"Want to learn Korean without feeling lost in the alphabet? This beginner course helps learners read Hangul, speak simple sentences, understand daily vocabulary, and build the base needed for culture, study, or future EPS-TOPIK preparation.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(7,'Global English Professional Track','Communication','global-english-pro','If English is blocking your interview, presentation, class participation, or confidence, this track is built for you. Learn practical speaking, writing, correction, and presentation habits that help students and professionals communicate clearly.','Grammar correction\nSpeaking confidence\nPublic speaking\nProfessional writing\nInterview practice\nPresentation skills\nCross-cultural communication','4.9','145','15 Seats','site/img/advanced-english.jpg','Rs. 7,000','45 Days','Ajay B.K.','Global Language Academy','language-classes',4,'active',1,'Global English Professional Track | GoldenEye Academy','If English is blocking your interview, presentation, class participation, or confidence, this track is built for you. Learn practical speaking, writing, correction, and presentation habits that help students and professionals communicate clearly.','English speaking Pokhara, professional English Nepal','Global English Professional Track helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Global English Professional Track\",\"description\":\"If English is blocking your interview, presentation, class participation, or confidence, this track is built for you. Learn practical speaking, writing, correction, and presentation habits that help students and professionals communicate clearly.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(8,'Basic English Foundation','Foundation','basic-english-course','Starting English again from the basics? This foundation course rebuilds grammar, vocabulary, reading, writing, listening, and everyday speaking so learners can stop guessing and start communicating with confidence.','English basics\nVocabulary building\nGrammar foundations\nListening comprehension\nReading practice\nWriting practice\nDaily speaking drills','4.5','80','15 Seats','site/img/basic-english.jpg','Rs. 5,000','45 Days','Ajay B.K.','Global Language Academy','language-classes',4,'active',0,'Basic English Foundation | GoldenEye Academy','Starting English again from the basics? This foundation course rebuilds grammar, vocabulary, reading, writing, listening, and everyday speaking so learners can stop guessing and start communicating with confidence.','basic English Pokhara, English course Nepal','Basic English Foundation helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Basic English Foundation\",\"description\":\"Starting English again from the basics? This foundation course rebuilds grammar, vocabulary, reading, writing, listening, and everyday speaking so learners can stop guessing and start communicating with confidence.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(9,'Professional Web Development','Job Ready','professional-web-development','Want a skill you can actually show? Professional Web Development turns beginners into project builders with HTML, CSS, responsive UI, Laravel, databases, APIs, and deployment basics. Best for learners who want portfolio proof, not only theory.','HTML and CSS foundations\nResponsive UI basics\nLaravel MVC architecture\nRouting and middleware\nDatabase design\nAuthentication concepts\nREST API fundamentals\nLive project deployment','4.9','128','10 Seats','site/img/basic-web-development.jpg','Rs. 24,850','3 Months','Prasun Paudel','Web Development and IT Career','web-development-it-career',3,'active',1,'Professional Web Development | GoldenEye Academy','Want a skill you can actually show? Professional Web Development turns beginners into project builders with HTML, CSS, responsive UI, Laravel, databases, APIs, and deployment basics. Best for learners who want portfolio proof, not only theory.','web development Pokhara, Laravel course Nepal, coding course','Professional Web Development helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Professional Web Development\",\"description\":\"Want a skill you can actually show? Professional Web Development turns beginners into project builders with HTML, CSS, responsive UI, Laravel, databases, APIs, and deployment basics. Best for learners who want portfolio proof, not only theory.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(10,'Advanced Diploma in Computer Science','Career Starter','advanced-computer-diploma','Need computer confidence for study, office work, or job applications? This diploma builds practical command over documents, Excel, presentations, databases, email, and daily digital workflows so learners can perform in real office situations.','Computer fundamentals\nWindows productivity\nAdvanced Word\nExcel formulas and reporting\nPowerPoint presentations\nMS Access basics\nEmail and internet workflow\nProfessional document handling','4.8','156','10 Seats','site/img/computer-diploma.jpg','Rs. 7,000','3 Months','Surendra Bhattarai','Computer and Office Skills','computer-classes',2,'active',1,'Advanced Diploma in Computer Science | GoldenEye Academy','Need computer confidence for study, office work, or job applications? This diploma builds practical command over documents, Excel, presentations, databases, email, and daily digital workflows so learners can perform in real office situations.','computer diploma Pokhara, computer course Nepal','Advanced Diploma in Computer Science helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Advanced Diploma in Computer Science\",\"description\":\"Need computer confidence for study, office work, or job applications? This diploma builds practical command over documents, Excel, presentations, databases, email, and daily digital workflows so learners can perform in real office situations.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(11,'Corporate Office and Admin Package','Most Popular','corporate-office-package','Applying for office, admin, front desk, or support roles? This package gives job seekers the practical Word, Excel, PowerPoint, email, and file-management skills employers expect from day one.','MS Word documentation\nExcel data handling\nPowerPoint presentation design\nEmail etiquette\nCloud collaboration\nOffice file management\nWorkplace digital habits','4.7','312','10 Seats','site/img/computer-office-package.jpg','Rs. 7,000','3 Months','Surendra Bhattarai','Computer and Office Skills','computer-classes',2,'active',1,'Corporate Office and Admin Package | GoldenEye Academy','Applying for office, admin, front desk, or support roles? This package gives job seekers the practical Word, Excel, PowerPoint, email, and file-management skills employers expect from day one.','office package Pokhara, Excel class Nepal, computer skills','Corporate Office and Admin Package helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Corporate Office and Admin Package\",\"description\":\"Applying for office, admin, front desk, or support roles? This package gives job seekers the practical Word, Excel, PowerPoint, email, and file-management skills employers expect from day one.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(12,'Chinese Language Starter','New Language','chinese-language-course','Curious about Chinese for study, work, travel, or business exposure? This starter course introduces pronunciation, vocabulary, basic conversation, listening, and cultural etiquette in a beginner-safe format.','Chinese pronunciation\nBasic vocabulary\nListening practice\nReading basics\nSpeaking drills\nCultural etiquette\nMock conversations','4.7','60','15 Seats','site/img/chinese-course.jpg','Rs. 15,000','1 Month','Chham Maya Rai','Global Language Academy','language-classes',4,'active',0,'Chinese Language Starter | GoldenEye Academy','Curious about Chinese for study, work, travel, or business exposure? This starter course introduces pronunciation, vocabulary, basic conversation, listening, and cultural etiquette in a beginner-safe format.','Chinese language Pokhara, Mandarin class Nepal','Chinese Language Starter helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Chinese Language Starter\",\"description\":\"Curious about Chinese for study, work, travel, or business exposure? This starter course introduces pronunciation, vocabulary, basic conversation, listening, and cultural etiquette in a beginner-safe format.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35'),(13,'Free Course Roadmap Help','Guidance First','free-course-roadmap-help','Not sure what to study next? Do not guess. This free course-help chat maps your goal, current level, budget, timeline, and best-fit course so students, parents, job seekers, and abroad applicants can decide with clarity.','Goal diagnosis\nCurrent level review\nCourse comparison\nStudy abroad or job path guidance\nTimeline planning\nRecommended next step\nFollow-up contact plan','5.0','94','Limited Slots','site/img/cat-4.jpg','Free','30 Minutes','Course Help Team','Course Guidance and Events','other-classes',5,'active',1,'Free Course Roadmap Help | GoldenEye Academy','Not sure what to study next? Do not guess. This free course-help chat maps your goal, current level, budget, timeline, and best-fit course so students, parents, job seekers, and abroad applicants can decide with clarity.','free course help Pokhara, course guidance Nepal, career guidance','Free Course Roadmap Help helps learners move from interest to a practical next step through guided training at GoldenEye Academy.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Course\",\"name\":\"Free Course Roadmap Help\",\"description\":\"Not sure what to study next? Do not guess. This free course-help chat maps your goal, current level, budget, timeline, and best-fit course so students, parents, job seekers, and abroad applicants can decide with clarity.\",\"provider\":{\"@type\":\"Organization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:35','2026-05-03 20:32:35');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
DROP TABLE IF EXISTS `f_a_q_s`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f_a_q_s` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `order_priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `f_a_q_s` DISABLE KEYS */;
INSERT INTO `f_a_q_s` VALUES (1,'What courses does GoldenEye Academy offer?','GoldenEye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, and free course-roadmap help.','active','What courses does GoldenEye Academy offer? | GoldenEye Academy FAQ','GoldenEye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, and free course-roadmap help.','GoldenEye Academy FAQ, What courses does GoldenEye Academy offer?','GoldenEye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, and free course-roadmap help.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"What courses does GoldenEye Academy offer?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"GoldenEye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, and free course-roadmap help.\"}}',10,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(2,'Can I ask for help before choosing a course?','Yes. Students, parents, job seekers, and study abroad applicants can ask for quick course help before choosing a course.','active','Can I ask for help before choosing a course? | GoldenEye Academy FAQ','Yes. Students, parents, job seekers, and study abroad applicants can ask for quick course help before choosing a course.','GoldenEye Academy FAQ, Can I ask for help before choosing a course?','Yes. Students, parents, job seekers, and study abroad applicants can ask for quick course help before choosing a course.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Can I ask for help before choosing a course?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Students, parents, job seekers, and study abroad applicants can ask for quick course help before choosing a course.\"}}',20,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(3,'What should I choose after SEE or Plus Two?','The right path depends on your goal, timeline, budget, interest, and current level. Our team compares study abroad, language, IT, and job-skill options with you.','active','What should I choose after SEE or Plus Two? | GoldenEye Academy FAQ','The right path depends on your goal, timeline, budget, interest, and current level. Our team compares study abroad, language, IT, and job-skill options with you.','GoldenEye Academy FAQ, What should I choose after SEE or Plus Two?','The right path depends on your goal, timeline, budget, interest, and current level. Our team compares study abroad, language, IT, and job-skill options with you.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"What should I choose after SEE or Plus Two?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"The right path depends on your goal, timeline, budget, interest, and current level. Our team compares study abroad, language, IT, and job-skill options with you.\"}}',30,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(4,'Do you help parents understand course options?','Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.','active','Do you help parents understand course options? | GoldenEye Academy FAQ','Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.','GoldenEye Academy FAQ, Do you help parents understand course options?','Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Do you help parents understand course options?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.\"}}',40,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(5,'Are IELTS and PTE both available?','Yes. GoldenEye Academy offers both IELTS and PTE preparation. The team helps students choose based on destination, requirement, timeline, and test preference.','active','Are IELTS and PTE both available? | GoldenEye Academy FAQ','Yes. GoldenEye Academy offers both IELTS and PTE preparation. The team helps students choose based on destination, requirement, timeline, and test preference.','GoldenEye Academy FAQ, Are IELTS and PTE both available?','Yes. GoldenEye Academy offers both IELTS and PTE preparation. The team helps students choose based on destination, requirement, timeline, and test preference.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Are IELTS and PTE both available?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. GoldenEye Academy offers both IELTS and PTE preparation. The team helps students choose based on destination, requirement, timeline, and test preference.\"}}',50,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(6,'Do you provide Japanese and Korean classes?','Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners planning study, work, or migration pathways.','active','Do you provide Japanese and Korean classes? | GoldenEye Academy FAQ','Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners planning study, work, or migration pathways.','GoldenEye Academy FAQ, Do you provide Japanese and Korean classes?','Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners planning study, work, or migration pathways.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Do you provide Japanese and Korean classes?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners planning study, work, or migration pathways.\"}}',60,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(7,'Can beginners join computer courses?','Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.','active','Can beginners join computer courses? | GoldenEye Academy FAQ','Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.','GoldenEye Academy FAQ, Can beginners join computer courses?','Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Can beginners join computer courses?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.\"}}',70,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(8,'Do I need coding experience for web development?','No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.','active','Do I need coding experience for web development? | GoldenEye Academy FAQ','No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.','GoldenEye Academy FAQ, Do I need coding experience for web development?','No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Do I need coding experience for web development?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.\"}}',80,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(9,'Are classes practical or only theoretical?','Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.','active','Are classes practical or only theoretical? | GoldenEye Academy FAQ','Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.','GoldenEye Academy FAQ, Are classes practical or only theoretical?','Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Are classes practical or only theoretical?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.\"}}',90,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(10,'Do you provide certificates?','Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.','active','Do you provide certificates? | GoldenEye Academy FAQ','Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.','GoldenEye Academy FAQ, Do you provide certificates?','Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Do you provide certificates?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.\"}}',100,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(11,'Are flexible class timings available?','Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.','active','Are flexible class timings available? | GoldenEye Academy FAQ','Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.','GoldenEye Academy FAQ, Are flexible class timings available?','Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Are flexible class timings available?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.\"}}',110,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(12,'Can working professionals join?','Yes. Working professionals can choose short courses, office skills, English communication, or flexible language/test-prep batches.','active','Can working professionals join? | GoldenEye Academy FAQ','Yes. Working professionals can choose short courses, office skills, English communication, or flexible language/test-prep batches.','GoldenEye Academy FAQ, Can working professionals join?','Yes. Working professionals can choose short courses, office skills, English communication, or flexible language/test-prep batches.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Can working professionals join?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Working professionals can choose short courses, office skills, English communication, or flexible language\\/test-prep batches.\"}}',120,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(13,'How much do courses cost?','Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.','active','How much do courses cost? | GoldenEye Academy FAQ','Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.','GoldenEye Academy FAQ, How much do courses cost?','Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"How much do courses cost?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.\"}}',130,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(14,'Is there an online learning option?','Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.','active','Is there an online learning option? | GoldenEye Academy FAQ','Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.','GoldenEye Academy FAQ, Is there an online learning option?','Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Is there an online learning option?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.\"}}',140,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(15,'How do I enroll?','You can submit the Join Now form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your goal and guide the next step.','active','How do I enroll? | GoldenEye Academy FAQ','You can submit the Join Now form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your goal and guide the next step.','GoldenEye Academy FAQ, How do I enroll?','You can submit the Join Now form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your goal and guide the next step.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"How do I enroll?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"You can submit the Join Now form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your goal and guide the next step.\"}}',150,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(16,'What happens after I submit the form?','The team receives your details, reviews the selected course or course-help request, and contacts you for confirmation and guidance.','active','What happens after I submit the form? | GoldenEye Academy FAQ','The team receives your details, reviews the selected course or course-help request, and contacts you for confirmation and guidance.','GoldenEye Academy FAQ, What happens after I submit the form?','The team receives your details, reviews the selected course or course-help request, and contacts you for confirmation and guidance.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"What happens after I submit the form?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"The team receives your details, reviews the selected course or course-help request, and contacts you for confirmation and guidance.\"}}',160,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(17,'Can I ask for a course recommendation?','Yes. Select the option for help choosing the right program, and the team will contact you with a roadmap recommendation.','active','Can I ask for a course recommendation? | GoldenEye Academy FAQ','Yes. Select the option for help choosing the right program, and the team will contact you with a roadmap recommendation.','GoldenEye Academy FAQ, Can I ask for a course recommendation?','Yes. Select the option for help choosing the right program, and the team will contact you with a roadmap recommendation.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Can I ask for a course recommendation?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. Select the option for help choosing the right program, and the team will contact you with a roadmap recommendation.\"}}',170,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(18,'Do you run events and workshops?','Yes. GoldenEye Academy can run workshops, skill sessions, course-help events, and career-focused activities based on schedule and demand.','active','Do you run events and workshops? | GoldenEye Academy FAQ','Yes. GoldenEye Academy can run workshops, skill sessions, course-help events, and career-focused activities based on schedule and demand.','GoldenEye Academy FAQ, Do you run events and workshops?','Yes. GoldenEye Academy can run workshops, skill sessions, course-help events, and career-focused activities based on schedule and demand.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Do you run events and workshops?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Yes. GoldenEye Academy can run workshops, skill sessions, course-help events, and career-focused activities based on schedule and demand.\"}}',180,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(19,'Can I switch course after getting guidance?','Course changes depend on batch status and availability. The team will help you avoid wrong enrollment before payment whenever possible.','active','Can I switch course after getting guidance? | GoldenEye Academy FAQ','Course changes depend on batch status and availability. The team will help you avoid wrong enrollment before payment whenever possible.','GoldenEye Academy FAQ, Can I switch course after getting guidance?','Course changes depend on batch status and availability. The team will help you avoid wrong enrollment before payment whenever possible.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Can I switch course after getting guidance?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Course changes depend on batch status and availability. The team will help you avoid wrong enrollment before payment whenever possible.\"}}',190,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(20,'Where is GoldenEye Academy located?','GoldenEye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.','active','Where is GoldenEye Academy located? | GoldenEye Academy FAQ','GoldenEye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.','GoldenEye Academy FAQ, Where is GoldenEye Academy located?','GoldenEye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Question\",\"name\":\"Where is GoldenEye Academy located?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"GoldenEye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.\"}}',200,'2026-05-03 20:32:35','2026-05-03 20:32:35');
/*!40000 ALTER TABLE `f_a_q_s` ENABLE KEYS */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
DROP TABLE IF EXISTS `join_now_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `join_now_queries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contactMethod` varchar(255) DEFAULT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `landing_page` varchar(255) DEFAULT NULL,
  `cta_id` varchar(255) DEFAULT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `course_slug` varchar(255) DEFAULT NULL,
  `course` varchar(255) NOT NULL,
  `queries` longtext NOT NULL,
  `admin_notes` text DEFAULT NULL,
  `followed_up_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `join_now_queries_course_id_foreign` (`course_id`),
  KEY `join_now_queries_lead_source_index` (`lead_source`),
  KEY `join_now_queries_cta_id_index` (`cta_id`),
  CONSTRAINT `join_now_queries_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `join_now_queries` DISABLE KEYS */;
/*!40000 ALTER TABLE `join_now_queries` ENABLE KEYS */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_09_21_094704_create_contacts_table',1),(5,'2024_09_21_144444_create_news_letters_table',1),(6,'2024_09_22_192515_create_join_now_queries_table',1),(7,'2024_09_26_091401_create_courses_table',1),(8,'2024_10_02_071731_create_notices_table',1),(9,'2024_10_02_081206_create_f_a_q_s_table',1),(10,'2025_08_14_170933_add_two_factor_columns_to_users_table',1),(11,'2026_04_14_202859_create_permission_tables',1),(12,'2026_04_14_203704_create_blog_posts_table',1),(13,'2026_04_15_082311_create_teachers_table',1),(14,'2026_04_15_082312_create_testimonials_table',1),(15,'2026_04_15_082313_create_site_settings_table',1),(16,'2026_04_24_172859_add_status_to_teachers_and_testimonials_table',1),(17,'2026_04_24_173058_add_status_to_courses_table',1),(18,'2026_04_25_061849_add_link_fields_to_notices_table',1),(19,'2026_04_25_070157_add_status_to_submissions_tables',1),(20,'2026_04_25_093301_add_subtitle_to_notices_table',1),(21,'2026_04_26_083808_add_course_relation_to_enrollments',1),(22,'2026_04_26_083914_add_featured_and_categories_tables',1),(23,'2026_04_26_092157_add_seo_fields_to_faqs_table',1),(24,'2026_04_26_151759_add_image_to_course_categories_table',1),(25,'2026_04_26_152122_refine_submissions_tables',1),(26,'2026_04_26_152320_add_seo_fields_to_course_categories_table',1),(27,'2026_04_27_161118_add_badge_text_to_courses_table',1),(28,'2026_04_27_193804_add_status_and_priority_to_course_categories_table',1),(29,'2026_04_27_195244_change_courses_category_to_string',1),(30,'2026_04_29_071915_add_seo_and_aeo_fields_to_models',1),(31,'2026_04_29_142044_add_contact_method_to_join_now_queries_table',1),(32,'2026_04_29_142437_harden_database_for_seo_and_functionality',1),(33,'2026_04_29_153937_add_badge_to_notices_table',1),(34,'2026_04_30_002218_add_scheduling_and_display_fields_to_notices_table',1),(35,'2026_05_02_142738_add_unique_index_to_courses_slug',1),(36,'2026_05_02_143514_add_performance_indices_to_main_tables',1),(37,'2026_05_02_165520_create_service_pillars_table',1),(38,'2026_05_02_170354_add_conversion_tracking_to_submissions',1),(39,'2026_05_02_173802_add_category_to_blog_posts_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
DROP TABLE IF EXISTS `news_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_letters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `news_letters` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_letters` ENABLE KEYS */;
DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `badge` varchar(255) DEFAULT NULL,
  `display_type` varchar(255) NOT NULL DEFAULT 'popup',
  `is_urgent` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
INSERT INTO `notices` VALUES (1,'Free Course Roadmap Help','Not sure whether to choose IELTS, PTE, Korean, Japanese, computer skills, or web development? Send a quick course-help request first.','Guidance First','popup',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'site/img/carousel-1.png','/join-now?course=undecided','Ask for Course Help','active','2026-05-03 20:32:35','2026-05-03 20:32:35'),(2,'New Batches Open This Month','IELTS, PTE, Korean, Japanese, computer office skills, and web development batches are accepting inquiries.','Admissions Open','bar',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'site/img/premium.png','/courses-all','Explore Programs','active','2026-05-03 20:32:35','2026-05-03 20:32:35'),(3,'Parent and Student Help Desk','Parents can discuss course direction, timing, fees, and student readiness before enrollment.','For Parents','standard',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'site/img/about.jpg','/contact','Ask a Question','inactive','2026-05-03 20:32:35','2026-05-03 20:32:35');
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','web','2026-05-03 20:32:34','2026-05-03 20:32:34'),(2,'Staff','web','2026-05-03 20:32:34','2026-05-03 20:32:34'),(3,'Teacher','web','2026-05-03 20:32:34','2026-05-03 20:32:34'),(4,'Student','web','2026-05-03 20:32:34','2026-05-03 20:32:34');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
DROP TABLE IF EXISTS `service_pillars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_pillars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(80) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `bullets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bullets`)),
  `cta_label` varchar(255) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_pillars_slug_unique` (`slug`),
  KEY `service_pillars_status_sort_order_index` (`status`,`sort_order`),
  KEY `service_pillars_is_featured_index` (`is_featured`),
  KEY `service_pillars_status_index` (`status`),
  KEY `service_pillars_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `service_pillars` DISABLE KEYS */;
INSERT INTO `service_pillars` VALUES (1,'Learn Your Way: Digital-First Flexibility','fa fa-laptop-code','learn-your-way-digital-first-flexibility','Flexible online learning with in-person support when students need focus and collaboration.','[\"Online When You Want: Premium, interactive virtual classrooms that fit your schedule. Learn from our experts anywhere, anytime.\",\"In-Person When You Need It: Access to our physical hubs for focused, face-to-face collaboration.\",\"Always Connected: Access learning resources, quick help, and follow-up support beyond the classroom.\"]','Ask for Course Help','/join-now?course=undecided',1,'active',10,'Learn Your Way: Digital-First Flexibility | GoldenEye Academy','Flexible online learning with in-person support when students need focus and collaboration.','GoldenEye Academy, Learn Your Way: Digital-First Flexibility','Flexible online learning with in-person support when students need focus and collaboration.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"Learn Your Way: Digital-First Flexibility\",\"description\":\"Flexible online learning with in-person support when students need focus and collaboration.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36'),(2,'The Network: Your Unfair Advantage','fa fa-handshake','the-network-your-unfair-advantage','Corporate access, industry exposure, and community events that help students move faster.','[\"Corporate Connections: Skip the cold-emailing. We connect you directly with private corporations for real-world exposure and internships.\",\"Industry Meet and Greets: Learn directly from professionals working in the fields students want to enter.\",\"Exclusive Community Events: Workshops, seminars, and career sessions designed to expand student networks.\"]','Join The Network','/contact',1,'active',20,'The Network: Your Unfair Advantage | GoldenEye Academy','Corporate access, industry exposure, and community events that help students move faster.','GoldenEye Academy, The Network: Your Unfair Advantage','Corporate access, industry exposure, and community events that help students move faster.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"The Network: Your Unfair Advantage\",\"description\":\"Corporate access, industry exposure, and community events that help students move faster.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36'),(3,'Career and College Blueprint: Zero Guesswork','fa fa-bullseye','career-and-college-blueprint-zero-guesswork','Guided pathway planning for post-SEE, Plus Two, scholarships, and university applications.','[\"Post-SEE and Plus Two Placement: Help students compare streams, colleges, and future paths.\",\"Scholarship Direction: Guide students toward realistic funding and admission options.\",\"Parent-Friendly Guidance: Explain choices clearly so families can decide with confidence.\"]','Plan My Path','/join-now?course=undecided',0,'active',30,'Career and College Blueprint: Zero Guesswork | GoldenEye Academy','Guided pathway planning for post-SEE, Plus Two, scholarships, and university applications.','GoldenEye Academy, Career and College Blueprint: Zero Guesswork','Guided pathway planning for post-SEE, Plus Two, scholarships, and university applications.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"Career and College Blueprint: Zero Guesswork\",\"description\":\"Guided pathway planning for post-SEE, Plus Two, scholarships, and university applications.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36'),(4,'Real-World Skills: Become Un-Ignorable','fa fa-lightbulb','real-world-skills-become-un-ignorable','Career-ready bootcamps and employability programs built around real workplace demand.','[\"Market-Ready Bootcamps: Practical training in IT, computer skills, office productivity, and communication.\",\"Employability Upliftment: Train for interviews, job tasks, and daily workplace confidence.\",\"Proof-Oriented Learning: Build outputs that students can explain during interviews or admissions conversations.\"]','Build My Skills','/courses-all',0,'active',40,'Real-World Skills: Become Un-Ignorable | GoldenEye Academy','Career-ready bootcamps and employability programs built around real workplace demand.','GoldenEye Academy, Real-World Skills: Become Un-Ignorable','Career-ready bootcamps and employability programs built around real workplace demand.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"Real-World Skills: Become Un-Ignorable\",\"description\":\"Career-ready bootcamps and employability programs built around real workplace demand.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36'),(5,'Global Launchpad: Languages and Test Prep','fa fa-globe-asia','global-launchpad-languages-and-test-prep','Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.','[\"Study Abroad Ready: Focused prep for IELTS, PTE, and language requirements.\",\"Modern Language Mastery: Practical Japanese, Korean, English, and Chinese classes for global fluency.\",\"Timeline Guidance: Match test prep with destination, application, and intake planning.\"]','Start Test Prep','/courses-all',0,'active',50,'Global Launchpad: Languages and Test Prep | GoldenEye Academy','Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.','GoldenEye Academy, Global Launchpad: Languages and Test Prep','Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"Global Launchpad: Languages and Test Prep\",\"description\":\"Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36'),(6,'Academic Powerhouse: Grades 8 to Masters','fa fa-book-open','academic-powerhouse-grades-8-to-masters','Academic support from school level through undergraduate and masters-level IT and business support.','[\"Curriculum Classes: Targeted support for students who need stronger academic foundations.\",\"Undergraduate Support: Help learners handle IT, business, and technical subjects more confidently.\",\"Structured Follow-Up: Keep learners accountable through regular progress checks.\"]','Get Academic Support','/contact',0,'active',60,'Academic Powerhouse: Grades 8 to Masters | GoldenEye Academy','Academic support from school level through undergraduate and masters-level IT and business support.','GoldenEye Academy, Academic Powerhouse: Grades 8 to Masters','Academic support from school level through undergraduate and masters-level IT and business support.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"Academic Powerhouse: Grades 8 to Masters\",\"description\":\"Academic support from school level through undergraduate and masters-level IT and business support.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36'),(7,'Events: Learn, Meet, Move','fa fa-calendar-check','events-learn-meet-move','Workshops, tech seminars, career summits, and community events that turn learning into real connections.','[\"Career Summits: Meet colleges, mentors, and employers in one focused space.\",\"Skill Workshops: Join practical events that add proof to your profile.\",\"Community Meetups: Build a circle with ambitious students and professionals.\"]','Ask About Events','/contact',0,'active',70,'Events: Learn, Meet, Move | GoldenEye Academy','Workshops, tech seminars, career summits, and community events that turn learning into real connections.','GoldenEye Academy, Events: Learn, Meet, Move','Workshops, tech seminars, career summits, and community events that turn learning into real connections.','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"Service\",\"name\":\"Events: Learn, Meet, Move\",\"description\":\"Workshops, tech seminars, career summits, and community events that turn learning into real connections.\",\"provider\":{\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\"}}','2026-05-03 20:32:36','2026-05-03 20:32:36');
/*!40000 ALTER TABLE `service_pillars` ENABLE KEYS */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_name','GoldenEye','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(2,'site_name_suffix','Academy','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(3,'site_logo','site/img/logo.png','image','2026-05-03 20:32:36','2026-05-03 20:32:36'),(4,'site_favicon','site/img/logo.png','image','2026-05-03 20:32:36','2026-05-03 20:32:36'),(5,'logo_subtitle','Pokhara - Est. 2008','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(6,'site_email','goldeneyeacademy2008@gmail.com','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(7,'site_phone','061-572599','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(8,'whatsapp_number','9779856058599','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(9,'site_address','Srijana Chowk, Pokhara, Nepal','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(10,'opening_hours','Sun-Fri: 7:00 AM - 6:00 PM','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(11,'facebook_url','https://www.facebook.com/goldeneyeacademy','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(12,'instagram_url','https://www.instagram.com/goldeneye.academy/','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(13,'linkedin_url','https://www.linkedin.com/company/golden-eye-academy/','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(14,'youtube_url','https://youtube.com','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(15,'twitter_url','','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(16,'meta_title','GoldenEye Academy - Career, College, Skills and Test Prep in Pokhara','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(17,'meta_keywords','GoldenEye Academy, IELTS Pokhara, PTE Pokhara, Korean class, Japanese class, computer course, web development Pokhara, course guidance Nepal','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(18,'meta_description','GoldenEye Academy helps students, parents, study abroad applicants, and job seekers choose practical courses through quick course guidance in Pokhara.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(19,'aeo_summary','GoldenEye Academy provides course guidance, test preparation, language classes, computer skills, web development, and career support for learners in Pokhara.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(20,'schema_markup','{\"@context\":\"https:\\/\\/schema.org\",\"@type\":\"EducationalOrganization\",\"name\":\"GoldenEye Academy\",\"address\":\"Srijana Chowk, Pokhara, Nepal\",\"email\":\"goldeneyeacademy2008@gmail.com\",\"telephone\":\"061-572599\"}','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(21,'google_analytics_id','','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(22,'google_search_console_id','','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(23,'bing_webmaster_id','','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(24,'speakable_selectors','.hero-hook-title, .section-title, h1, h2','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(25,'robots_txt','User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: /sitemap.xml','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(26,'geo_latitude','28.2126','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(27,'geo_longitude','83.9786','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(28,'google_maps_embed','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3515.823438031535!2d83.97858907530514!3d28.212555675898857!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399595ab361d716d%3A0xcf953250b7312903!2sGoldenEye%20Academy!5e0!3m2!1sen!2snp!4v1714100000000!5m2!1sen!2snp','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(29,'recaptcha_site_key','','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(30,'recaptcha_secret_key','','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(31,'image_size_limit','2048','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(32,'hero_image','site/img/carousel-1.png','image','2026-05-03 20:32:36','2026-05-03 20:32:36'),(33,'hero_badge_text','GoldenEye Academy Launchpad','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(34,'hero_title','Don\'t just study. Build your competitive edge.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(35,'hero_hook_headline','Don\'t just study. Build your competitive edge.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(36,'hero_hook_body','Confused about what to study next? Do not choose a course randomly. GoldenEye Academy helps students, parents, study abroad applicants, and job seekers compare the right path before enrollment.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(37,'hero_subtitle','Send your goal, get a practical roadmap, and choose the course that fits your timeline, budget, and current level.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(38,'hero_cta_text','Ask for Course Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(39,'hero_cta_1_text','Ask for Course Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(40,'hero_cta_2_text','See Courses','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(41,'stat_1_val','15+','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(42,'stat_1_lab','Years of Guidance','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(43,'stat_2_val','5,000+','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(44,'stat_2_lab','Learners Supported','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(45,'stat_3_val','4.9/5','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(46,'stat_3_lab','Student Rating','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(47,'stat_4_val','2 hr','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(48,'stat_4_lab','Typical Response','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(49,'pathway_tagline','Choose by goal','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(50,'pathway_title','Start with the path that sounds like you.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(51,'courses_title','Compare Programs by Goal','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(52,'courses_header_title','Professional Courses','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(53,'courses_subtitle','Shortlist courses by outcome, then talk to us before choosing your batch.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(54,'courses_all_tagline','Expert-Led Training','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(55,'courses_all_title','Choose Your','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(56,'category_header_badge','Program Category','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(57,'category_title_prefix','Explore','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(58,'category_tagline','Focused Pathways','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(59,'about_image','site/img/about.jpg','image','2026-05-03 20:32:36','2026-05-03 20:32:36'),(60,'about_title','Guidance-first learning since 2008','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(61,'about_text','GoldenEye Academy exists for one reason: learners should not waste time, money, or confidence on the wrong course. We help you decide first, then train with structure.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(62,'about_point_1','Quick course roadmap help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(63,'about_point_2','Language, test prep, IT, and office skills','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(64,'about_point_3','Support for students, parents, and job seekers','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(65,'about_point_4','Practical classes with clear outcomes','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(66,'about_content_title','Why GoldenEye Academy works','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(67,'about_content','We combine course guidance, practical teaching, and follow-up so learners choose courses with a clear reason.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(68,'about_section_tagline','15+ Years of Trust','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(69,'about_section_title','Pokhara trusted us because guidance comes first','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(70,'about_header_title','About GoldenEye Academy','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(71,'about_page_content','<h2>Our Approach</h2><p>GoldenEye Academy starts with simple guidance because students should not choose courses randomly. We help learners compare goals, timeline, current level, and practical outcomes before enrollment.</p><p>After that, training is built around structured classes, practice, and clear next steps.</p>','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(72,'about_feat_1_title','Guidance Before Enrollment','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(73,'about_feat_1_desc','Learners understand the best route before committing to a course.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(74,'about_feat_2_title','Practical Learning','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(75,'about_feat_2_desc','Courses focus on usable skills, mock tests, projects, and workplace confidence.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(76,'about_feat_3_title','Parent-Friendly Decisions','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(77,'about_feat_3_desc','Families can ask about outcomes, fees, timing, and student readiness.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(78,'about_feat_4_title','Follow-Up Support','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(79,'about_feat_4_desc','The team supports learners beyond the first inquiry.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(80,'founder_name','Shankar Pokharel','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(81,'founder_position','Founder and Director','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(82,'founder_message','Our goal is simple: help learners make the right decision first, then train them with practical discipline and confidence.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(83,'founder_image','site/img/message-chairperson.jpg','image','2026-05-03 20:32:36','2026-05-03 20:32:36'),(84,'founder_section_tagline','Director\'s Message','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(85,'founder_section_title','Choose the path with clarity','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(86,'teachers_title','Faculty and Course Help Team','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(87,'teachers_subtitle','Meet the people who guide your next step.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(88,'testimonials_title','Student Results and Feedback','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(89,'blog_title','Academy Blog','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(90,'blog_header_title','Academy Blog','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(91,'blog_subtitle','Guides, updates, and decisions that help you move faster.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(92,'blog_tagline','Academy Insights','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(93,'blog_section_title','Latest From GoldenEye','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(94,'blog_cta_title','Need advice before choosing?','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(95,'blog_cta_desc','Tell us your goal and our team will suggest the right path.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(96,'blog_cta_btn','Ask for Course Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(97,'recent_posts_title','Recent Guides','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(98,'faq_header_title','Frequently Asked Questions','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(99,'faq_lead_title','Still deciding? Ask first.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(100,'faq_btn_text','Ask for Course Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(101,'faq_page_content','<h2>Frequently Asked Questions</h2><p>Use these answers to understand course selection, class timing, and enrollment. If you are unsure, send a quick course-help request.</p>','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(102,'contact_header_title','Contact GoldenEye Academy','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(103,'contact_page_content','<h3>Message our team</h3><p>Share your current situation and goal. We will help you compare the right course or next step before enrollment.</p>','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(104,'enroll_header_title','Ask for Course Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(105,'enroll_section_title','Tell us your goal. We will help map the next step.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(106,'privacy_header_title','Privacy Policy','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(107,'privacy_policy_content','<h2>Privacy Commitment</h2><p>We use inquiry and enrollment details only to respond, guide students, manage admissions, and improve academy communication.</p>','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(108,'terms_header_title','Terms and Conditions','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(109,'terms_and_conditions_content','<h2>Enrollment Terms</h2><p>Course availability, timing, fees, and batch details should be confirmed with the academy team before final enrollment.</p>','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(110,'popup_status','active','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(111,'popup_image','site/img/carousel-1.png','image','2026-05-03 20:32:36','2026-05-03 20:32:36'),(112,'popup_title','Still deciding? Get your roadmap first.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(113,'popup_subtitle','Tell us your goal and we will recommend the right course path before you spend money on the wrong option.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(114,'popup_button_text','Ask for Course Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(115,'popup_register_link','/join-now?course=undecided','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(116,'notice_badge_text','Official Update','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(117,'notice_dismiss_text','Dismiss Notice','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(118,'whatsapp_cta_text','Message us on WhatsApp','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(119,'whatsapp_cta_subtext','Casual questions. Quick reply.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(120,'whatsapp_button_text','Message us on WhatsApp','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(121,'whatsapp_prefill_message','Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(122,'sticky_cta_text','Ask Now','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(123,'sticky_cta_badge','Quick Help','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(124,'sticky_cta_desc','Text us your goal.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(125,'inquiry_tab_text','Need Guidance?','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(126,'inquiry_title','Get your course recommendation','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(127,'inquiry_subtitle','Share your goal. We will recommend the right next step within 2 hours.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(128,'navbar_menu_label','Navigate','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(129,'footer_about_text','GoldenEye Academy helps students, parents, abroad applicants, and job seekers choose practical learning paths through quick course guidance.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(130,'footer_faq_title','Student Support','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(131,'footer_quick_link_title','Academy Links','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(132,'footer_contact_title','Find Us','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(133,'footer_social_title','Follow Our Journey','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(134,'footer_newsletter_desc','Get course updates, quick reminders, and practical career guidance.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(135,'career_highlight_1','Guidance before enrollment','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(136,'career_highlight_2','Practical course roadmap','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(137,'career_highlight_3','Job, study, and language pathways','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(138,'career_highlight_4','Follow-up from the academy team','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(139,'contact_success_message','Your inquiry has been received. Our team will contact you shortly.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(140,'newsletter_success_message','You are subscribed. We will send relevant course and guidance updates.','text','2026-05-03 20:32:36','2026-05-03 20:32:36'),(141,'enroll_success_message','Your request has been received. Our team will contact you shortly.','text','2026-05-03 20:32:36','2026-05-03 20:32:36');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,'Shankar Pokharel','Founder and Academic Director','site/img/message-chairperson.jpg','Guides the academy vision, parent guidance, course direction, and long-term student development strategy.','Shankar Pokharel | GoldenEye Academy Faculty','Guides the academy vision, parent guidance, course direction, and long-term student development strategy.','GoldenEye Academy faculty, Founder and Academic Director','Shankar Pokharel supports GoldenEye Academy learners as Founder and Academic Director.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',1,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(2,'Course Help Team','Course Roadmap and Admissions','site/img/team-1.jpg','Helps students, parents, abroad applicants, and job seekers compare programs before enrollment.','Course Help Team | GoldenEye Academy Faculty','Helps students, parents, abroad applicants, and job seekers compare programs before enrollment.','GoldenEye Academy faculty, Course Roadmap and Admissions','Course Help Team supports GoldenEye Academy learners as Course Roadmap and Admissions.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',1,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(3,'Aakash Subedi','IELTS and PTE Specialist','site/img/team_4.jpg','Coaches test-prep students with mock analysis, writing correction, speaking practice, and score improvement planning.','Aakash Subedi | GoldenEye Academy Faculty','Coaches test-prep students with mock analysis, writing correction, speaking practice, and score improvement planning.','GoldenEye Academy faculty, IELTS and PTE Specialist','Aakash Subedi supports GoldenEye Academy learners as IELTS and PTE Specialist.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',1,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(4,'Pradeep Paudel','Senior Korean Instructor','site/img/team-2.jpg','Leads Korean language and EPS-TOPIK preparation with practical listening, reading, and vocabulary drills.','Pradeep Paudel | GoldenEye Academy Faculty','Leads Korean language and EPS-TOPIK preparation with practical listening, reading, and vocabulary drills.','GoldenEye Academy faculty, Senior Korean Instructor','Pradeep Paudel supports GoldenEye Academy learners as Senior Korean Instructor.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',1,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(5,'Surendra Bhattarai','Computer and Office Skills Lead','site/img/team-3.jpg','Trains students in practical computer skills, office package, Excel, documentation, and digital confidence.','Surendra Bhattarai | GoldenEye Academy Faculty','Trains students in practical computer skills, office package, Excel, documentation, and digital confidence.','GoldenEye Academy faculty, Computer and Office Skills Lead','Surendra Bhattarai supports GoldenEye Academy learners as Computer and Office Skills Lead.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',1,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(6,'Ajay B.K.','English Communication Coach','site/img/team_1.jpg','Supports English learners with speaking confidence, grammar correction, presentations, and interview preparation.','Ajay B.K. | GoldenEye Academy Faculty','Supports English learners with speaking confidence, grammar correction, presentations, and interview preparation.','GoldenEye Academy faculty, English Communication Coach','Ajay B.K. supports GoldenEye Academy learners as English Communication Coach.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',0,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(7,'Navaraj Thapa','Japanese Language Instructor','site/img/team-4.jpg','Guides Japanese learners through JLPT vocabulary, grammar, scripts, listening, and culture-focused practice.','Navaraj Thapa | GoldenEye Academy Faculty','Guides Japanese learners through JLPT vocabulary, grammar, scripts, listening, and culture-focused practice.','GoldenEye Academy faculty, Japanese Language Instructor','Navaraj Thapa supports GoldenEye Academy learners as Japanese Language Instructor.',NULL,'https://www.facebook.com/goldeneyeacademy','https://www.linkedin.com/company/golden-eye-academy/','active',0,'2026-05-03 20:32:36','2026-05-03 20:32:36');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `aeo_summary` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Sandesh Mahat','Professional Web Development','site/img/testimonial-1.jpg','GoldenEye helped me understand web development step by step. The project practice made the course useful beyond theory.',5,'active',1,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(2,'Apekshya Chhetri','Korean Language','site/img/testimonial-3.jpg','The Korean classes were structured and practical. I became more confident with Hangul, listening, and daily conversation.',5,'active',1,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(3,'Nirmala Thapa','IELTS Masterclass','site/img/testimonial-4.jpg','The IELTS mock tests and speaking feedback helped me understand exactly what to improve before the real exam.',5,'active',1,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(4,'Sharap Dorje Gurung','Computer and Office Skills','site/img/testimonial-2.jpg','The office package course improved my confidence in Excel, documents, presentations, and day-to-day computer tasks.',5,'active',1,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(5,'Rojina Gurung','PTE Elite Academic Training','site/img/user.png','The PTE templates and computer-based practice made the exam feel less confusing. I knew what to focus on each week.',5,'active',0,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(6,'Suman Pariyar','Free Course Roadmap Help','site/img/user.png','I was confused between language and computer courses. The quick guidance session gave me a clear order of what to learn first.',5,'active',0,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36'),(7,'Pratiksha Sharma','Global English Professional Track','site/img/user.png','My speaking confidence improved through regular practice, correction, and interview-style activities.',5,'active',0,NULL,NULL,NULL,NULL,NULL,'2026-05-03 20:32:36','2026-05-03 20:32:36');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'GoldenEye Admin','admin@goldeneye.edu.np','2026-05-03 20:32:35','$2y$12$GGw4CF6rTtntteneZfMqtO/OkHKe16w184IC3QigXIuRrMC9s5mcq',NULL,NULL,NULL,NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35'),(2,'GoldenEye Staff','staff@goldeneye.edu.np','2026-05-03 20:32:35','$2y$12$VumK6N.VfgFC7CK0dN7CA.oO03BcDZtt71aPaap3bjdD3v8a1T0uW',NULL,NULL,NULL,NULL,'2026-05-03 20:32:35','2026-05-03 20:32:35');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



-- Applied after the base export to match the current codebase
-- GoldenEye Academy Hostinger patch
-- Use only if you imported goldeneye_hostinger_release_20260504-080254.sql.
-- This brings that earlier SQL export up to the current codebase migration set.

ALTER TABLE `courses`
  ADD COLUMN `display_order` INT UNSIGNED NOT NULL DEFAULT 100 AFTER `is_featured`,
  ADD INDEX `courses_display_order_index` (`display_order`);

SET @migration_name := '2026_05_04_032707_add_display_order_to_courses_table';
SET @next_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT @migration_name, @next_batch
WHERE NOT EXISTS (
  SELECT 1
  FROM `migrations`
  WHERE `migration` = @migration_name
);
