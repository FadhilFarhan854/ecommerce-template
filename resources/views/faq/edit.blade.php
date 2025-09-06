@extends('layouts.app')

@section('title', 'Edit FAQ')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit FAQ</h1>
                    <p class="mt-2 text-gray-600">Update the frequently asked question and its answer.</p>
                </div>
                <a href="{{ route('faqs.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to FAQs
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-8">
                <form action="{{ route('faqs.update', $faq->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Question Field -->
                    <div>
                        <label for="question" class="block text-sm font-semibold text-gray-700 mb-2">
                            Question <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="question" 
                               name="question" 
                               value="{{ old('question', $faq->question) }}"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 @error('question') border-red-500 @else border-gray-300 @enderror"
                               placeholder="Enter the frequently asked question..."
                               required>
                        @error('question')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Answer Field -->
                    <div>
                        <label for="answer" class="block text-sm font-semibold text-gray-700 mb-2">
                            Answer <span class="text-red-500">*</span>
                        </label>
                        <textarea id="answer" 
                                  name="answer" 
                                  rows="6"
                                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 resize-vertical @error('answer') border-red-500 @else border-gray-300 @enderror"
                                  placeholder="Enter the detailed answer to this question..."
                                  required>{{ old('answer', $faq->answer) }}</textarea>
                        @error('answer')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            You can use line breaks to format your answer. Keep it clear and concise.
                        </p>
                    </div>

                    <!-- Preview Section -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border-2 border-dashed border-gray-200">
                            <div class="question-preview mb-3">
                                <h4 class="font-semibold text-gray-800">Question:</h4>
                                <p class="text-gray-600" id="question-preview">{{ $faq->question }}</p>
                            </div>
                            <div class="answer-preview">
                                <h4 class="font-semibold text-gray-800">Answer:</h4>
                                <p class="text-gray-600 whitespace-pre-line" id="answer-preview">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('faqs.index') }}" 
                               class="px-6 py-3 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition duration-200 font-medium">
                                Cancel
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Delete Button -->
                            <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this FAQ? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 shadow-lg">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </form>

                            <!-- Update Button -->
                            <button type="submit" 
                                    class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 shadow-lg">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update FAQ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change History Card (Optional Enhancement) -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Editing FAQ</h3>
                    <div class="text-yellow-700 text-sm space-y-1">
                        <p><strong>Created:</strong> {{ $faq->created_at ? $faq->created_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</p>
                        @if($faq->updated_at && $faq->updated_at != $faq->created_at)
                            <p><strong>Last Updated:</strong> {{ $faq->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        @endif
                        <p class="mt-2">Make sure your changes improve clarity and provide better information to your customers.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Tips for Editing FAQs</h3>
                    <ul class="text-blue-700 space-y-1 text-sm">
                        <li>• Review for accuracy and up-to-date information</li>
                        <li>• Ensure the answer directly addresses the question</li>
                        <li>• Keep language simple and customer-friendly</li>
                        <li>• Consider if additional details would be helpful</li>
                        <li>• Check for spelling and grammar errors</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const questionInput = document.getElementById('question');
    const answerInput = document.getElementById('answer');
    const questionPreview = document.getElementById('question-preview');
    const answerPreview = document.getElementById('answer-preview');

    // Update question preview
    questionInput.addEventListener('input', function() {
        const value = this.value.trim();
        questionPreview.textContent = value || 'Your question will appear here...';
        questionPreview.classList.toggle('italic', !value);
    });

    // Update answer preview
    answerInput.addEventListener('input', function() {
        const value = this.value.trim();
        answerPreview.textContent = value || 'Your answer will appear here...';
        answerPreview.classList.toggle('italic', !value);
    });

    // Auto-resize textarea
    function autoResize() {
        answerInput.style.height = 'auto';
        answerInput.style.height = (answerInput.scrollHeight) + 'px';
    }
    
    answerInput.addEventListener('input', autoResize);
    
    // Initial resize
    autoResize();
});
</script>

<style>
/* Custom styles for better UX */
.resize-vertical {
    resize: vertical;
}

/* Smooth focus transitions */
input:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Better form styling */
input[type="text"], textarea {
    transition: all 0.2s ease-in-out;
}

input[type="text"]:hover, textarea:hover {
    border-color: #9CA3AF;
}

/* Preview styling */
.question-preview, .answer-preview {
    transition: all 0.2s ease-in-out;
}

/* Better button hover effects */
button:hover, a:hover {
    transform: translateY(-1px);
}

button:active, a:active {
    transform: translateY(0);
}
</style>
@endsection
