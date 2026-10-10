<?php

namespace App\Observers;

use App\Helpers\ActivityHelper;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    public function created(Model $model)
    {
        $namaModel = class_basename($model);
        if ($namaModel !== 'ActivityLog') {
            
            if ($namaModel === 'PengambilanCatering') {
                $model->load('karyawan');
                $employeeNo = $model->karyawan->employee_no ?? 'N/A';
                $namaKaryawan = $model->karyawan->name ?? '';
                
                $deskripsi = "Melakukan scan data user {$employeeNo}";
                if ($namaKaryawan) {
                    $deskripsi .= " ({$namaKaryawan})";
                }

                ActivityHelper::record('Scan QR', $deskripsi);
            } else {
                $detail = $this->getDetailInfo($model);
                ActivityHelper::record('Tambah Data', "Menambahkan data baru pada modul {$namaModel} ({$detail})");
            }
        }
    }

    public function updated(Model $model)
    {
        $namaModel = class_basename($model);
        if ($namaModel !== 'ActivityLog') {
            
            if ($namaModel === 'User') {
                $dirty = $model->getDirty();
                $updates = [];

                if (array_key_exists('departemen', $dirty)) {
                    $updates[] = "akses departemen";
                }
                if (array_key_exists('lokasi', $dirty)) {
                    $updates[] = "akses lokasi";
                }
                if (array_key_exists('role', $dirty)) {
                    $updates[] = "hak akses role";
                }
                if (array_key_exists('password', $dirty)) {
                    $updates[] = "password";
                }
                if (array_key_exists('name', $dirty) || array_key_exists('email', $dirty)) {
                    $updates[] = "informasi profil";
                }

                if (count($updates) > 0) {
                    $aksiDetail = implode(', ', $updates);
                    $aksi = "Memperbarui data {$aksiDetail} user";
                } else {
                    $aksi = "Memperbarui data user";
                }

                ActivityHelper::record('Edit Data', "{$aksi}: {$model->name} ({$model->email})");
            
            } elseif ($namaModel === 'Karyawan') {
                $dirty = $model->getDirty();
                $updates = [];

                if (array_key_exists('schedule', $dirty) || array_key_exists('shift', $dirty)) {
                    $updates[] = "jadwal shift";
                }
                if (array_key_exists('departemen', $dirty) || array_key_exists('dept', $dirty)) {
                    $updates[] = "departemen";
                }
                if (array_key_exists('location', $dirty) || array_key_exists('lokasi', $dirty)) {
                    $updates[] = "lokasi";
                }
                if (array_key_exists('employment_under', $dirty)) {
                    $updates[] = "employment under";
                }
                if (array_key_exists('indonesia_level', $dirty)) {
                    $updates[] = "indonesia level";
                }
                if (array_key_exists('no_hp', $dirty)) {
                    $updates[] = "nomor HP";
                }
                if (array_key_exists('name', $dirty) || array_key_exists('employee_no', $dirty)) {
                    $updates[] = "informasi identitas";
                }

                if (count($updates) > 0) {
                    $aksiDetail = implode(', ', $updates);
                    $aksi = "Memperbarui {$aksiDetail} booking karyawan";
                } else {
                    $aksi = "Memperbarui data booking karyawan";
                }

                $detail = $this->getDetailInfo($model);
                ActivityHelper::record('Edit Data', "{$aksi} ({$detail})");
            
            } else {
                $detail = $this->getDetailInfo($model);
                ActivityHelper::record('Edit Data', "Memperbarui data pada modul {$namaModel} ({$detail})");
            }
        }
    }

    public function deleted(Model $model)
    {
        $namaModel = class_basename($model);
        if ($namaModel !== 'ActivityLog') {
            if ($namaModel === 'User') {
                ActivityHelper::record('Hapus Data', "Menghapus data user: {$model->name} ({$model->email})");
            } elseif ($namaModel === 'Karyawan') {
                $detail = $this->getDetailInfo($model);
                ActivityHelper::record('Hapus Data', "Menghapus data booking karyawan ({$detail})");
            } else {
                $detail = $this->getDetailInfo($model);
                ActivityHelper::record('Hapus Data', "Menghapus data dari modul {$namaModel} ({$detail})");
            }
        }
    }

    private function getDetailInfo(Model $model)
    {
        if (isset($model->name) && isset($model->employee_no)) {
            return "Nama: {$model->name}, Employee No: {$model->employee_no}";
        }
        if (isset($model->name)) {
            return "Nama: {$model->name}";
        } 
        if (isset($model->employee_no)) {
            return "Employee No: {$model->employee_no}";
        } 
        if (isset($model->email)) {
            return "Email: {$model->email}";
        }
        return "ID: " . ($model->id ?? 'N/A');
    }
}