<?php

// Simulasi data yang akan diterima controller saat checkbox tidak dicentang
echo "=== Test 1: Checkbox TIDAK dicentang ===\n";
$request_unchecked = [
    'status' => '0'  // Hanya input hidden yang terkirim
];

$status_unchecked = isset($request_unchecked['status']) && $request_unchecked['status'] ? 1 : 0;
echo "Input yang diterima: " . print_r($request_unchecked, true);
echo "Status hasil: " . $status_unchecked . "\n";
echo "Filled check: " . (isset($request_unchecked['status']) ? 'true' : 'false') . "\n\n";

// Simulasi data yang akan diterima controller saat checkbox dicentang
echo "=== Test 2: Checkbox DICENTANG ===\n";
$request_checked = [
    'status' => '1'  // Input checkbox menimpa input hidden
];

$status_checked = isset($request_checked['status']) && $request_checked['status'] ? 1 : 0;
echo "Input yang diterima: " . print_r($request_checked, true);
echo "Status hasil: " . $status_checked . "\n";
echo "Filled check: " . (isset($request_checked['status']) ? 'true' : 'false') . "\n\n";

// Test menggunakan filled() method seperti di controller
echo "=== Test 3: Menggunakan filled() method ===\n";

// Simulasi request object dengan filled method
class MockRequest {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function filled($key) {
        return isset($this->data[$key]) && $this->data[$key] !== '' && $this->data[$key] !== null;
    }
    
    public function get($key) {
        return $this->data[$key] ?? null;
    }
}

$mock_request_unchecked = new MockRequest(['status' => '0']);
$mock_request_checked = new MockRequest(['status' => '1']);

echo "Unchecked - filled('status'): " . ($mock_request_unchecked->filled('status') ? 'true' : 'false') . "\n";
echo "Checked - filled('status'): " . ($mock_request_checked->filled('status') ? 'true' : 'false') . "\n";

echo "\n=== KESIMPULAN ===\n";
echo "MASALAH DITEMUKAN:\n";
echo "- Ketika checkbox TIDAK dicentang: filled('status') = true (karena ada input hidden dengan value '0')\n";
echo "- Ketika checkbox DICENTANG: filled('status') = true (karena ada input checkbox dengan value '1')\n";
echo "- Controller menggunakan filled('status') yang selalu return true!\n";
echo "- Solusi: Gunakan request->has('status') && request->get('status') == '1'\n";

?>
