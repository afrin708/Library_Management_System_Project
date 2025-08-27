-- Database Name: librarydb

CREATE DATABASE IF NOT EXISTS librarydb;
USE librarydb;

-- Table structure for table `users`
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Role` varchar(20) DEFAULT NULL CHECK (`Role` IN ('Admin','Staff','Teacher','Student','Guest')),
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `admin`
CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  PRIMARY KEY (`AdminID`),
  UNIQUE KEY `UserID` (`UserID`),
  CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `bookcategories`
CREATE TABLE `bookcategories` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(100) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `publishers`
CREATE TABLE `publishers` (
  `PublisherID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(150) NOT NULL,
  `Contact` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PublisherID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `vendors`
CREATE TABLE `vendors` (
  `VendorID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(150) NOT NULL,
  `Contact` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`VendorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `books`
CREATE TABLE `books` (
  `BookID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `Author` varchar(150) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `Price` decimal(8,2) DEFAULT 0.00,
  `Status` varchar(20) DEFAULT NULL CHECK (`Status` IN ('Free','Paid')),
  `Quantity` int(11) DEFAULT 1,
  `PublisherID` int(11) DEFAULT NULL,
  PRIMARY KEY (`BookID`),
  KEY `CategoryID` (`CategoryID`),
  KEY `PublisherID` (`PublisherID`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `bookcategories` (`CategoryID`),
  CONSTRAINT `books_ibfk_2` FOREIGN KEY (`PublisherID`) REFERENCES `publishers` (`PublisherID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `bookissues`
CREATE TABLE `bookissues` (
  `IssueID` int(11) NOT NULL AUTO_INCREMENT,
  `BookID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `IssueDate` date NOT NULL,
  `DueDate` date NOT NULL,
  `Status` varchar(20) DEFAULT 'Issued',
  PRIMARY KEY (`IssueID`),
  KEY `BookID` (`BookID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `bookissues_ibfk_1` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`),
  CONSTRAINT `bookissues_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `bookrequests`
CREATE TABLE `bookrequests` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `BookTitle` varchar(255) DEFAULT NULL,
  `Author` varchar(150) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`RequestID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `bookrequests_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `bookreturns`
CREATE TABLE `bookreturns` (
  `ReturnID` int(11) NOT NULL AUTO_INCREMENT,
  `IssueID` int(11) DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Fine` decimal(8,2) DEFAULT 0.00,
  PRIMARY KEY (`ReturnID`),
  KEY `IssueID` (`IssueID`),
  CONSTRAINT `bookreturns_ibfk_1` FOREIGN KEY (`IssueID`) REFERENCES `bookissues` (`IssueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `fines`
CREATE TABLE `fines` (
  `FineID` int(11) NOT NULL AUTO_INCREMENT,
  `ReturnID` int(11) DEFAULT NULL,
  `Amount` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`FineID`),
  KEY `ReturnID` (`ReturnID`),
  CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`ReturnID`) REFERENCES `bookreturns` (`ReturnID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `memberships`
CREATE TABLE `memberships` (
  `MembershipID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `MembershipType` varchar(50) DEFAULT NULL,
  `PaymentStatus` varchar(20) DEFAULT 'Unpaid',
  PRIMARY KEY (`MembershipID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `memberships_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `paidbooks`
CREATE TABLE `paidbooks` (
  `PaidID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  PRIMARY KEY (`PaidID`),
  KEY `UserID` (`UserID`),
  KEY `BookID` (`BookID`),
  CONSTRAINT `paidbooks_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `paidbooks_ibfk_2` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `requests`
CREATE TABLE `requests` (
  `ReqID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`ReqID`),
  KEY `UserID` (`UserID`),
  KEY `BookID` (`BookID`),
  CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `reviews`
CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL CHECK (`Rating` BETWEEN 1 AND 5),
  `Comment` text DEFAULT NULL,
  PRIMARY KEY (`ReviewID`),
  KEY `UserID` (`UserID`),
  KEY `BookID` (`BookID`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `staff`
CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `ApprovalStatus` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`StaffID`),
  UNIQUE KEY `UserID` (`UserID`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for testing
-- Admin user
INSERT INTO users (UserID, Name, Role, Email, Password, CreatedAt)
VALUES (1, 'Admin User', 'Admin', 'admin@library.com', 'admin123', CURRENT_TIMESTAMP);
INSERT INTO admin (AdminID, UserID)
VALUES (1, 1);

-- Staff user (pending approval)
INSERT INTO users (UserID, Name, Role, Email, Password, CreatedAt)
VALUES (2, 'Staff User', 'Staff', 'staff@library.com', 'staff123', CURRENT_TIMESTAMP);
INSERT INTO staff (StaffID, UserID, ApprovalStatus)
VALUES (1, 2, 'Pending');

-- Normal user (student)
INSERT INTO users (UserID, Name, Role, Email, Password, CreatedAt)
VALUES (3, 'Student User', 'Student', 'student@library.com', 'student123', CURRENT_TIMESTAMP);

-- Categories
INSERT INTO bookcategories (CategoryID, CategoryName)
VALUES (1, 'Fiction'), (2, 'Non-Fiction');

-- Publishers
INSERT INTO publishers (PublisherID, Name, Contact)
VALUES (1, 'Penguin Books', 'contact@penguin.com'), (2, 'Random House', 'info@randomhouse.com');

-- Vendors
INSERT INTO vendors (VendorID, Name, Contact)
VALUES (1, 'Book Supplier Inc.', 'supplier@books.com');

-- Books
INSERT INTO books (BookID, Title, Author, CategoryID, Price, Status, Quantity, PublisherID)
VALUES
    (1, 'The Great Gatsby', 'F. Scott Fitzgerald', 1, 9.99, 'Paid', 5, 1),
    (2, 'Sapiens', 'Yuval Noah Harari', 2, 0.00, 'Free', 10, 2);

-- Book issue
INSERT INTO bookissues (IssueID, BookID, UserID, IssueDate, DueDate, Status)
VALUES (1, 1, 3, '2025-08-10', '2025-08-24', 'Issued');

-- Book request
INSERT INTO bookrequests (RequestID, UserID, BookTitle, Author, Status)
VALUES (1, 3, '1984', 'George Orwell', 'Pending');

-- Membership
INSERT INTO memberships (MembershipID, UserID, MembershipType, PaymentStatus)
VALUES (1, 3, 'Premium', 'Unpaid');

-- Review
INSERT INTO reviews (ReviewID, UserID, BookID, Rating, Comment)
VALUES (1, 3, 1, 5, 'Great book, highly recommend!');

COMMIT;