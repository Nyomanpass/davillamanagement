<?php
// app/Livewire/Master/CreateEmployee.php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Employee; // Import Model Employee
use Livewire\Attributes\Layout;

class CreateEmployee extends Component
{
    #[Layout('layouts.app')]

    // Properti untuk binding form
    public $nama = '';
    public $alamat = '';
    public $jabatan = '';

    // Rules Validasi
    protected $rules = [
        'nama' => 'required|string|min:3|max:255',
        'alamat' => 'required|string|max:500',
        'jabatan' => 'required|string|max:100',
    ];

    public function saveEmployee()
    {
       
        try {
            $this->validate();
            Employee::create([
                'nama' => $this->nama,
                'alamat' => $this->alamat,
                'jabatan' => $this->jabatan,
            ]);
            session()->flash('success', 'Data Karyawan berhasil ditambahkan!');
            $this->reset();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan data karyawan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.create-employee');
    }
}