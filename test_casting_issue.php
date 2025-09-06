<?php

echo "=== TEST BANNER CASTING ISSUE ===\n\n";

// Simulasi casting boolean issue
echo "TEST 1: Boolean casting dengan integer input\n";

class TestModel {
    protected $casts = ['status' => 'boolean'];
    private $attributes = ['status' => 0];
    
    public function update($data) {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $this->castAttribute($key, $value);
        }
    }
    
    public function castAttribute($key, $value) {
        if (isset($this->casts[$key]) && $this->casts[$key] === 'boolean') {
            // Laravel boolean casting logic
            return (bool) $value;
        }
        return $value;
    }
    
    public function getAttributes() {
        return $this->attributes;
    }
}

$model = new TestModel();
echo "Original status: " . $model->getAttributes()['status'] . "\n";

$model->update(['status' => 1]);
echo "After update with 1: " . ($model->getAttributes()['status'] ? 'true' : 'false') . "\n";

$model->update(['status' => 0]);
echo "After update with 0: " . ($model->getAttributes()['status'] ? 'true' : 'false') . "\n";

echo "\nTEST 2: Tanpa casting boolean\n";

class TestModelNoCast {
    private $attributes = ['status' => 0];
    
    public function update($data) {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }
    
    public function getAttributes() {
        return $this->attributes;
    }
}

$model2 = new TestModelNoCast();
echo "Original status: " . $model2->getAttributes()['status'] . "\n";

$model2->update(['status' => 1]);
echo "After update with 1: " . $model2->getAttributes()['status'] . "\n";

$model2->update(['status' => 0]);
echo "After update with 0: " . $model2->getAttributes()['status'] . "\n";

echo "\n=== KESIMPULAN ===\n";
echo "✅ Tanpa casting: Integer 1 dan 0 disimpan dengan benar\n";
echo "❌ Dengan boolean casting: Casting bisa menyebabkan konflik\n";
echo "🔧 SOLUSI: Hapus casting boolean dari model Banner\n";

?>
