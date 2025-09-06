@extends('layouts.app')

@section('title', 'Create New FAQ')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New FAQ</h1>
                    <p class="mt-2 text-gray-600">Add a new frequently asked question to help your customers.</p>
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
                <form action="{{ route('faqs.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Question Field -->
                    <div>
                        <label for="question" class="block text-sm font-semibold text-gray-700 mb-2">
                            Question <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="question" 
                               name="question" 
                               value="{{ old('question') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 @error('question') border-red-500 @enderror"
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
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 resize-vertical @error('answer') border-red-500 @enderror"
                                  placeholder="Enter the detailed answer to this question..."
                                  required>{{ old('answer') }}</textarea>
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
                                <p class="text-gray-600 italic" id="question-preview">Your question will appear here...</p>
                            </div>
                            <div class="answer-preview">
                                <h4 class="font-semibold text-gray-800">Answer:</h4>
                                <p class="text-gray-600 italic whitespace-pre-line" id="answer-preview">Your answer will appear here...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('faqs.index') }}" 
                           class="px-6 py-3 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition duration-200 font-medium">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 shadow-lg">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Create FAQ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Tips for Writing Great FAQs</h3>
                    <ul class="text-blue-700 space-y-1 text-sm">
                        <li>• Keep questions clear and specific</li>
                        <li>• Provide comprehensive but concise answers</li>
                        <li>• Use simple language that customers can understand</li>
                        <li>• Address the most common customer concerns</li>
                        <li>• Include relevant product or service information</li>
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
    answerInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
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
</style>
@endsection
