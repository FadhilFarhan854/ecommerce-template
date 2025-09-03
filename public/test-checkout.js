// Test script for checkout auto-calculation
console.log('Testing checkout auto-calculation...');

// Test data
const testCityId = 55; // Bandung
const testCityName = 'Bandung';

// Test auto-calculate function
if (typeof autoCalculateShippingForAddress === 'function') {
    console.log('Testing autoCalculateShippingForAddress...');
    autoCalculateShippingForAddress(testCityId, testCityName);
} else {
    console.error('autoCalculateShippingForAddress function not found');
}

// Test manual calculation
if (typeof testAutoCalculate === 'function') {
    console.log('Testing manual calculation...');
    testAutoCalculate(testCityId);
} else {
    console.error('testAutoCalculate function not found');
}
