<?php
namespace App\Enums;

enum PaymentStatus: string { case pending='pending'; case succeeded='succeeded'; case failed='failed'; case refunded='refunded'; }
