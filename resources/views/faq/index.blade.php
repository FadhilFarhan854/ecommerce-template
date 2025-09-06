@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Find answers to the most common questions about our perfume collection and services.
            </p>
        </div>

        <!-- Admin Actions (only show if user is admin) -->
        @auth
            @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
            <div class="mb-8 text-center">
                <a href="{{ route('faqs.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition duration-300 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New FAQ
                </a>
            </div>
            @endif
        @endauth

        <!-- FAQ Accordion Cards -->
        @if($faqs->count() > 0)
            <div class="space-y-4">
                @foreach($faqs as $index => $faq)
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
                    <!-- Question Card Header -->
                    <div class="faq-question cursor-pointer p-6 hover:bg-gray-50 transition-colors duration-200" 
                         onclick="toggleFAQ({{ $index }})">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 pr-8">
                                {{ $faq->question }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                <!-- Admin Edit/Delete buttons -->
                                @auth
                                    @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                                    <div class="flex items-center space-x-2 mr-4">
                                        <a href="{{ route('faqs.edit', $faq->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-800 p-1 rounded transition-colors"
                                           title="Edit FAQ">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this FAQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 p-1 rounded transition-colors"
                                                    title="Delete FAQ">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                @endauth
                                
                                <!-- Toggle Icon -->
                                <div class="transform transition-transform duration-300" id="icon-{{ $index }}">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Answer Section (Hidden by default) -->
                    <div class="faq-answer hidden border-t border-gray-100" id="answer-{{ $index }}">
                        <div class="p-6 bg-gray-50">
                            <div class="prose prose-gray max-w-none">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-6">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No FAQs Available</h3>
                <p class="text-gray-600 mb-6">There are no frequently asked questions at the moment.</p>
                
                @auth
                    @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                    <a href="{{ route('faqs.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create First FAQ
                    </a>
                    @endif
                @endauth
            </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg z-50" 
             id="success-message">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleFAQ(index) {
    const answer = document.getElementById(`answer-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    
    if (answer.classList.contains('hidden')) {
        // Show answer
        answer.classList.remove('hidden');
        answer.classList.add('animate-fade-in');
        icon.style.transform = 'rotate(180deg)';
    } else {
        // Hide answer
        answer.classList.add('hidden');
        answer.classList.remove('animate-fade-in');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Auto-hide success message after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.remove();
            }, 300);
        }, 5000);
    }
});

// Add CSS animation for smooth fade-in
const style = document.createElement('style');
style.textContent = `
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>

<style>
/* Additional custom styles */
.prose p {
    margin-bottom: 1rem;
}

.faq-question:hover {
    background-color: #f9fafb;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection
