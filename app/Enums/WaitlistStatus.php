<?php
namespace App\Enums;

enum WaitlistStatus: string { case waiting='waiting'; case notified='notified'; case converted='converted'; case expired='expired'; }
