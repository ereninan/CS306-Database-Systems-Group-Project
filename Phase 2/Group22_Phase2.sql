CREATE DATABASE gym_app;
USE gym_app;

# Branch Table
CREATE TABLE Branch(
	bid INT PRIMARY KEY,
    bname VARCHAR(100) NOT NULL,
    bcity VARCHAR(50),
    bphone VARCHAR(20)
);

# Employee Table
CREATE TABLE Employee(
	ssn CHAR(11) PRIMARY KEY,
    ename VARCHAR(50) NOT NULL,
    ephone VARCHAR(20),
    job_title VARCHAR(30),
    salary DECIMAL(10,2),
    bid INT NOT NULL,
    FOREIGN KEY (bid) REFERENCES Branch(bid) ON DELETE CASCADE
);

# Equipment Table
CREATE TABLE Equipment(
	eqid INT PRIMARY KEY,
    `type` VARCHAR(50),
    `condition` VARCHAR(50),
    purchase_date DATE,
    bid INT NOT NULL,
    FOREIGN KEY (bid) REFERENCES Branch(bid) ON DELETE CASCADE
);

# Memeber Table
CREATE TABLE Member(
	mid INT PRIMARY KEY,
    mname VARCHAR(50) NOT NULL,
    mage INT,
    maddress VARCHAR(255)
);

# Class Table
CREATE TABLE Class(
	cid INT PRIMARY KEY,
    cname VARCHAR(100) NOT NULL,
    capacity INT,
    duration INT,
    ssn CHAR(11) NOT NULL,
    FOREIGN KEY (ssn) REFERENCES Employee(ssn) ON DELETE CASCADE
);

# Emergency Contact Table
CREATE TABLE Emergency_Contact (
	mid INT,
    ecname VARCHAR(50),
    ecphone VARCHAR(20),
    relation VARCHAR(50),
    PRIMARY KEY (mid, ecname),
    FOREIGN KEY (mid) REFERENCES Member(mid) ON DELETE CASCADE
);

# Enrolled Table (It refers relation (M:N) between Member and Class table)
CREATE TABLE Enrolled(
	mid INT,
    cid INT,
    enrollment_date DATE,
    PRIMARY KEY(mid,cid),
    FOREIGN KEY (mid) REFERENCES Member(mid) ON DELETE CASCADE,
    FOREIGN KEY (cid) REFERENCES Class(cid) ON DELETE CASCADE
);
