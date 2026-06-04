<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lapangan_id' => [
                'required',
                'exists:lapangan,id',
            ],
            'tanggal' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'jam_mulai' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $tanggal = $this->input('tanggal');
                    if ($tanggal) {
                        try {
                            $bookingDate = Carbon::parse($tanggal);
                            if ($bookingDate->isToday()) {
                                $now = now();
                                if ($value <= $now->format('H:i')) {
                                    $fail('Tidak bisa memesan jam yang sudah terlewat pada hari ini.');
                                }
                            }
                        } catch (\Exception $e) {
                            // Ignored: handeled by 'date' validation
                        }
                    }

                    $parts = explode(':', $value);
                    $hour = isset($parts[0]) ? (int)$parts[0] : 0;
                    if ($hour < 7 || $hour > 23) {
                        $fail('Jam mulai sewa harus berada di dalam jam operasional GOR (07:00 - 24:00).');
                    }
                }
            ],
            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
                function ($attribute, $value, $fail) {
                    $parts = explode(':', $value);
                    $hour = isset($parts[0]) ? (int)$parts[0] : 0;
                    $mins = isset($parts[1]) ? (int)$parts[1] : 0;

                    // Allows up to 23:59 or 24:00
                    if ($hour < 8 || ($hour > 23 && !($hour == 23 && $mins == 59))) {
                        $fail('Jam selesai sewa harus berada di dalam jam operasional GOR (07:00 - 24:00).');
                    }
                }
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:500',
            ],
            'metode_pembayaran' => [
                'required',
                'in:qris,tunai',
            ],
            'voucher_id' => [
                'nullable',
                'exists:redemptions,id',
            ],
            'membership_voucher_id' => [
                'nullable',
                'exists:vouchers,id',
            ],
            'voucher_ids' => [
                'nullable',
                'array',
            ],
            'voucher_ids.*' => [
                'exists:redemptions,id',
            ],
            'membership_voucher_ids' => [
                'nullable',
                'array',
            ],
            'membership_voucher_ids.*' => [
                'exists:vouchers,id',
            ],
            'direct_redeem_jenis' => [
                'nullable',
                'string',
                'in:anbiyaa_water,kok_satuan,raket,lapangan_offpeak,voucher_50k,lapangan_peak,voucher_member',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'jam_selesai.after' => 'Jam selesai harus lebih besar (setelah) dari jam mulai.',
            'tanggal.after_or_equal' => 'Tanggal booking tidak boleh di masa lalu.',
            'jam_mulai.required' => 'Jam mulai harus dipilih.',
            'jam_selesai.required' => 'Jam selesai harus dipilih.',
        ];
    }
}
