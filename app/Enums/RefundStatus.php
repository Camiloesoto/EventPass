<?php
namespace App\Enums;

enum RefundStatus: string { case pending='pending'; case succeeded='succeeded'; case failed='failed'; }
