<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\AbsensiConfig;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-absensi')]
#[Description('Command description')]
class CheckAbsensi extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = AbsensiConfig::first();
        $today = now()->toDateString();

        $users = \App\Models\User::all();

        foreach ($users as $user) {

            $cek = Absensi::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            if (!$cek) {
                Absensi::create([
                    'user_id' => $user->id,
                    'absensi_config_id' => $config->id,
                    'date' => $today,
                    'status' => 'absen'
                ]);
            }
        }
    }
}
