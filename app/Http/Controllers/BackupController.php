<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        return view('settings.backup');
    }

    public function backup()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $filename = 'backup_apptoko_' . date('Y_m_d_His') . '.sql';
        $path = storage_path('app/public/' . $filename);

        if (empty($password)) {
            $command = "mysqldump --user={$username} --host={$host} {$database} > \"{$path}\"";
        } else {
            $command = "mysqldump --user={$username} --password={$password} --host={$host} {$database} > \"{$path}\"";
        }

        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Gagal Backup: ' . implode(" ", $output));
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);

        $file = $request->file('backup_file');

        if ($file->getClientOriginalExtension() !== 'sql') {
            return back()->with('error', 'Harap unggah file dengan format .sql!');
        }

        $path = $file->getRealPath();

        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        if (empty($password)) {
            $command = "mysql --user={$username} --host={$host} {$database} < \"{$path}\"";
        } else {
            $command = "mysql --user={$username} --password={$password} --host={$host} {$database} < \"{$path}\"";
        }

        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Gagal memulihkan database. Pastikan file valid.');
        }

        return back()->with('success', 'Database berhasil di-restore kembali ke keadaan semula!');
    }
}
