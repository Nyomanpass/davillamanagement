<?php
namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use Carbon\Carbon;

#[Layout('layouts.app')]
class HistoryUser extends Component
{
    use WithPagination;
    
    public $perPage = 15;
    public $search = '';
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        // Set default ke bulan dan tahun sekarang
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatedSelectedYear()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::with('user');

        // 1. Filter Berdasarkan Bulan & Tahun (Optimasi)
        $query->whereMonth('created_at', $this->selectedMonth)
              ->whereYear('created_at', $this->selectedYear);

        // 2. Filter Pencarian
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('activity_type', 'like', '%' . $this->search . '%');
            });
        }

        $activities = $query->latest()->paginate($this->perPage);

        // Daftar tahun untuk filter (3 tahun ke belakang)
        $years = range(now()->year, now()->year - 3);

        return view('livewire.master.history-user', [
            'activities' => $activities,
            'years' => $years
        ]);
    }
}