<?php

namespace App\Imports;

use App\Models\Voter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class VotersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected int $tpsId;
    protected int $imported = 0;

    /**
     * Format Excel yang diharapkan (hanya 1 kolom):
     * | nama          |
     * | Budi Santoso  |
     * | Siti Rahayu   |
     *
     * TPS dipilih dari form, tidak perlu ada di Excel.
     */
    public function __construct(int $tpsId)
    {
        $this->tpsId = $tpsId;
    }

    public function model(array $row): ?Voter
    {
        $nama = trim($row['nama'] ?? '');

        if (empty($nama)) {
            return null;
        }

        $this->imported++;

        return new Voter([
            'tps_id'       => $this->tpsId,
            'nama'         => $nama,
            'is_supporter' => false,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Kolom "nama" tidak boleh kosong.',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }
}
