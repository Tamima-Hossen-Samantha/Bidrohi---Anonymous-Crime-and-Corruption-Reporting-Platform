<?php

namespace App\Services;

use App\Models\EvidenceFile;
use App\Models\Report;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvidenceEncryptionService
{
    /**
     * The encryption algorithm we use.
     * AES-256-CBC is military-grade symmetric encryption.
     */
    private string $cipher = 'AES-256-CBC';

    /**
     * The encryption key, derived from your Laravel APP_KEY.
     * We hash it to make sure it is exactly 32 bytes long.
     */
    private string $key;

    public function __construct()
    {
        $this->key = substr(hash('sha256', config('app.key'), true), 0, 32);
    }

    /**
     * MAIN METHOD — call this when a user uploads a file.
     *
     * What happens step by step:
     * 1. Read the raw bytes of the uploaded file
     * 2. Compute SHA-256 hash of those raw bytes (tamper-proof fingerprint)
     * 3. Generate a random 16-byte IV (Initialization Vector) for encryption
     * 4. Encrypt the file using AES-256-CBC
     * 5. Pack the IV and encrypted data together and base64-encode them
     * 6. Save the encrypted blob to storage
     * 7. Save the record in the database with the SHA-256 hash
     *
     * @param UploadedFile $file   — the file the user uploaded
     * @param Report       $report — the report this evidence belongs to
     * @return EvidenceFile        — the saved database record
     */
    public function encryptAndStore(UploadedFile $file, Report $report): EvidenceFile
    {
        // Step 1: Read raw bytes from the uploaded file
        $originalBytes = file_get_contents($file->getRealPath());

        // Step 2: Compute SHA-256 fingerprint of the ORIGINAL bytes
        // This is the tamper-proof seal. If anyone modifies the file
        // later, this hash will no longer match when we verify it.
        $sha256Hash = hash('sha256', $originalBytes);

        // Collect file metadata for display purposes
        $originalName = $file->getClientOriginalName();
        $mimeType     = $file->getMimeType();
        $fileSize     = strlen($originalBytes);

        // Step 3: Generate a random IV — this makes every encryption unique
        // Even if two people submit the same file, the encrypted output differs
        $iv = random_bytes(16);

        // Step 4: Encrypt the raw bytes
        $encrypted = openssl_encrypt(
            $originalBytes,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        // Step 5: Pack IV + encrypted data, then base64-encode for safe storage
        $packed = base64_encode($iv . $encrypted);

        // Step 6: Save to disk using a random UUID filename so it cannot be guessed
        $filename      = Str::uuid() . '.enc';
        $encryptedPath = 'evidence/' . $report->id . '/' . $filename;
        Storage::put($encryptedPath, $packed);

        // Step 7: Save the database record
        // Note: we store sha256_hash of the ORIGINAL, not the encrypted version
        return EvidenceFile::create([
            'report_id'      => $report->id,
            'original_name'  => $originalName,
            'encrypted_path' => $encryptedPath,
            'sha256_hash'    => $sha256Hash,
            'file_size'      => $fileSize,
            'mime_type'      => $mimeType,
        ]);
    }

    /**
     * Decrypt a stored evidence file back to its original bytes.
     * Returns null if the file is missing or decryption fails.
     *
     * @param EvidenceFile $evidenceFile — the database record
     * @return string|null               — raw bytes, or null on failure
     */
    public function decrypt(EvidenceFile $evidenceFile): ?string
    {
        $packed = Storage::get($evidenceFile->encrypted_path);
        if (!$packed) return null;

        // Unpack: first 16 bytes are the IV, rest is encrypted data
        $data      = base64_decode($packed);
        $iv        = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted === false ? null : $decrypted;
    }

    /**
     * Verify that a stored evidence file has not been tampered with.
     *
     * How it works:
     * 1. Decrypt the file back to original bytes
     * 2. Compute the SHA-256 hash of those decrypted bytes
     * 3. Compare with the hash stored in the database at upload time
     * 4. If they match → file is untouched. If not → someone modified it.
     *
     * This is what you show judges: "Evidence is cryptographically sealed."
     *
     * @param EvidenceFile $evidenceFile
     * @return bool — true if file is intact, false if tampered
     */
    public function verifyIntegrity(EvidenceFile $evidenceFile): bool
    {
        if (!$evidenceFile->sha256_hash) return false;

        $decrypted = $this->decrypt($evidenceFile);
        if ($decrypted === null) return false;

        $actualHash = hash('sha256', $decrypted);

        // hash_equals is used instead of === to prevent timing attacks
        return hash_equals($evidenceFile->sha256_hash, $actualHash);
    }

    /**
     * Returns a human-readable integrity report for display in the admin panel.
     * Shows the hash, file info, and whether the file passed verification.
     *
     * @param EvidenceFile $evidenceFile
     * @return array
     */
    public function integrityReport(EvidenceFile $evidenceFile): array
    {
        $verified = $this->verifyIntegrity($evidenceFile);

        return [
            'file_id'       => $evidenceFile->id,
            'original_name' => $evidenceFile->original_name,
            'sha256_hash'   => $evidenceFile->sha256_hash,
            'file_size_kb'  => $evidenceFile->file_size
                ? round($evidenceFile->file_size / 1024, 1) . ' KB'
                : 'Unknown',
            'mime_type'     => $evidenceFile->mime_type,
            'integrity'     => $verified ? '✓ VERIFIED — File Untampered' : '✗ COMPROMISED — File Modified',
            'verified'      => $verified,
        ];
    }
}