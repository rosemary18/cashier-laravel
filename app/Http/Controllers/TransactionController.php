<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\Cart;
use Exception;
use Throwable;

class TransactionController extends Controller
{
    public function store()
    {
        // Pastikan pengguna sudah terautentikasi
        if (!auth()->check()) {
            return redirect()->route('login'); // Arahkan pengguna ke halaman login
        }
        
        DB::beginTransaction();

        try {
            // Ambil ID pengguna yang sedang login
            $userId = auth()->id();

            // Proses transaksi dan detail
            $transaction = Transaction::create(array_merge(request()->all(), ['user_id' => $userId]));  // Menyimpan data transaksi

            // Membuat detail transaksi dari cart yang ada
            $cartDetails = Cart::all()->map(function ($cart) {
                return [
                    'item_id' => $cart->item_id,
                    'quantity' => $cart->quantity,
                    'subtotal' => $cart->item->price * $cart->quantity
                ];
            })->toArray();

            // Menyimpan detail transaksi
            $transaction->details()->createMany($cartDetails);

            // Menghapus semua item dalam cart setelah transaksi selesai
            DB::table('carts')->delete();

            // Commit transaksi
            DB::commit();
        } catch (Throwable $e) {
            // Rollback jika terjadi error
            DB::rollback();

            dd("Terjadi kesalahan: " . $e->getMessage());
            return back()->withError('Terjadi kesalahan: ' . $e->getMessage());
        }

        // Mengarahkan ke halaman transaksi terakhir yang berhasil
        return redirect()->route('transaction.show', $transaction);
    }

    public function index()
    {
        // Mengambil daftar transaksi terbaru
        $transactions = Transaction::latest()->get();

        // Menampilkan view dengan daftar transaksi
        return view('transaction.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        // Menampilkan detail transaksi
        return view('transaction.show', compact('transaction'));
    }
}
