<?php
namespace App\Enums;

enum TicketStatus: string { case issued='issued'; case transferred='transferred'; case redeemed='redeemed'; case cancelled='cancelled'; }
