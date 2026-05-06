CREATE DATABASE IF NOT EXISTS gym_db;
USE gym_db;

DROP PROCEDURE IF EXISTS EnrollMember;
DROP TRIGGER IF EXISTS after_branch_insert;
DROP TRIGGER IF EXISTS after_employee_insert;
DROP TRIGGER IF EXISTS after_member_insert;
DROP TRIGGER IF EXISTS after_member_delete;

DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS Enrolled;
DROP TABLE IF EXISTS Emergency_Contact;
DROP TABLE IF EXISTS Class;
DROP TABLE IF EXISTS Member;
DROP TABLE IF EXISTS Equipment;
DROP TABLE IF EXISTS Employee;
DROP TABLE IF EXISTS Branch;

CREATE TABLE Branch(
    bid INT PRIMARY KEY,
    bname VARCHAR(100) NOT NULL,
    bcity VARCHAR(50),
    bphone VARCHAR(20)
);

CREATE TABLE Employee(
    ssn CHAR(11) PRIMARY KEY,
    ename VARCHAR(50) NOT NULL,
    ephone VARCHAR(20),
    job_title VARCHAR(30),
    salary DECIMAL(10,2),
    bid INT NOT NULL,
    FOREIGN KEY (bid) REFERENCES Branch(bid) ON DELETE CASCADE
);

CREATE TABLE Equipment(
    eqid INT PRIMARY KEY,
    `type` VARCHAR(50),
    `condition` VARCHAR(50),
    purchase_date DATE,
    bid INT NOT NULL,
    FOREIGN KEY (bid) REFERENCES Branch(bid) ON DELETE CASCADE
);

CREATE TABLE Member(
    mid INT PRIMARY KEY,
    mname VARCHAR(50) NOT NULL,
    mage INT,
    maddress VARCHAR(255)
);

CREATE TABLE Class(
    cid INT PRIMARY KEY,
    cname VARCHAR(100) NOT NULL,
    capacity INT,
    duration INT,
    ssn CHAR(11) NOT NULL,
    FOREIGN KEY (ssn) REFERENCES Employee(ssn) ON DELETE CASCADE
);

CREATE TABLE Emergency_Contact (
    mid INT,
    ecname VARCHAR(50),
    ecphone VARCHAR(20),
    relation VARCHAR(50),
    PRIMARY KEY (mid, ecname),
    FOREIGN KEY (mid) REFERENCES Member(mid) ON DELETE CASCADE
);

CREATE TABLE Enrolled(
    mid INT,
    cid INT,
    enrollment_date DATE,
    PRIMARY KEY(mid,cid),
    FOREIGN KEY (mid) REFERENCES Member(mid) ON DELETE CASCADE,
    FOREIGN KEY (cid) REFERENCES Class(cid) ON DELETE CASCADE
);

CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DELIMITER //
CREATE TRIGGER after_branch_insert
AFTER INSERT ON Branch
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (message) VALUES (CONCAT('New branch opened: ', NEW.bname, ' in ', NEW.bcity));
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER after_employee_insert
AFTER INSERT ON Employee
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (message) VALUES (CONCAT('New employee hired: ', NEW.ename, ' as ', NEW.job_title));
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER after_member_insert
AFTER INSERT ON Member
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (message) VALUES (CONCAT('New member joined: ', NEW.mname));
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER after_member_delete
AFTER DELETE ON Member
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (message) VALUES (CONCAT('Member left the gym: ', OLD.mname));
END //
DELIMITER ;

INSERT INTO Branch (bid, bname, bcity, bphone) VALUES
(1, 'Central Branch', 'New York', '2125550101'),
(2, 'Westside Branch', 'Los Angeles', '3105550102');

INSERT INTO Employee (ssn, ename, ephone, job_title, salary, bid) VALUES
('11111111111', 'John Doe', '5551112233', 'Fitness Trainer', 25000.00, 1),
('22222222222', 'Jane Smith', '5552223344', 'Branch Manager', 45000.00, 2);

INSERT INTO Member (mid, mname, mage, maddress) VALUES
(1001, 'Alice Brown', 28, '123 Main St, New York'),
(1002, 'Bob Miller', 35, '456 Oak St, Los Angeles');

INSERT INTO Class (cid, cname, capacity, duration, ssn) VALUES
(201, 'Advanced Fitness', 15, 60, '11111111111'),
(202, 'Group Pilates', 20, 45, '22222222222');

DELIMITER //
CREATE PROCEDURE EnrollMember(IN p_mid INT, IN p_cid INT)
BEGIN
    DECLARE v_member_exists INT DEFAULT 0;
    DECLARE v_class_exists INT DEFAULT 0;
    DECLARE v_already_enrolled INT DEFAULT 0;
    
    SELECT COUNT(*) INTO v_member_exists FROM Member WHERE mid = p_mid;
    SELECT COUNT(*) INTO v_class_exists FROM Class WHERE cid = p_cid;
    SELECT COUNT(*) INTO v_already_enrolled FROM Enrolled WHERE mid = p_mid AND cid = p_cid;
    
    IF v_member_exists = 0 THEN
        SELECT 'Error: Member does not exist.' AS ProcedureResult;
    ELSEIF v_class_exists = 0 THEN
        SELECT 'Error: Class does not exist.' AS ProcedureResult;
    ELSEIF v_already_enrolled > 0 THEN
        SELECT 'Error: Member is already enrolled in this class.' AS ProcedureResult;
    ELSE
        INSERT INTO Enrolled (mid, cid, enrollment_date) VALUES (p_mid, p_cid, CURDATE());
        SELECT CONCAT('Success: Member ', p_mid, ' enrolled in Class ', p_cid) AS ProcedureResult;
    END IF;
END //
DELIMITER ;
