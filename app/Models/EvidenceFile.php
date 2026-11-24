<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'file_type',        // 'photo', 'video', 'audio', 'document'
        'original_name',
        'stored_name',      // encrypted filename on disk
        'mime_type',
        'file_size',        // bytes
        'encryption_key',   // AES-256 key reference (never stored in plain)
        'checksum',         // SHA-256 hash for tamper detection
        'is_encrypted',
        'storage_path',
    ];

    protected $hidden = [
        'encryption_key',
        'stored_name',
        'storage_path',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'file_size'    => 'integer',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Helper
    public function isPhoto(): bool
    {
        return $this->file_type === 'photo';
    }

    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }
}