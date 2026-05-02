<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BayarController extends Controller
{
    /**
     * Buat transaksi pembayaran ke Payment Gateway (Midtrans/Xendit).
     */
    public function bayar(Request $request)
    {
        //
    }

    /**
     * Handle callback/webhook dari Payment Gateway setelah pembayaran.
     */
    public function callback(Request $request)
    {
        //
    }
}
