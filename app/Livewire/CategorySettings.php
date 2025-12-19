<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Layout;


#[Layout('layouts.app')]
class CategorySettings extends Component
{
    public $name, $type = 'income', $categoryId;
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|min:3',
        'type' => 'required|in:income,expense',
    ];

    public function save()
    {
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            ['name' => $this->name, 'type' => $this->type]
        );

        $this->resetFields();
        session()->flash('message', $this->categoryId ? 'Kategori diperbarui!' : 'Kategori ditambah!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Kategori dihapus!');
    }

    public function resetFields()
    {
        $this->reset(['name', 'type', 'categoryId', 'isEdit']);
    }

    public function render()
    {
        return view('livewire.category-settings', [
            'categories' => Category::orderBy('type')->get()
        ]);
    }
}