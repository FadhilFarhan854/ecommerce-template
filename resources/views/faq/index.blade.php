@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="min-h-screen bg-gray-50">
   

    <!-- Main Content -->
    <div class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Admin Actions (only show if user is admin) -->
            @auth
                @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                <div class="mb-12 text-center">
                    <a href="{{ route('faqs.create') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-green-500 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-green-600 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New FAQ
                    </a>
                </div>
                @endif
            @endauth

            <!-- FAQ Accordion Cards -->
            @if($faqs->count() > 0)
                <div class="space-y-6">
                    @foreach($faqs as $index => $faq)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl group">
                        <!-- Question Card Header -->
                        <div class="faq-question cursor-pointer p-6 lg:p-8 hover:bg-gradient-to-r hover:from-blue-50 hover:to-green-50 transition-all duration-300" 
                             onclick="toggleFAQ({{ $index }})">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg lg:text-xl font-semibold text-gray-900 pr-8 group-hover:text-blue-700 transition-colors duration-300">
                                    {{ $faq->question }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <!-- Admin Edit/Delete buttons -->
                                    @auth
                                        @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                                        <div class="flex items-center space-x-2 mr-4">
                                            <a href="{{ route('faqs.edit', $faq->id) }}" 
                                               class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-lg transition-all duration-200"
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
                                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-all duration-200"
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
                                    <div class="transform transition-transform duration-300 p-2 bg-gradient-to-r from-blue-100 to-green-100 rounded-full" id="icon-{{ $index }}">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Answer Section (Hidden by default) -->
                        <div class="faq-answer hidden border-t border-gradient-to-r from-blue-100 to-green-100" id="answer-{{ $index }}">
                            <div class="p-6 lg:p-8 bg-gradient-to-r from-blue-50/50 to-green-50/50">
                                <div class="prose prose-gray max-w-none">
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line text-lg">{{ $faq->answer }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No FAQs Available</h3>
                    <p class="text-gray-600 mb-8 text-lg max-w-md mx-auto">There are no frequently asked questions at the moment. Check back later for updates.</p>
                    
                    @auth
                        @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                        <a href="{{ route('faqs.create') }}" 
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-green-500 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-green-600 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create First FAQ
                        </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="fixed top-4 right-4 bg-gradient-to-r from-green-500 to-blue-500 text-white px-6 py-4 rounded-xl shadow-xl z-50 transform transition-all duration-300" 
     id="success-message">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
    </div>
</div>
@endif

<script>
function toggleFAQ(index) {
    const answer = document.getElementById(`answer-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    
    if (answer.classList.contains('hidden')) {
        // Show answer
        answer.classList.remove('hidden');
        answer.classList.add('animate-fade-in');
        icon.style.transform = 'rotate(180deg)';
        icon.classList.add('bg-gradient-to-r', 'from-green-100', 'to-blue-100');
    } else {
        // Hide answer
        answer.classList.add('hidden');
        answer.classList.remove('animate-fade-in');
        icon.style.transform = 'rotate(0deg)';
        icon.classList.remove('bg-gradient-to-r', 'from-green-100', 'to-blue-100');
    }
}

// Auto-hide success message after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            successMessage.style.transform = 'translateX(100%)';
            setTimeout(() => {
                successMessage.remove();
            }, 300);
        }, 5000);
    }
});

// Add CSS animation for smooth transitions
const style = document.createElement('style');
style.textContent = `
    .animate-fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Smooth gradient transitions */
    .feature-card {
        transition: all 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
    }
    
    /* Custom scrollbar styling */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: linear-gradient(to bottom, #f3f4f6, #e5e7eb);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #10b981);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #059669);
    }
`;
document.head.appendChild(style);
</script>
@endsection
