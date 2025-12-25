@if ($paginator->hasPages())
    @php
        // Determine active page styling
        $activeClasses = 'bg-gradient-to-r from-blue-600 to-purple-600 text-white border-blue-600';
        $inactiveClasses = 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
        
        // Determine arrow states
        $prevDisabled = $paginator->onFirstPage();
        $nextDisabled = !$paginator->hasMorePages();
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Mobile View --}}
        <div class="flex flex-1 justify-between sm:hidden">
            {{-- Previous Button (Mobile) --}}
            @if ($prevDisabled)
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Sebelumnya
                </a>
            @endif

            {{-- Next Button (Mobile) --}}
            @if ($nextDisabled)
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed ml-3">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2"></i>
                </span>
            @else
                <a href="{{ $paginator->nextPageUrl() }}" 
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors duration-200 ml-3">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2"></i>
                </a>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            {{-- Results Info --}}
            <div>
                <p class="text-sm text-gray-700">
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>

            {{-- Pagination Controls --}}
            <div>
                <div class="flex items-center space-x-1">
                    {{-- First Page Button --}}
                    @if ($paginator->currentPage() > 2)
                        <a href="{{ $paginator->url(1) }}" 
                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                            aria-label="Halaman pertama">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    @endif

                    {{-- Previous Button --}}
                    @if ($prevDisabled)
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" 
                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                            aria-label="Sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @php
                                // Show only limited pages around current page
                                $currentPage = $paginator->currentPage();
                                $lastPage = $paginator->lastPage();
                            @endphp
                            
                            @foreach ($element as $page => $url)
                                @php
                                    // Determine which pages to show
                                    $showPage = false;
                                    if ($page == 1 || $page == $lastPage) {
                                        $showPage = true;
                                    } elseif ($page >= $currentPage - 1 && $page <= $currentPage + 1) {
                                        $showPage = true;
                                    }
                                @endphp
                                
                                @if ($showPage)
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page" 
                                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border {{ $activeClasses }} shadow-sm">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" 
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border {{ $inactiveClasses }} hover:shadow-sm transition-all duration-200">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @elseif ($loop->first || $loop->last)
                                    {{-- Show ellipsis for hidden pages --}}
                                    @if (($page == $currentPage - 2 && $currentPage > 3) || ($page == $currentPage + 2 && $currentPage < $lastPage - 2))
                                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500">
                                            ...
                                        </span>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($nextDisabled)
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->nextPageUrl() }}" 
                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                            aria-label="Selanjutnya">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif

                    {{-- Last Page Button --}}
                    @if ($paginator->currentPage() < $paginator->lastPage() - 1)
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" 
                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                            aria-label="Halaman terakhir">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Page Size Selector (Optional) --}}
    @if (isset($pageSizeOptions) && count($pageSizeOptions) > 1)
        <div class="mt-4 flex items-center justify-end space-x-2 text-sm text-gray-700">
            <span>Items per page:</span>
            <select onchange="window.location.href = updateQueryString('per_page', this.value)" 
                    class="border border-gray-300 rounded-lg px-3 py-1 bg-white">
                @foreach ($pageSizeOptions as $option)
                    <option value="{{ $option }}" {{ $paginator->perPage() == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <script>
            function updateQueryString(key, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(key, value);
                url.searchParams.delete('page'); // Reset to first page
                return url.toString();
            }
        </script>
    @endif
@else
    {{-- No Pagination Needed --}}
    <div class="text-center py-4 text-gray-500 text-sm">
        Menampilkan semua {{ $paginator->total() }} hasil
    </div>
@endif

<style>
    .pagination-item {
        min-width: 2.5rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .pagination-item:hover:not(.disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .pagination-item.active {
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }
    
    .pagination-ellipsis {
        user-select: none;
        pointer-events: none;
    }
</style>

<script>
    // Add smooth scrolling to top when paginating
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Only scroll if not disabled
                if (!this.classList.contains('disabled') && !this.getAttribute('aria-disabled')) {
                    // Smooth scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    
                    // Optional: Show loading indicator
                    const loadingIndicator = document.getElementById('loadingOverlay');
                    if (loadingIndicator) {
                        loadingIndicator.classList.remove('hidden');
                    }
                }
            });
        });
    });
</script>