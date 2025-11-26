<?php

namespace App\Rules;

use App\Models\Jadwal;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JadwalTimeValidation implements ValidationRule
{
    protected $data = [];

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
        $time = Carbon::parse($value);
        $checkSchedule = Jadwal::where(function ($query) use ($time) {
            $query->whereTime('waktu_mulai', '<=', $time->format('H:i'))
                  ->whereTime('waktu_selesai', '>=', $time->format('H:i'));
        })->exists();
        //
    }
}
