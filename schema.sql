-- PostgreSQL database schema for an online book store.
-- Author: Keith Ó Dúlaigh
-- Date: April 19, 2015

-- WARNING: 
-- This script will delete existing data and tables in the eshop schema before
-- creating the new database structure.

BEGIN;

DROP SCHEMA IF EXISTS eshop CASCADE;

CREATE SCHEMA eshop;

SET search_path TO eshop;

CREATE TABLE member (
    email VARCHAR(255) PRIMARY KEY,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    address_street VARCHAR(100) NOT NULL,
    address_town VARCHAR(100) NOT NULL,
    address_county VARCHAR(100) NOT NULL
);

CREATE TABLE book (
    isbn VARCHAR(13) PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    price MONEY NOT NULL CHECK (price >= 0.00::MONEY),
    category VARCHAR(100) NOT NULL,
    author_name_first VARCHAR(100) NOT NULL,
    author_name_last VARCHAR(100) NOT NULL,
    published DATE NOT NULL,
    description VARCHAR(1000) NOT NULL,
    small_image text,
    large_image text
);

CREATE TABLE comments (
    member_email VARCHAR(255) REFERENCES member(email) ON UPDATE CASCADE ON DELETE CASCADE,
    book_isbn VARCHAR(13) REFERENCES book(isbn) ON UPDATE CASCADE ON DELETE CASCADE,
    review VARCHAR(100),
    score INTEGER CHECK (score BETWEEN 1 AND 5),
    CONSTRAINT comments_pk PRIMARY KEY (member_email, book_isbn)
);

CREATE TABLE saves (
    member_email VARCHAR(255) REFERENCES member(email) ON UPDATE CASCADE ON DELETE CASCADE,
    book_isbn VARCHAR(13) REFERENCES book(isbn) ON UPDATE CASCADE ON DELETE CASCADE,
    quantity INTEGER CHECK (quantity > 0) DEFAULT 1,
    CONSTRAINT saves_pk PRIMARY KEY (member_email, book_isbn)
);

CREATE TABLE purchases (
    member_email VARCHAR(255) REFERENCES member(email) ON UPDATE CASCADE ON DELETE RESTRICT,
    book_isbn VARCHAR(13) REFERENCES book(isbn) ON UPDATE CASCADE ON DELETE RESTRICT,
    price MONEY CHECK (price >= 0.00::MONEY) NOT NULL,
    purchased_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    quantity INTEGER CHECK (quantity > 0) DEFAULT 1,
    CONSTRAINT purchases_pk PRIMARY KEY (member_email, book_isbn, purchased_on)
);

COMMIT;
