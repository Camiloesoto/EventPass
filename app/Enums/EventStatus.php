<?php
namespace App\Enums;

enum EventStatus: string { case draft='draft'; case published='published'; case cancelled='cancelled'; case completed='completed'; }
