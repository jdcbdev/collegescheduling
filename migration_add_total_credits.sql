-- Migration: Modify total_credits to STORED generated column in subjects table
-- This script modifies the computed total_credits column for existing databases
-- Run this after updating the PHP code

ALTER TABLE subjects MODIFY COLUMN total_credits INT AS (lec_credits + lab_credits) STORED;
