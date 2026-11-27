<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PBI-18: Manajemen Notifikasi Sistem.
 * Pengguna melihat daftar, menandai dibaca/belum dibaca, dan menghapus notifikasi miliknya.
 * Notifikasi dibuat otomatis oleh sistem (NotifikasiService), bukan oleh pengguna.
 */
class NotifikasiController extends Controller
{
    private const FILTER_STATUS = ['semua', 'belum-dibaca', 'dibaca'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Notifikasi::class);

        $user = Auth::user();
        $status = in_array($request->query('status'), self::FILTER_STATUS, true) ? $request->query('status') : 'semua';
        $tipe = array_key_exists((string) $request->query('tipe'), Notifikasi::LABEL_TIPE) ? $request->query('tipe') : null;

        $notifikasiList = Notifikasi::query()
            ->milik($user)
            ->when($status === 'belum-dibaca', fn ($q) => $q->belumDibaca())
            ->when($status === 'dibaca', fn ($q) => $q->sudahDibaca())
            ->when($tipe !== null, fn ($q) => $q->where('tipe', $tipe))
            ->orderByDesc('created_at')
            ->orderByDesc('notifikasi_id')
            ->paginate(config('notifikasi.per_halaman', 10))
            ->withQueryString();

        $jumlahBelumDibaca = Notifikasi::query()->milik($user)->belumDibaca()->count();
        $jumlahSudahDibaca = Notifikasi::query()->milik($user)->sudahDibaca()->count();

        return view('notifikasi.index', [
            'title' => 'Notifikasi Saya - RuangAman',
            'notifikasiList' => $notifikasiList,
            'jumlahBelumDibaca' => $jumlahBelumDibaca,
            'jumlahSudahDibaca' => $jumlahSudahDibaca,
            'status' => $status,
            'tipe' => $tipe,
            'daftarTipe' => Notifikasi::LABEL_TIPE,
        ]);
    }

    /**
     * Menampilkan detail notifikasi dan otomatis menandainya sebagai dibaca.
     */
    public function show(Notifikasi $notifikasi): View
    {
        $this->authorize('view', $notifikasi);

        if (! $notifikasi->sudahDibaca()) {
            $notifikasi->update(['read_at' => now()]);
        }

        return view('notifikasi.show', [
            'title' => 'Detail Notifikasi - RuangAman',
            'notifikasi' => $notifikasi,
        ]);
    }

    public function tandaiDibaca(Notifikasi $notifikasi): RedirectResponse
    {
        $this->authorize('update', $notifikasi);

        if (! $notifikasi->sudahDibaca()) {
            $notifikasi->update(['read_at' => now()]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function tandaiBelumDibaca(Notifikasi $notifikasi): RedirectResponse
    {
        $this->authorize('update', $notifikasi);

        $notifikasi->update(['read_at' => null]);

        return redirect()
            ->route('notifikasi.index')
            ->with('success', 'Notifikasi ditandai belum dibaca.');
    }

    public function tandaiSemuaDibaca(): RedirectResponse
    {
        $this->authorize('viewAny', Notifikasi::class);

        $jumlah = Notifikasi::query()
            ->milik(Auth::user())
            ->belumDibaca()
            ->update(['read_at' => now()]);

        return back()->with('success', $jumlah > 0
            ? "{$jumlah} notifikasi ditandai sudah dibaca."
            : 'Tidak ada notifikasi yang belum dibaca.');
    }

    public function destroy(Notifikasi $notifikasi): RedirectResponse
    {
        $this->authorize('delete', $notifikasi);

        $notifikasi->delete();

        return redirect()
            ->route('notifikasi.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Menghapus seluruh notifikasi milik pengguna yang sudah dibaca.
     */
    public function hapusSudahDibaca(): RedirectResponse
    {
        $this->authorize('viewAny', Notifikasi::class);

        $jumlah = Notifikasi::query()
            ->milik(Auth::user())
            ->sudahDibaca()
            ->delete();

        return redirect()
            ->route('notifikasi.index')
            ->with('success', $jumlah > 0
                ? "{$jumlah} notifikasi yang sudah dibaca berhasil dihapus."
                : 'Tidak ada notifikasi yang sudah dibaca untuk dihapus.');
    }
}
