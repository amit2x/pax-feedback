<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReferenceNumberService
{
    public function next(): string
    {
        $year = (int) now()->year;

        return DB::transaction(function () use ($year) {
            $row = DB::table('feedback_reference_sequences')
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (! $row) {
                DB::table('feedback_reference_sequences')->insert([
                    'year' => $year,
                    'last_number' => 1,
                ]);
                $number = 1;
            } else {
                $number = ((int) $row->last_number) + 1;
                DB::table('feedback_reference_sequences')
                    ->where('year', $year)
                    ->update(['last_number' => $number]);
            }

            return sprintf('FB-%d-%06d', $year, $number);
        });
    }
}
