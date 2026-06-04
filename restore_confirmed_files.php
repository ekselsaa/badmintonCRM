<?php
$completeDir = __DIR__ . '/recovered_files/complete';
$workspaceDir = __DIR__;

// List of files to completely restore from their full original view versions
$filesToRestore = [
    'database/seeders/DatabaseSeeder.php',
    'tests/Feature/BookingProcessTest.php',
    'resources/views/pelanggan/membership/index.blade.php',
    'resources/views/jadwal/index.blade.php',
    'resources/views/jadwal/show.blade.php',
    'resources/views/pelanggan/booking/edit.blade.php',
    'resources/views/admin/loyalty/index.blade.php',
    'resources/views/admin/booking/show.blade.php',
    'resources/views/admin/crm/detail.blade.php',
    'app/Models/User.php',
    'app/Models/MembershipPayment.php',
    'app/Http/Controllers/ProfilController.php',
    'app/Models/Jadwal.php',
    'app/Http/Controllers/JadwalPublicController.php',
];

foreach ($filesToRestore as $file) {
    $src = $completeDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    $dest = $workspaceDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    
    if (file_exists($src)) {
        copy($src, $dest);
        echo "Restored: $file\n";
    } else {
        echo "ERROR: Original version not found for $file at $src\n";
    }
}

// ─── MANUAL REVERT FOR AdminController.php ───
$adminControllerFile = $workspaceDir . '/app/Http/Controllers/AdminController.php';
if (file_exists($adminControllerFile)) {
    $content = file_get_contents($adminControllerFile);
    
    // We want to replace the verifikasiPembayaranMembership method
    // Let's locate the method and replace it.
    // The current method starts around: public function verifikasiPembayaranMembership(Request $request, $id)
    // and ends after the transaction and return statement.
    // Let's find it using string position.
    
    $startToken = 'public function verifikasiPembayaranMembership(Request $request, $id)';
    $pos = strpos($content, $startToken);
    if ($pos !== false) {
        // Find the end of the method (which ends with return back()->with('success', 'Verifikasi pembayaran membership berhasil diperbarui!');\n    })
        // Wait, the method ends with:
        //         return back()->with('success', 'Verifikasi pembayaran membership berhasil diperbarui!');
        //     }
        // Let's search for the next closing brace at the class level or matching braces, or let's search for the return statement and closing brace.
        $endSearchToken = "return back()->with('success', 'Verifikasi pembayaran membership berhasil diperbarui!');";
        $endPos = strpos($content, $endSearchToken, $pos);
        if ($endPos !== false) {
            // Find the closing brace of the method (which is the next } after the return statement, at the start of a line or after a newline)
            $methodEndPos = strpos($content, '}', $endPos);
            if ($methodEndPos !== false) {
                $methodEndPos += 1; // include the closing brace
                
                $originalMethod = 'public function verifikasiPembayaranMembership(Request $request, $id)
    {
        $request->validate([
            \'status_verifikasi\' => \'required|in:diverifikasi,ditolak\',
            \'catatan_admin\'     => \'nullable|string|max:500\',
        ]);

        $payment = \App\Models\MembershipPayment::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $request) {
            $payment->update([
                \'status_verifikasi\' => $request->status_verifikasi,
                \'catatan_admin\'     => $request->catatan_admin,
                \'verified_at\'       => now(),
            ]);

            if ($request->status_verifikasi === \'diverifikasi\') {
                // Otomatis ubah kategori_member user menjadi member
                $payment->user->update([
                    \'kategori_member\' => \'member\'
                ]);
            }
        });

        return back()->with(\'success\', \'Verifikasi pembayaran membership berhasil diperbarui!\');
    }';
                
                $newContent = substr($content, 0, $pos) . $originalMethod . substr($content, $methodEndPos);
                file_put_contents($adminControllerFile, $newContent);
                echo "Manually reverted verifikasiPembayaranMembership in AdminController.php\n";
            } else {
                echo "ERROR: Could not find closing brace of verifikasiPembayaranMembership\n";
            }
        } else {
            echo "ERROR: Could not find return statement in verifikasiPembayaranMembership\n";
        }
    } else {
        echo "ERROR: Could not find verifikasiPembayaranMembership in AdminController.php\n";
    }
}

// ─── MANUAL REVERT FOR resources/views/pelanggan/booking/index.blade.php ───
$bookingIndexFile = $workspaceDir . '/resources/views/pelanggan/booking/index.blade.php';
if (file_exists($bookingIndexFile)) {
    $content = file_get_contents($bookingIndexFile);
    
    // Replace renderTimeline changes
    $search1 = "        let isPast = isPastDate || (isToday && slot.hour <= currentHour);
        let isBooked = false;
        let isPending = false;
        let overlapKeterangan = '';

        // Check if court has overlapping booking
        if (selectedCourtId && window.occupiedJadwals && window.occupiedJadwals.length > 0) {
            const overlap = window.occupiedJadwals.find(j => {
                if (j.lapangan_id != selectedCourtId) return false;
                const jStart = formatTime(j.jam_mulai);
                const jEnd = formatTime(j.jam_selesai);
                return isOverlap(jStart, jEnd, slot.start, slot.end);
            });

            if (overlap) {
                if (overlap.status === 'pending') {
                    isPending = true;
                } else {
                    isBooked = true;
                    overlapKeterangan = overlap.keterangan || '';
                }
            }
        }";
        
    $replace1 = "        let isPast = isPastDate || (isToday && slot.hour <= currentHour);
        let isBooked = false;
        let isPending = false;

        // Check if court has overlapping booking
        if (selectedCourtId && window.occupiedJadwals && window.occupiedJadwals.length > 0) {
            const overlap = window.occupiedJadwals.find(j => {
                if (j.lapangan_id != selectedCourtId) return false;
                const jStart = formatTime(j.jam_mulai);
                const jEnd = formatTime(j.jam_selesai);
                return isOverlap(jStart, jEnd, slot.start, slot.end);
            });

            if (overlap) {
                if (overlap.status === 'pending') {
                    isPending = true;
                } else {
                    isBooked = true;
                }
            }
        }";
        
    // Replace statusText in renderTimeline
    $search2 = "        } else if (isBooked) {
            slotEl.classList.add('disabled', 'status-booked');
            statusText = overlapKeterangan ? overlapKeterangan : 'Dipesan';";
            
    $replace2 = "        } else if (isBooked) {
            slotEl.classList.add('disabled', 'status-booked');
            statusText = 'Dipesan';";
            
    // Replace labelText in rebuildOccupiedSchedulesList
    $search3 = "const labelText = isPending ? 'Pending' : (j.keterangan ? j.keterangan : 'Dipesan');";
    $replace3 = "const labelText = isPending ? 'Pending' : 'Dipesan';";
    
    // Normalize newlines before search and replace
    $content = str_replace("\r\n", "\n", $content);
    $search1 = str_replace("\r\n", "\n", $search1);
    $replace1 = str_replace("\r\n", "\n", $replace1);
    $search2 = str_replace("\r\n", "\n", $search2);
    $replace2 = str_replace("\r\n", "\n", $replace2);
    
    $pos1 = strpos($content, $search1);
    $pos2 = strpos($content, $search2);
    $pos3 = strpos($content, $search3);
    
    if ($pos1 !== false) {
        $content = str_replace($search1, $replace1, $content);
        echo "Replaced timeline overlap detection in booking/index.blade.php\n";
    } else {
        echo "WARNING: Could not find timeline overlap detection search string in booking/index.blade.php\n";
    }
    
    if ($pos2 !== false) {
        $content = str_replace($search2, $replace2, $content);
        echo "Replaced timeline statusText in booking/index.blade.php\n";
    } else {
        echo "WARNING: Could not find timeline statusText search string in booking/index.blade.php\n";
    }
    
    if ($pos3 !== false) {
        $content = str_replace($search3, $replace3, $content);
        echo "Replaced occupied list labelText in booking/index.blade.php\n";
    } else {
        echo "WARNING: Could not find occupied list labelText search string in booking/index.blade.php\n";
    }
    
    file_put_contents($bookingIndexFile, $content);
}

// ─── DELETE THE NEW MIGRATIONS ───
$migrationsToDelete = [
    'database/migrations/2026_06_04_134750_add_schedule_fields_to_membership_payments_table.php',
    'database/migrations/2026_06_04_135949_alter_kategori_member_in_users_table.php'
];

foreach ($migrationsToDelete as $mig) {
    $path = $workspaceDir . '/' . $mig;
    if (file_exists($path)) {
        unlink($path);
        echo "Deleted migration: $mig\n";
    }
}
