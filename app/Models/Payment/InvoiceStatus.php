<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceStatus extends Model
{
    use HasFactory;
    const StatusPending = 1;
    const StatusPaid = 2;
    const StatusCancelled = 3;
    const StatusRefunded = 4;

}
