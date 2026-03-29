-- Create and select the database
CREATE DATABASE IF NOT EXISTS gym_app;
USE gym_app;

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

-- Table representing the M:N relationship between Member and Class
CREATE TABLE Enrolled(
    mid INT,
    cid INT,
    enrollment_date DATE,
    PRIMARY KEY(mid,cid),
    FOREIGN KEY (mid) REFERENCES Member(mid) ON DELETE CASCADE,
    FOREIGN KEY (cid) REFERENCES Class(cid) ON DELETE CASCADE
);

-- Branch Table
INSERT INTO Branch (bid, bname, bcity, bphone) VALUES
(1, 'Central Branch', 'New York', '2125550101'),
(2, 'Westside Branch', 'Los Angeles', '3105550102'),
(3, 'Downtown Branch', 'Chicago', '3125550103'),
(4, 'Uptown Branch', 'Houston', '7135550104'),
(5, 'Northside Branch', 'Phoenix', '6025550105');

-- Employee Table
INSERT INTO Employee (ssn, ename, ephone, job_title, salary, bid) VALUES
('11111111111', 'John Doe', '5551112233', 'Fitness Trainer', 25000.00, 1),
('22222222222', 'Jane Smith', '5552223344', 'Pilates Instructor', 26000.00, 1),
('33333333333', 'Michael Johnson', '5553334455', 'Branch Manager', 45000.00, 2),
('44444444444', 'Emily Davis', '5554445566', 'Yoga Instructor', 24000.00, 3),
('55555555555', 'David Wilson', '5555556677', 'Receptionist', 18000.00, 4);

-- Equipment Table
INSERT INTO Equipment (eqid, `type`, `condition`, purchase_date, bid) VALUES
(101, 'Treadmill', 'New', '2023-05-10', 1),
(102, 'Dumbbell Set', 'Good', '2022-08-15', 1),
(103, 'Stationary Bike', 'Needs Maintenance', '2021-11-20', 2),
(104, 'Pilates Ball', 'Good', '2023-01-12', 3),
(105, 'Bench Press', 'New', '2024-02-05', 4);

-- Member Table
INSERT INTO Member (mid, mname, mage, maddress) VALUES
(1001, 'Alice Brown', 28, '123 Main St, New York'),
(1002, 'Bob Miller', 35, '456 Oak St, Los Angeles'),
(1003, 'Charlie Garcia', 24, '789 Pine St, Chicago'),
(1004, 'Diana Martinez', 42, '321 Elm St, Houston'),
(1005, 'Evan Robinson', 30, '654 Maple St, Phoenix');

-- Class Table
INSERT INTO Class (cid, cname, capacity, duration, ssn) VALUES
(201, 'Advanced Fitness', 15, 60, '11111111111'),
(202, 'Group Pilates', 20, 45, '22222222222'),
(203, 'Morning Yoga', 12, 50, '44444444444'),
(204, 'Cardio Blast', 25, 40, '11111111111'),
(205, 'Basic Yoga', 10, 60, '44444444444');

-- Emergency_Contact Table
INSERT INTO Emergency_Contact (mid, ecname, ecphone, relation) VALUES
(1001, 'Tom Brown', '5559990011', 'Brother'),
(1002, 'Sarah Miller', '5559990022', 'Spouse'),
(1003, 'Luis Garcia', '5559990033', 'Father'),
(1004, 'Maria Martinez', '5559990044', 'Sister'),
(1005, 'Nancy Robinson', '5559990055', 'Mother');

-- Enrolled Table
INSERT INTO Enrolled (mid, cid, enrollment_date) VALUES
(1001, 201, '2026-03-01'),
(1001, 204, '2026-03-05'),
(1002, 202, '2026-03-10'),
(1003, 203, '2026-03-15'),
(1004, 205, '2026-03-20');

-- Queries

-- Query 1 - Retrieve all rows and columns from the Branch table to see all facility locations.
-- Category A - Retrieve all rows
SELECT * FROM Branch;

-- Query 2 - List the names and phone numbers of employees who work in the branch with ID 1.
-- Category B - Join with projection of selected columns
SELECT E.ename, B.bphone 
FROM Employee E, Branch B 
WHERE E.bid = B.bid AND B.bid = 1;

-- Query 3 - Find the maximum salary among all employees in the gym system.
-- Category C - Aggregate MAX
SELECT MAX(salary) 
FROM Employee;

-- Query 4 - List all members, sorted by their age in ascending order.
-- Category D - ORDER BY ascending
SELECT * 
FROM Member 
ORDER BY mage ASC;

-- Query 5 - Find branch IDs that have more than one employee by grouping results and filtering.
-- Category E - Filtering grouped results with HAVING
SELECT bid, COUNT(*) 
FROM Employee 
GROUP BY bid HAVING COUNT(*) > 1;

-- Query 6 - Show the names of employees who have a salary greater than 12000.
-- Category A - Selection + Projection together
SELECT ename 
FROM Employee 
WHERE salary > 12000;

-- Query 7 - List the names of members and the names of the classes they are enrolled in.
-- Category B - Join between 3 tables
SELECT M.mname, C.cname 
FROM Member M, Enrolled En, Class C 
WHERE M.mid = En.mid AND En.cid = C.cid;

-- Query 8 - Calculate the average age of all gym members.
-- Category C - Aggregate AVG
SELECT AVG(mage) 
FROM Member;

-- Query 9 - List the names of all fitness classes, sorted alphabetically in descending order.
-- Category D - ORDER BY descending
SELECT cname 
FROM Class 
ORDER BY cname DESC;

-- Query 10 - Find the total number of enrollments for each fitness class by joining the tables and grouping.
-- Category E - Join + Group By
SELECT C.cname, COUNT(En.mid) 
FROM Class C 
LEFT JOIN Enrolled En ON C.cid = En.cid GROUP BY C.cname;