-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 04:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin123@gmail.com', 'admin@123');

-- --------------------------------------------------------

--
-- Table structure for table `beauty_parlours`
--

CREATE TABLE `beauty_parlours` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `services` text NOT NULL,
  `bridal_price` decimal(10,2) NOT NULL,
  `packages` text DEFAULT NULL,
  `experience` int(11) NOT NULL,
  `home_service` varchar(10) NOT NULL,
  `products_used` varchar(255) DEFAULT NULL,
  `portfolio_images` text NOT NULL,
  `about` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beauty_parlours`
--

INSERT INTO `beauty_parlours` (`id`, `vendor_id`, `street`, `city`, `pincode`, `services`, `bridal_price`, `packages`, `experience`, `home_service`, `products_used`, `portfolio_images`, `about`) VALUES
(5, 21, 'patia', 'Bhubaneswar', '751024', '', 20000.00, 'enagagement : 7000\r\nparty makeup:2000', 4, 'Yes', 'MAC , SWISS BEAUTY', '1774948213_beauty1.avif', 'we provide best service with quality.'),
(6, 28, 'Infocity', 'Bhubaneswar', '751024', '', 15000.00, 'basic:5000\r\npremium:15000', 1, 'Yes', 'MAC , SWISS BEAUTY, HUDA BEAUTY', '1778849056_face3.png', 'Expert beauty and makeup services for a stunning and elegant look on special occasions.');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `guest_count` int(11) DEFAULT NULL,
  `special_request` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `booking_status` varchar(50) DEFAULT 'Pending',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `customer_email` varchar(255) NOT NULL,
  `admin_revenue` decimal(10,2) GENERATED ALWAYS AS (`amount` * 0.15) STORED,
  `rating` int(1) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `vendor_id`, `service_name`, `event_date`, `event_time`, `event_location`, `guest_count`, `special_request`, `amount`, `payment_status`, `booking_status`, `customer_name`, `customer_phone`, `service_id`, `customer_email`, `rating`, `review`, `created_at`) VALUES
(16, 2, 22, 'nonveg plate', '2026-05-21', '9 pm', 'jsr', 50, 'Food : non veg\nCuisine : indian\n', 250.00, 'Pending', 'Cancelled', 'Jyoti Karmakar', '9834675908', 4, 'jyoti123@gmail.com', NULL, NULL, '2026-05-14 11:30:13'),
(25, 2, 22, 'nonveg plate', '2026-05-16', '19:59 To 23:00', 'jsr', 50, 'food should be spicy', 12500.00, 'Paid', 'Confirmed', 'Jyoti Karmakar', '07070661808', 4, 'karmakarjyoti47@gmail.com', NULL, NULL, '2026-05-15 11:19:42'),
(28, 4, 25, 'Aura Lawns Banquet Hall', '2026-05-21', '19:00 To 08:30', 'Patia', 150, 'Flowers should be used real', 25000.00, 'Paid', 'Confirmed', 'Preeti Kumari', '7070441808', 10, 'ppp123@gmail.com', 5, 'Affordable and nice interior', '2026-05-15 13:52:23'),
(29, 4, 24, 'Wedding Veg Plate', '2026-05-22', '19:30 To 08:30', 'Patia', 150, 'food should be not be so spicy.', 67500.00, 'Paid', 'Confirmed', 'Preeti Kumari', '7070441808', 8, 'ppp123@gmail.com', 4, 'Food was very tasty and delicious.', '2026-05-15 14:12:20'),
(30, 4, 29, 'Birthday & Dinner Invitation Card', '2026-05-25', '20:00 To 23:00', 'Patia', 120, 'change the color to pink  and purple ', 20400.00, 'Paid', 'Confirmed', 'Preeti Kumari', '7070441808', 14, 'ppp123@gmail.com', 3, 'Invitation cards was good.', '2026-05-15 14:48:21'),
(31, 4, 25, 'Aura Lawns Banquet Hall', '2026-05-18', '07:00 To 12:00', 'Chakradharpur', 50, 'fcfcgc', 25000.00, 'Paid', 'Confirmed', 'Preeti Kumari', '7070441808', 10, 'ppp123@gmail.com', 5, 'very good', '2026-05-18 11:22:48'),
(32, 5, 25, 'Aura Lawns Banquet Hall', '2027-12-27', '07:00 To 16:40', 'Munger & Barakurma', 200, 'Wedding flower should be in red and white color', 25000.00, 'Pending', 'Pending', 'Sahil', '6299148515', 10, 'sahil123@gmail.com', NULL, NULL, '2026-08-10 11:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `card_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `card_types` text DEFAULT NULL,
  `printing_types` varchar(200) DEFAULT NULL,
  `paper_types` varchar(200) DEFAULT NULL,
  `packages` text DEFAULT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `experience` int(11) NOT NULL,
  `portfolio_images` text DEFAULT NULL,
  `about_business` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`card_id`, `vendor_id`, `street`, `city`, `pincode`, `card_types`, `printing_types`, `paper_types`, `packages`, `starting_price`, `experience`, `portfolio_images`, `about_business`, `created_at`) VALUES
(2, 13, 'patia', 'Bhubaneswar', '751024', 'Wedding Invitation Cards,Birthday Cards,Engagement Cards,Reception Cards', 'digital', 'glossy', '1000 wedding cards: 70000', 50.00, 5, '1773671335_card1.webp', 'Sagun Card Palace, Bhubaneswar\'s premier wedding shop, curates exquisite cards, invitations, and stationery for Odisha\'s most cherished celebrations.', '2026-03-16 14:28:55'),
(3, 29, 'Patia', 'Bhubaneswar', '751024', 'Wedding Invitation Cards,Birthday Cards,Engagement Cards,Reception Cards,Corporate Event Cards,Digital E-Cards', 'digital', 'NA', '0', 150.00, 1, '1778856021_card2.jpeg', 'good', '2026-05-15 14:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `vendor_id`, `service_id`, `added_at`) VALUES
(22, 2, 3, 2, '2026-05-14 06:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `caterers`
--

CREATE TABLE `caterers` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `cuisine_types` text NOT NULL,
  `food_type` varchar(100) NOT NULL,
  `veg_price` decimal(10,2) NOT NULL,
  `nonveg_price` decimal(10,2) DEFAULT NULL,
  `menu_details` text NOT NULL,
  `packages` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  `portfolio_images` text NOT NULL,
  `about_business` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `caterers`
--

INSERT INTO `caterers` (`id`, `vendor_id`, `street`, `city`, `pincode`, `cuisine_types`, `food_type`, `veg_price`, `nonveg_price`, `menu_details`, `packages`, `capacity`, `experience`, `portfolio_images`, `about_business`) VALUES
(2, 22, 'H no. 09 ,Domjuri ', 'Jamshedpur', '832102', 'North Indian,South Indian,Continental,Mughlai', 'Non-Veg Only', 200.00, 250.00, 'North Indian', 'basic: 100 people 18000', 4000, 5, '1778740577_wedding.jpg', 'good'),
(3, 24, 'Patia', 'Bhubaneswar', '751024', 'North Indian,South Indian,Chinese', 'Both Veg & Non-Veg', 160.00, 200.00, 'Butter chicken masala , Mushroom masala , Butter naan', 'basic:16000', 2000, 3, '1778848014_k5.webp', 'Delicious food with quality service and customized menus for every celebration.');

-- --------------------------------------------------------

--
-- Table structure for table `decorators`
--

CREATE TABLE `decorators` (
  `decorator_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `decoration_types` text NOT NULL,
  `flowers` varchar(200) DEFAULT NULL,
  `packages` text DEFAULT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `experience` int(11) NOT NULL,
  `portfolio_images` text NOT NULL,
  `about_business` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `decorators`
--

INSERT INTO `decorators` (`decorator_id`, `vendor_id`, `street`, `city`, `pincode`, `decoration_types`, `flowers`, `packages`, `starting_price`, `experience`, `portfolio_images`, `about_business`) VALUES
(5, 23, 'Patia', 'Bhubaneswar', '751024', 'Wedding Stage Decoration,Floral Decoration,Theme Decoration,Birthday Decoration,Corporate Event Decoration,Engagement Decoration,Reception Decoration', 'artificial and real ', 'basic:20000\r\npremium:12500\r\n', 20000.00, 4, '1778847559_wed6.jpg', 'Timeless Celebration provides stylish and creative event decorations with beautiful themes, elegant setups, and memorable designs for weddings, birthdays, and special occasions.'),
(6, 30, 'BARAKURMA', 'Jharkhand', '833102', 'Wedding Stage Decoration', 'rose', 'basic:20000', 2000.00, 3, '1779103160_v3.jpg', 'abcd');

-- --------------------------------------------------------

--
-- Table structure for table `music_vendors`
--

CREATE TABLE `music_vendors` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `services` text NOT NULL,
  `event_types` varchar(255) NOT NULL,
  `price_per_event` decimal(10,2) NOT NULL,
  `price_per_hour` decimal(10,2) DEFAULT NULL,
  `experience` int(11) NOT NULL,
  `travel_available` varchar(10) NOT NULL,
  `packages` text NOT NULL,
  `equipment_details` text NOT NULL,
  `portfolio_files` text NOT NULL,
  `about` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `music_vendors`
--

INSERT INTO `music_vendors` (`id`, `vendor_id`, `street`, `city`, `pincode`, `services`, `event_types`, `price_per_event`, `price_per_hour`, `experience`, `travel_available`, `packages`, `equipment_details`, `portfolio_files`, `about`) VALUES
(2, 26, 'Patia', 'Bhubaneswar', '751024', 'DJ,Sound System Rental', 'wedding,birthday any events', 5000.00, 750.00, 4, 'No', 'basic:5000\r\npremium:15000', 'speakers, lights,smoke', '', 'Energetic DJ and music services that keep every event lively and entertaining.');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photography_vendors`
--

CREATE TABLE `photography_vendors` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `services` text NOT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `equipment_type` varchar(100) NOT NULL,
  `packages` text NOT NULL,
  `experience` int(11) NOT NULL,
  `portfolio_images` text NOT NULL,
  `about_business` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photography_vendors`
--

INSERT INTO `photography_vendors` (`id`, `vendor_id`, `street`, `city`, `pincode`, `services`, `starting_price`, `equipment_type`, `packages`, `experience`, `portfolio_images`, `about_business`) VALUES
(2, 27, 'Patia', 'Bhubaneswar', '751024', 'Wedding Photography,Pre-Wedding Shoot,Drone Shoot', 10000.00, 'Professional DSLR', 'basic:20000\r\npremium:50000', 2, '1778848861_portfolio1.jpg', 'Professional photography capturing memorable moments with creativity and perfection.');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `picture` varchar(255) DEFAULT NULL,
  `food_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `vendor_id`, `service_name`, `price`, `description`, `created_at`, `picture`, `food_type`) VALUES
(4, 22, 'nonveg plate', 250.00, 'butter naan+butter chicken +pulao+papad+salad+matar paneer', '2026-05-14 06:44:38', '1778741078_non veg plate.jpg', NULL),
(5, 21, 'bridal makeup', 7000.00, 'full bridal makeup', '2026-05-15 12:54:44', '1778849684_makeup.jpg', NULL),
(6, 23, 'Mandap', 25000.00, 'Floral decoration theme based', '2026-05-15 12:57:06', '1778849826_wed2.jpg', NULL),
(8, 24, 'Wedding Veg Plate', 450.00, 'Includes paneer dishes, mixed vegetables, pulao, naan, salads, desserts, and refreshing beverages for a complete vegetarian wedding meal.', '2026-05-15 13:05:42', '1778850342_vegplate1.jpg', NULL),
(9, 24, 'Wedding Non-Veg Plate', 550.00, 'Includes delicious chicken and mutton dishes, rice, naan, starters, desserts, and refreshing beverages for a complete wedding feast.', '2026-05-15 13:06:29', '1778850389_non_veg1.jpg', NULL),
(10, 25, 'Aura Lawns Banquet Hall', 25000.00, 'Offers a beautiful and spacious banquet venue with elegant decorations, seating arrangements, lighting, and premium facilities, perfect for weddings, receptions, parties, and grand celebrations.', '2026-05-15 13:10:49', '1778850649_banquet-hall.webp', NULL),
(11, 26, 'Royal DJ Music Service', 15000.00, 'Royal DJ provides energetic DJ performances, high-quality sound systems, lighting effects, and nonstop entertainment for weddings and parties at just ₹15,000 per day.', '2026-05-15 13:14:27', '1778850867_music1.jpg', NULL),
(12, 27, 'Pre-wedding shoot ', 25000.00, 'Offers creative pre-wedding photography with cinematic shots, beautiful locations, professional editing, and memorable couple moments at just ₹25,000 per day.', '2026-05-15 13:21:52', '1778851312_prewed1.jpg', NULL),
(13, 28, 'Glam Mantra Bridal Makeup Service', 18000.00, 'Glam Mantra Beauty provides professional bridal makeup, hairstyling, skincare, and beauty services with premium products for a flawless and elegant look on special occasions.', '2026-05-15 13:24:43', '1778851483_bridal1.jpg', NULL),
(14, 29, 'Birthday & Dinner Invitation Card', 170.00, 'Elegant and creative birthday & dinner digital invitation cards designed with premium printing, stylish themes, and personalized designs to make every celebration memorable.', '2026-05-15 14:43:41', '1778856221_bday1.jpeg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `mobile`, `address`) VALUES
(1, 'Shobha Mahato', 'shobha99@gmail.com', 'shobha123', '9838382947', 'chakradharpur'),
(2, 'Jyoti Karmakar', 'jyoti123@gmail.com', 'jyoti123', '9834675908', 'Jamshedpur'),
(3, 'Basu karmakar', 'basu@gmail.com', 'basu@123', '7896543210', 'jsr'),
(4, 'Preeti Kumari', 'ppp123@gmail.com', 'ppp@123', '7070441808', 'Jamshedpur , Jharkhand'),
(5, 'Sahil', 'sahil123@gmail.com', 'sahil123', '6299148515', 'Munger');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL,
  `vendor_type` varchar(50) NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `vendor_type`, `business_name`, `owner_name`, `email`, `phone`, `password`, `created_at`, `status`) VALUES
(13, 'card_vendor', 'Sagun Card Palace', 'Sagun Sharma', 'sagun@gmail.com', '7070123456', 'sagun@123', '2026-03-16 14:28:55', 'approved'),
(21, 'beauty_parlour', 'pooja beauticians', 'pooja gope', 'pooja@gmail.com', '7979853362', 'Pooja@123', '2026-03-31 09:10:13', 'approved'),
(22, 'caterer', 'annapurna suruchi ', 'annapurna', 'anna@gmail.com', '9876543210', 'anna@123', '2026-05-14 06:36:17', 'approved'),
(23, 'decorator', 'Timeless celebration', 'Rohit kumar', 'timeless123@gmail.com', '9163580665', 'timeless@123', '2026-05-15 12:19:19', 'approved'),
(24, 'caterer', 'Parampara Catering', 'Preeti kumari', 'param123@gmail.com', '9876541230', 'param@123', '2026-05-15 12:26:54', 'approved'),
(25, 'venue', 'Aura Lawns', 'Rajesh Rath', 'aura123@gmail.com', '6204984752', 'Aura@123', '2026-05-15 12:31:50', 'approved'),
(26, 'music_dj', 'Royal DJ', 'Rajesh Kumar', 'royal123@gmail.com', '7466185339', 'Royal@123', '2026-05-15 12:34:33', 'approved'),
(27, 'photography', 'Bandhan Pictures', 'Soumya Kar', 'bandhan123@gmail.com', '7979648832', 'Bandhan@123', '2026-05-15 12:41:01', 'approved'),
(28, 'beauty_parlour', 'Glam Mantra', 'Anu Das', 'glam123@gmail.com', '9008526159', 'Roop@123', '2026-05-15 12:44:16', 'approved'),
(29, 'card_vendor', 'Rajat Cards', 'Rajat Kumar', 'rajat@gmail.com', '7466158226', 'rajat@123', '2026-05-15 14:40:21', 'approved'),
(30, 'decorator', 'ABC', 'ABC', 'abc123@gmail.com', '6278994923', 'abc@123', '2026-05-18 11:19:20', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_portfolio`
--

CREATE TABLE `vendor_portfolio` (
  `portfolio_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_portfolio`
--

INSERT INTO `vendor_portfolio` (`portfolio_id`, `vendor_id`, `image_path`, `caption`, `created_at`) VALUES
(5, 13, '1773671335_card1.webp', NULL, '2026-03-16 14:28:55'),
(6, 14, '1773837658_beauty1.avif', NULL, '2026-03-18 12:40:58'),
(0, 21, '1774948213_beauty1.avif', NULL, '2026-03-31 09:10:13'),
(0, 22, '1778760459_Kaushal-Caterers-8775-1-weddingplz.jpg', 'Annapurna suruchi', '2026-05-14 12:07:39'),
(0, 28, '1778849056_face3.png', NULL, '2026-05-15 12:44:16'),
(0, 23, '1778849769_wed7.jpg', 'Timeless celebrations celebrates every moments', '2026-05-15 12:56:09'),
(0, 24, '1778850001_k14.jfif.jpeg', 'Param catering services', '2026-05-15 13:00:01'),
(0, 25, '1778850443_Weddings-Functions-Receptions.jpg', 'Aura Lawns always welcomes you', '2026-05-15 13:07:23'),
(0, 26, '1778850779_royal_portfolio1.jpg', 'Royal DJ delivers energetic music, professional sound systems, and exciting DJ performances to make weddings, parties, and events lively and unforgettable.', '2026-05-15 13:12:59'),
(0, 27, '1778851074_portfolio2_bandhan.jpg', 'Bandhan Pictures showcases stunning wedding, pre-wedding, and event photography with creative shots, cinematic moments, and professional editing that beautifully captures every special memory.', '2026-05-15 13:17:54'),
(0, 29, '1778856977_card_portfolio2.jpg', 'Rajat Cards & Services', '2026-05-15 14:56:17');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `venue_type` varchar(100) NOT NULL,
  `min_capacity` int(11) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `price_per_plate` decimal(10,2) DEFAULT NULL,
  `rental_price` decimal(10,2) DEFAULT NULL,
  `ac_available` varchar(10) DEFAULT NULL,
  `parking_capacity` int(11) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `outside_catering` varchar(10) DEFAULT NULL,
  `outside_decoration` varchar(10) DEFAULT NULL,
  `advance_payment` int(11) DEFAULT NULL,
  `cancellation_policy` text DEFAULT NULL,
  `venue_images` text DEFAULT NULL,
  `about_venue` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `vendor_id`, `street`, `city`, `pincode`, `venue_type`, `min_capacity`, `max_capacity`, `price_per_plate`, `rental_price`, `ac_available`, `parking_capacity`, `facilities`, `outside_catering`, `outside_decoration`, `advance_payment`, `cancellation_policy`, `venue_images`, `about_venue`, `created_at`) VALUES
(2, 25, 'Infocity', 'Bhubaneswar', '751024', 'Banquet Hall', 50, 500, NULL, NULL, 'Yes', 10, 'Power Backup,Guest Rooms', 'Yes', 'Yes', 15, 'cancellation can be done before 7 days of booking', '1778848310_v2.jpg', 'Beautiful and spacious venues perfect for weddings, parties, and special events.', '2026-05-15 12:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `vendor_id`, `created_at`) VALUES
(6, 2, 20, '2026-05-11 07:23:59'),
(8, 2, 9, '2026-05-12 12:11:49'),
(9, 2, 3, '2026-05-12 12:11:51'),
(13, 4, 21, '2026-05-20 07:35:35'),
(14, 5, 13, '2026-08-10 11:05:26'),
(15, 5, 25, '2026-08-10 11:05:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `beauty_parlours`
--
ALTER TABLE `beauty_parlours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `caterers`
--
ALTER TABLE `caterers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `decorators`
--
ALTER TABLE `decorators`
  ADD PRIMARY KEY (`decorator_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `music_vendors`
--
ALTER TABLE `music_vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `fk_packages_vendor` (`vendor_id`);

--
-- Indexes for table `photography_vendors`
--
ALTER TABLE `photography_vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `beauty_parlours`
--
ALTER TABLE `beauty_parlours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `caterers`
--
ALTER TABLE `caterers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `decorators`
--
ALTER TABLE `decorators`
  MODIFY `decorator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `music_vendors`
--
ALTER TABLE `music_vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `photography_vendors`
--
ALTER TABLE `photography_vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beauty_parlours`
--
ALTER TABLE `beauty_parlours`
  ADD CONSTRAINT `beauty_parlours_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `caterers`
--
ALTER TABLE `caterers`
  ADD CONSTRAINT `caterers_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `decorators`
--
ALTER TABLE `decorators`
  ADD CONSTRAINT `decorators_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `music_vendors`
--
ALTER TABLE `music_vendors`
  ADD CONSTRAINT `music_vendors_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `packages`
--
ALTER TABLE `packages`
  ADD CONSTRAINT `fk_packages_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE;

--
-- Constraints for table `photography_vendors`
--
ALTER TABLE `photography_vendors`
  ADD CONSTRAINT `photography_vendors_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `venues`
--
ALTER TABLE `venues`
  ADD CONSTRAINT `venues_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
