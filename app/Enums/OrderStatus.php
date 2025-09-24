<?php
namespace App\Enums;

enum OrderStatus: string { case pending='pending'; case paid='paid'; case cancelled='cancelled'; case refunded='refunded'; }
